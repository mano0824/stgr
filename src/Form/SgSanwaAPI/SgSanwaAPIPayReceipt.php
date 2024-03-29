<?php
namespace App\Form\SgSanwaAPI;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgSanwaAPIPayReceipt Form
 */
class SgSanwaAPIPayReceipt extends AppSanwaForm {
    private $type = "PayReceipt";

    protected function _buildSchema(Schema $schema) {
        return $schema
                    ->addField('MachineNo',     ['type' => 'integer'])
                    ->addField('CallNo',        ['type' => 'integer'])
                    ;
    }
    
    // バリデーション後に実行する処理
    protected function _execute(array $data= array()) {
        try {
            $config = Configure::read("API_OPERATION");
            $params = array(
                'MachineNo' => $data['MachineNo'],
                'CallNo'    => $data['CallNo'],
                'BillNo'    => $data['HostBillNo']
            );

            if($config['DEBUG']){
                Log::write("debug",$params);
            }

            if($config['FIXED_RESPONSE']){
                // 固定応答モード
                $response = Configure::read('SgSanwaAPI_Pay_Receipt_Res');
            }else{
                $response = $this->getContents($params, $this->type);
            }


            // 項目生成関数
            function createResponseItem($response, $individual, $detail, $receiptkbn, $detailKbn, $rowNo) {
                if ($detail['RowNo'] != 1) {
                    return [
                        'ReceiptKbn' => $receiptkbn,
                        'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                        'RowNo'      => (int)$rowNo,
                        'TrkNo'      => ($detailKbn == 'J') ? 0 : (int)$individual['HolderNo'], // 軽減税率がTrkNoの固定値0以外の時は項目が出ない
                        'DetailKbn'  => $detailKbn,
                        'ShohinName' => (string)$detail['ItemName'] ?? '',
                        'Tanka'      => ($detail['Tanka'] != 0) ? (int)$detail['Tanka'] : 0,
                        'Suryo'      => ($detail['Suryo'] != 0) ? sprintf('%06.2f', $detail['Suryo']) : sprintf('%06.2f', 0),
                        'Kingaku'    => (int)$detail['Kingaku'],
                        'Comment1'   => '',
                        'Comment2'   => '',
                        'Comment3'   => '',
                        'Comment4'   => '',
                        'Comment5'   => '',
                        'Comment6'   => '',
                        'Comment7'   => '',
                        'Comment8'   => '',
                    ];
                } else {
                    return [
                        'ReceiptKbn' => $receiptkbn,
                        'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                        'RowNo'      => (int)$rowNo,
                        'TrkNo'      => ($detailKbn === 'J') ? 0 : (int)$individual['HolderNo'], // 軽減税率がTrkNoの固定値0以外の時は項目が出ない
                        'DetailKbn'  => $detailKbn,
                        'ShohinName' => (string)$detail['ItemName'],
                        'Tanka'      => (int)$detail['Tanka'],
                        'Suryo'      => sprintf('%06.2f', $detail['Suryo']),
                        'Kingaku'    => (int)$detail['Kingaku'],
                        'Comment1'   => '',
                        'Comment2'   => '',
                        'Comment3'   => '',
                        'Comment4'   => '',
                        'Comment5'   => '',
                        'Comment6'   => '',
                        'Comment7'   => '',
                        'Comment8'   => '',
                    ];
                }
            }

            // 税項目追加関数
            function addTaxItems(&$responseConvert, $response, $single = true) {
                if ($single) {
                    $taxItems = [
                        ['C', '内消費税合計', $response['ResultInformation']['Tax'] + $response['ResultInformation']['TaxKeigen']],
                        ['D', '（10%消費税分）', $response['ResultInformation']['Tax']],
                        ['E', '（8%消費税分）', $response['ResultInformation']['TaxKeigen']]
                    ];
                } else {
                    $taxItems = [
                        ['C', '内消費税合計', $response['ResultInformation']['Tax'] + $response['ResultInformation']['TaxKeigen']],
                        ['D', '（10%消費税分）', $response['ResultInformation']['Tax']],
                        ['E', '（8%消費税分）', $response['ResultInformation']['TaxKeigen']],
                        ['N', '（10%対象合計）', $response['ResultInformation']['TaishoKingaku']],
                        ['N', '（10%消費税分）', $response['ResultInformation']['Tax']],
                        ['N', '（8%対象合計）', $response['ResultInformation']['TaishoKingakuKeigen']],
                        ['N', '（8%消費税分）', $response['ResultInformation']['TaxKeigen']]
                    ];
                }
                
                $rowNo = 1;
                foreach ($taxItems as $item) {
                    if ($single) {
                        $responseConvert['ListTax'][] = [
                            'ReceiptKbn' => 0,
                            'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                            'RowNo'      => 0,
                            'TrkNo'      => 0,
                            'DetailKbn'  => $item[0],
                            'ShohinName' => $item[1],
                            'Tanka'      => 0,
                            'Suryo'      => sprintf('%06.2f', 0),
                            'Kingaku'    => (int)$item[2],
                            'Comment1'   => '',
                            'Comment2'   => '',
                            'Comment3'   => '',
                            'Comment4'   => '',
                            'Comment5'   => '',
                            'Comment6'   => '',
                            'Comment7'   => '',
                            'Comment8'   => '',
                        ];
                    } else {
                        $responseConvert['ListTax'][] = [
                            'ReceiptKbn' => 0,
                            'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                            'RowNo'      => ($item[0] == 'N') ? (int)$rowNo++ : 0,
                            'TrkNo'      => 0,
                            'DetailKbn'  => ($item[0] == 'N') ? 'N' : $item[0],
                            'ShohinName' => $item[1],
                            'Tanka'      => 0,
                            'Suryo'      => sprintf('%06.2f', 0),
                            'Kingaku'    => (int)$item[2],
                            'Comment1'   => '',
                            'Comment2'   => '',
                            'Comment3'   => '',
                            'Comment4'   => '',
                            'Comment5'   => '',
                            'Comment6'   => '',
                            'Comment7'   => '',
                            'Comment8'   => '',
                        ];
                    }
                }
            }
            
            // コメント追加関数
            function addComments($individual, &$responseItem) {
                $comments = [];
                foreach ($individual['Comment'] as $index => $comment) {
                    $commentText = $comment[0] ?? '';
                    if ($index < 8) {
                        $responseItem['Comment' . ($index + 1)] = $commentText;
                    }
                    $comments['Comment' . ($index + 1)] = $commentText;
                }
                return $comments;
            }

            // コメント判定用関数
            function isCommentsEmpty($comments) {
                foreach ($comments as $comment) {
                    if (!empty($comment)) {
                        return false;
                    }
                }
                return true;
            }

            // 項目名変換
            $responseConvert = [
                'SgSanwa_Api' => $response['SgSanwa_Api'],
                'MachineNo'   => (int)$response['ResultInformation']['MachineNo'],
                'CallNo'      => (int)$response['ResultInformation']['CallNo'],
                'ListTax'     => []
            ];
            $mergeDatas = [];
            
            $comments = []; // ログ出力用
            $RowNo = 0;
            
            // 人数分回る
            foreach ($response['ResultInformation']['IndividualArray'] as $index => $individual) {
                $datas = [];
                $kbnO = []; // 非明細データ(O)を一時保持
                $rowNo = 1;
                $humansCont = COUNT($response['ResultInformation']['IndividualArray'], COUNT_NORMAL);

                // 明細分回る
                foreach ($individual['DetailArray'] as $index => $detail) {
                    $receiptkbn = 0;
                    $detailKbn = ''; // データ区分分け
                    switch ((string)$detail['DataType']) {
                        case 'N':
                            $detailKbn = 'N'; // 明細データ
                            break;
                        case 'O':
                            $detailKbn = 'O'; // 非明細データ(Nの最後に追加)
                            break;
                        case 'T':
                            $detailKbn = 'T'; // 明細合計金額データ
                            break;
                        default:
                            $detailKbn = '';
                            Log::write("error", "データ区分エラー　現在の設定値: (" . $detail['DataType'] . ") [N, O, T]のいずれかに設定してください。");
                            break;
                    }

                    // 項目生成
                    $responseItem = createResponseItem($response, $individual, $detail, $receiptkbn, $detailKbn, $rowNo);
                    // コメントを追加
                    $comments[$index] = addComments($individual, $responseItem);

                    // 区分「N」の場合
                    if ($detailKbn == 'N' || $detailKbn == '') {
                        $responseItem['DetailKbn'] == 'N';
                        // 複数人だった場合
                        if ($humansCont != 1) {
                            $responseItem['ReceiptKbn'] = 1;
                        }
                        $datas[] = $responseItem;
                        $rowNo++;

                    } elseif ($detailKbn == 'O') {
                        // 区分「O」の場合は一時保持
                        $responseItem['DetailKbn'] == 'N';
                        $kbnO[] = $responseItem;

                    } elseif($detailKbn == 'T') {
                        // 1人だった場合
                        if ($humansCont == 1) {
                            for ($i = 0; $i < 2; $i++) {
                                if ($i == 1) {
                                    // 項目生成
                                    $responseItem = createResponseItem($response, $individual, $detail, $receiptkbn, $detailKbn, $rowNo);
                                    // コメントを追加
                                    $comments[$index] = addComments($individual, $responseItem);
                                }

                                $responseItem['DetailKbn'] = ($i == 0) ? 'T' : 'R';
                                $responseItem['RowNo'] = 0;
                                $responseItem['ShohinName'] = ($i == 0) ? $responseItem['ShohinName'] : '領収金額';
                                $responseItem['TrkNo'] = 0;

                                // 1人だった場合
                                $responseItem['Kingaku'] = ($i == 0) ? $responseItem['Kingaku'] : $response['ResultInformation']['RyoshuKingaku'];
                                // コメントを追加
                                $comments[$index] = addComments($individual, $responseItem);

                                $datas[] = $responseItem;
                            }
                        } else {
                            $responseItem['ReceiptKbn'] = 1;
                            // 最後以外の処理
                            if ($individual != end($response['ResultInformation']['IndividualArray'])) {
                                // コメントを追加
                                $comments[$index] = addComments($individual, $responseItem);
                                $datas[] = $responseItem;
                            }
                            // 最後だけ処理
                            if ($individual == end($response['ResultInformation']['IndividualArray'])) {
                                for ($i = 0; $i < 3; $i++) {
                                    //  そのままのデータ
                                    if ($i == 0) {
                                        // コメントを追加
                                        $comments[$index] = addComments($individual, $responseItem);
                                        $datas[] = $responseItem;

                                    } else if($i == 1) {// 人数合計値Tを生成
                                        $responseItem = [
                                            'ReceiptKbn' => 0,
                                            'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                                            'RowNo'      => 0,
                                            'TrkNo'      => 0, // 軽減税率がTrkNoの固定値0以外の時は項目が出ない
                                            'DetailKbn'  => 'T',
                                            'ShohinName' => $responseItem['ShohinName'],
                                            'Tanka'      => '',
                                            'Suryo'      => '',
                                            'Kingaku'    => (int)$response['ResultInformation']['RyoshuKingaku'] ,
                                            'Comment1'   => '',
                                            'Comment2'   => '',
                                            'Comment3'   => '',
                                            'Comment4'   => '',
                                            'Comment5'   => '',
                                            'Comment6'   => '',
                                            'Comment7'   => '',
                                            'Comment8'   => '',
                                        ];
                                        $datas[] = $responseItem;
                                    } else {// 領収書Rを生成
                                        $responseItem = [
                                            'ReceiptKbn' => 0,
                                            'ReceiptNo'  => (int)$response['ResultInformation']['PrintNo'],
                                            'RowNo'      => 0,
                                            'TrkNo'      => 0, // 軽減税率がTrkNoの固定値0以外の時は項目が出ない
                                            'DetailKbn'  => 'R',
                                            'ShohinName' => '領収金額',
                                            'Tanka'      => '',
                                            'Suryo'      => '',
                                            'Kingaku'    => (int)$response['ResultInformation']['RyoshuKingaku'] ,
                                            'Comment1'   => '',
                                            'Comment2'   => '',
                                            'Comment3'   => '',
                                            'Comment4'   => '',
                                            'Comment5'   => '',
                                            'Comment6'   => '',
                                            'Comment7'   => '',
                                            'Comment8'   => '',
                                        ];
                                        $datas[] = $responseItem;
                                    }  
                                }
                            }
                        }
                    }

                    // 最後だけ処理
                    if ($detail == end($individual['DetailArray'])) {
                        // 区分「O」のデータをNの最後に追加
                        foreach ($kbnO as $index => $item) {
                            $item['DetailKbn'] = 'N';
                            $item['TrkNo'] = $individual['HolderNo'];
                            $item['RowNo'] = $rowNo++;
                            // 複数人だった場合個人明細に出力
                            if ($humansCont != 1) {
                                $item['ReceiptKbn'] = 1;
                            }
                            $kbnO[$index] = $item;
                        }
                    }
                }
                $datas = array_merge($datas, $kbnO);
                $mergeDatas = array_merge($mergeDatas, $datas);
                $RowNo = $rowNo;
            }
            
            $responseConvert['ListTax'] += $mergeDatas;

            if ($humansCont == 1) {
                addTaxItems($responseConvert, $response);
            } else {
                $single = false;
                addTaxItems($responseConvert, $response, $single);
            }

            $responseConvert['Message'] = $response['Message'];

            if($config['DEBUG']){
                Log::write("debug",$responseConvert,);
                if (!isCommentsEmpty($comments)) Log::write("debug", $comments);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $responseConvert;
    }
}
