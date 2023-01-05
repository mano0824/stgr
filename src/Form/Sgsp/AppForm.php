<?php
namespace App\Form\Sgsp;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

class AppForm extends Form {

    /**
     * constructor.
     */
    public function __construct() {
        $config = Configure::read('STGR');
        $this->config = $config['GOLF'];
    }

    /**
     * 共通のヘッダー情報を取得する
     */
    protected function getRequestHeader() {
		$data = array(
			'RequestHeader' => array(
				'ClientId' => $this->config['ClientId'],
				'GroupCode' => $this->config['GroupCode'],
				'CompanyCode' => $this->config['CompanyCode'],
				'HotelCode' => $this->config['HotelCode'],
				'Key' => $this->config['Key']
            )
		);
        return $data;
    }

    /**
     * APIをコールする
     *
     * @param array $data 送信するデータ
     * @param string $type 呼び元識別子
     * @return array APIレスポンス
     */
    protected function getContents(array $data, $type) {


        $url = $this->config['ServiceURL'].$this->config['urlParams'][$type];
        $ch = curl_init($url);

        $content = json_encode($data);

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json; charset=utf-8",
                "Accept-Charset: UTF-8"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_TIMEOUT => $this->config['TimeOut']
        ]);

        $contents   = curl_exec($ch);
        $info       = curl_getinfo($ch);
        $errorNo    = curl_errno($ch);

        $response = array('Pms_Api'=>$type);
        if ($errorNo !== CURLE_OK) {
            if( $errorNo == CURLE_OPERATION_TIMEDOUT) {
                throw new Exception(__('{"result_code":"12002",
                                         "Pms_Api":"'.$type.'",
                                         "message":"timeout"}'));
            } else {
                throw new Exception(__('{"result_code":"12001",
                                         "Pms_Api":"'.$type.'",
                                         "message":"不明なエラー: errorNo=' . $errorNo . '"}'));
            }
            return $response;
        }

        if ($info['http_code'] !== 200) {
            throw new Exception(__('{"result_code":"12101",
                                     "Pms_Api":"'.$type.'",
                                     "message":" http_code:' . $info['http_code'] . '"}'));
        }

        $obj = json_decode($contents, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response += $obj;
        } else {
            Log::write("debug",$contents);
            throw new Exception(__('{"result_code":"12101",
                                     "Pms_Api":"'.$type.'",
                                     "message":"' . json_last_error_msg() . '"}'));
        }

        return $response;
    }
}
?>
