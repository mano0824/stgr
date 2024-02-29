<?php
namespace App\Form\SgSanwaAPI;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

class AppSanwaForm extends Form {

    /**
     * constructor.
     */
    public function __construct() {
        $config = Configure::read('STGR');
        $this->config = $config['SanwaAPI'];
        // $this->config = $config['TestSanwaAPI'];
    }

    /**
     * APIをコールする
     *
     * @param array $data 送信するデータ
     * @param string $type 呼び元識別子
     * @return array APIレスポンス
     */
    protected function getContents(array $data, $type) {
        $sanwaFlg = false;
        if ($sanwaFlg) {
            $data['MachineNo'] = 80;
            $data['HolderNo'] = [105];
        }

        $url = $this->config['ServiceURL'].$this->config['urlParams'][$type];
        $ch = curl_init($url);

        $content = json_encode($data);

        $header = array(
                    "Content-type: application/json",
                    "Accept-Charset: UTF-8",
                    "X-Api-Key: " . $this->config['X-Api-Key'],
                    "X-Manufacturer-Key: " . $this->config['X-Manufacturer-Key']
                );

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_TIMEOUT => $this->config['TimeOut']
        ]);

        $contents   = curl_exec($ch);
        $info       = curl_getinfo($ch);
        $errorNo    = curl_errno($ch);
        
        $response = array('SgSanwa_Api'=>$type);

        if ($errorNo !== CURLE_OK) {
            $typeNum = $this->getProcessNumber($type);
            $endNum = $this->getErrorNumber();
            $errCode = $typeNum . $endNum;
            if( $errorNo == CURLE_OPERATION_TIMEDOUT || $errorNo == CURLE_OPERATION_TIMEOUTED) {
                throw new Exception(__('{"result_code":"61'.$errCode.'",
                                        "SgSanwa_Api":"'.$type.'",
                                        "message":"受信タイムアウト"}'));
            } elseif ($errorNo == CURLE_COULDNT_RESOLVE_HOST || $errorNo == CURLE_COULDNT_CONNECT || $errorNo == CURLE_SEND_ERROR || $errorNo == CURLE_RECV_ERROR) {
                throw new Exception(__('{"result_code":"60'.$errCode.'",
                                        "SgSanwa_Api":"'.$type.'",
                                        "message":"ネットワーク接続異常"}'));
            } elseif ($errorNo == CURLE_GOT_NOTHING) {
                throw new Exception(__('{"result_code":"60'.$errCode.'",
                                        "SgSanwa_Api":"'.$type.'",
                                        "message":"サーバー接続異常"}'));
            } else {
                throw new Exception(__('{"result_code":"12001",
                                         "SgSanwa_Api":"'.$type.'",
                                         "message":"不明なエラー: errorNo=' . $errorNo . '"}'));
            }
            return $response;
        }

        if ($info['http_code'] !== 200) {
            $typeNum = $this->getProcessNumber($type);
            $endNum = $this->getErrorNumber();
            
            $this->handleHttpErrorCheck($info['http_code']);
            $errCodes = [400 => '01', 500 => '02', 503 => '03'];
            foreach ($errCodes as $key => $code) {
                if ($info['http_code'] == $key) {
                    $endNum = $code;
                    break;
                }
                $endNum = '99';
            }
            $errCode = $typeNum . $endNum;

            throw new Exception(__('{"result_code":"62'.$errCode.'",
                                    "SgSanwa_Api":"'.$type.'",
                                    "message":" http_code:' . $info['http_code'] . '"}'));
        }

        $obj = json_decode($contents, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response += $obj;
            $response['Message'] = '000000';
        } else {
            throw new Exception(__('{"result_code":"12101",
                                     "SgSanwa_Api":"'.$type.'",
                                     "message":"' . json_last_error_msg() . '"}'));
        }

        // エラーチェックとエラー番号振り分け
        $response = $this->handleErrorSelect($errorNo, $response, $type);

        return $response;
    }

    /**
     * エラーハンドリングセレクト
     * @param int $errorNo CURLエラーナンバー
     * @param array $response 結果
     * @param string $type 精算タイプ情報
     * @param int|null $uniqueCode 固有のエラーコードを設定
     */
    protected function handleErrorSelect($errorNo, $response, $type, $uniqueCode = null)
    {
        switch($errorNo) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_SSL_CONNECT_ERROR:
                $kind = 'connectError';
                break;
            case CURLE_OPERATION_TIMEOUTED:
                $kind = 'timeOut';
                break;
            case CURLE_HTTP_RETURNED_ERROR:
            case CURLE_HTTP_POST_ERROR:
                $kind = 'httpStatus';
                break;
            default:
                $kind = 'unknown';
                break;
        }

        if ($kind == 'unknown') {
            if ($response['ReturnCode'] === 0 || $response['ReturnCode'] === 1) {
                if ($type != 'PayReceipt' && $type != 'PayReset') {
                    $kind = $this->statusErrorKind($response, $type);
                }
            } else {
                $kind = 'abnormalReturnCode';
            }
            if ($errorNo == CURLE_OK && $kind == 'unknown') {
                $response['Message'] = '000000';
                return $response;
            }
        }

        $response = $this->handleError($response, $type, $kind, $uniqueCode);
        return $response;
    }

    /**
     * エラーハンドリング
     * @param array $errMsg 結果
     * @param string $errKind エラー状態
     * @param string $type 精算タイプ情報
     * @param int|null $uniqueCode 固有のエラーコードを設定
     * @return 結果
     */
    private function handleError($response, $type, $errKind, $uniqueCode = null)
    {
        if (empty($response['ResultInformation'])) {
            return $response;
        }
        // uniqueCodeが設定されている場合はそれを使用し、そうでなければ通常のエラーコードを生成
        $response = $uniqueCode ?? $this->assembleErrorCode(
            $this->getErrorCategoryCode($errKind),
            $this->getProcessNumber($type),
            $this->getErrorNumber(),
            $response,
            $type
        );

        if (strlen($response['Message']) != 6) {
            Log::write("error", "Failed to generate error message: " . __METHOD__ . ": (" . str_replace(__NAMESPACE__ . '\\', '', get_class()) . ")");
            return;
        }

        return $response;
    }

    /**
     * エラーコード組み立て
     * @param string $errorCategoryCode エラー分類
     * @param string $processNumber 処理番号
     * @param string $getErrorNumber エラー番号
     * @return 結果
     */
    private function assembleErrorCode($errorCategoryCode, $processNumber, $errorNumber, $response, $type)
    {
        $response['Message'] = "6{$errorCategoryCode}{$processNumber}{$errorNumber}";
        $response = $this->statusErrorCheck($response, $type);
        $status = 0;
        if ($type == 'Pay') {
            if ($response['ResultInformation']['Status'] != 0) {
                $status = $response['ResultInformation']['Status'];
                $response['Message'] = "6{$errorCategoryCode}{$processNumber}{$status}";
            }
        } else {
            if ($type != 'PayReceipt') {
                foreach ((array)$response['ResultInformation']['HolderNoArray'] as $res) {
                    if ($res['Status'] != 0) {
                        $status = $res['Status'];
                        $response['Message'] = "6{$errorCategoryCode}{$processNumber}{$status}";
                        
                        break;
                    }
                }
            }
        }

        return $response;
    }

    /**
     * エラー分類
     * @param String $errKind 種類
     * @return エラー分類のコード
     */
    private function getErrorCategoryCode($errKind)
    {
        $errorNames = [
            'connectError'       => '0', // 接続異常
            'timeOut'            => '1', // 受信異常（タイムアウト等）
            'httpStatus'         => '2', // HTTPステータスコード
            'abnormalReturnCode' => '3', // リターンコード異常
            'abnormalStatus'     => '4', // 異常ステータス
        ];

        return $errorNames[$errKind] ?? '9'; // その他
    }

    /**
     * 処理番号
     * @param String $type 精算タイプ情報
     * @return エラー分類のコード
     */
    private function getProcessNumber($type)
    {
        $processNames = [
            'PayCheck'      => '01',
            'PointPayCheck' => '01',
            'Pay'           => '02',
            'PayReset'      => '03',
            'PayReceipt'    => '04',
            // チェックインも追加する
        ];

        return $processNames[$type] ?? '99';
    }

    /**
     * エラー番号を取得（連番）
     * @return 2桁の数字
     */
    private function getErrorNumber()
    {
        static $errNumber = 1;

        // エラー番号を2桁でフォーマット
        $errCode = str_pad($errNumber++, 2, '0', STR_PAD_LEFT);

        // エラー番号が99を超えたらリセット
        if ($errNumber > 99) {
            $errNumber = 1;
        }

        return $errCode;
    }

    /**
     * ステータスエラーチェック（変換）
     * @param array $response 結果
     * @param string $type 精算タイプ情報
     * @return
     */
    private function statusErrorCheck($response, $type)
    {
        if ($type != 'PayReceipt' && $type != 'PayReset') {
            if ($type == 'Pay') {
                $status = array_column($response, 'Status');
            } else {
                $status = array_column($response['ResultInformation']['HolderNoArray'], 'Status');
            }

            if (is_array($status)) {
                foreach ($status as $key => $s) {
                    if ($type == 'Pay') {
                        $response['ResultInformation']['Status'] = $this->statusConvert($s, $type);
                    } else {
                        $response['ResultInformation']['HolderNoArray'][$key]['Status'] = $this->statusConvert($s, $type);
                    }
                    if ($s != 0) {
                        break;
                    }
                }
            } else {
                if ($type == 'Pay') {
                    $response['ResultInformation']['Status'] = $this->statusConvert($status, $type);
                } else {
                    foreach ($response['ResultInformation']['HolderNoArray'] as $detail) {
                        $detail['Status'] = $this->statusConvert($status, $type);
                    }
                }
            }
        }

        return $response;
    }

        /**
     * ステータスエラーチェック（種類）
     * @param array $response 結果
     * @param string $type 精算タイプ情報
     * @return
     */
    private function statusErrorKind($response, $type)
    {
        if ($type != 'PayReceipt' && $type != 'PayReset') {
            if ($type == 'Pay') {
                $status = array_column($response, 'Status');
            } else {
                $status = array_column($response['ResultInformation']['HolderNoArray'], 'Status');
            }
            $errKind = 'unknown';
            if (is_array($status)) {
                foreach ($status as $s) {
                    if ($s != 0) {
                        $errKind = 'abnormalStatus';
                        break;
                    }
                }
            } else {
                if ($status < 0 || $status > 20) {
                    $errKind = 'abnormalStatus';
                }
            }
        }

        return $errKind;
    }

    /**
     * HTTPエラー
     * @param int $httpCode HTTPステータスコード
     */
    private function handleHttpErrorCheck($httpCode)
    {
        $httpStatus = $httpCode;

        $messages = [
            300 => ": リダイレクトが発生しています。",
            400 => ": クライアントが無効なリクエストを発行しました。",
            500 => ": リクエストを処理中にサーバーで内部エラーが発生しました。",
            503 => ": サーバーが一時的にビジーで、リクエストを処理できませんでした。",
        ];

        // 300番台(リダイレクション)
        if ($httpStatus >= 300 && $httpStatus < 400) {
            Log::write("error", "HTTPステータスコード " . $httpStatus . $messages[300]);

        // 400番台(クライアントエラー)
        } elseif ($httpStatus >= 400 && $httpStatus < 500) {
            if ($httpStatus != 400) {
                Log::write("error", "HTTPステータスコード " . $httpStatus . ": クライアントエラーが発生しました。");
            }
            // 400
            if (isset($messages[$httpStatus])) {
                Log::write("error", "HTTPステータスコード " . $httpStatus . $messages[$httpStatus]);
            }

            // 500番台(サーバーエラー)
        } elseif ($httpStatus >= 500 && $httpStatus < 600) {
            if ($httpStatus != 500 && $httpStatus != 503) {
                Log::write("error", $httpStatus . ": サーバーエラーが発生しました。");
            }
            // 500, 503
            if (isset($messages[$httpStatus])) {
                Log::write("error", "HTTPステータスコード " . $httpStatus . $messages[$httpStatus]);
            }
        }
    }

    /**
     * ステータス変換
     * @param int $status ステータス
     * @param string $type タイプ
     * @return string 変換後ステータス
     */
    private function statusConvert(int $status, string $type): string
    {
        if ($status == 0) {
            return $status;
        }
        $statsConv = [
            '1' => '09',
            '2' => '10',
            '3' => '11',
            '4' => '12',
            '5' => '13',
            '6' => '14', '7' => '14', '8' => '14', '10' => '14',
            '11' => '15', '12' => '15', '13' => '15', '14' => '15', '16' => '15',
            '17' => '16',
            '18' => '17',
            '19' => '18',
        ];

        if ($type == 'Pay') {
            $statsConv += ['9' => '56', '15' => '56', '20' => '56'];
        } else {
            $statsConv += ['9' => '14', '15' => '56', '20' => '56'];
        }

        return $statsConv[$status] ?? '99';
    }
}
?>
