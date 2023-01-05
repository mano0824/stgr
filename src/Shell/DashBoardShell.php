<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Shell;

use Cake\Core\Configure;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Log\Log;
//use Psy\Shell as PsyShell;

/**
 * Simple console wrapper around Psy\Shell.
 */
class DashBoardShell extends Shell
{
    public $config = array();

    public function initialize()
    {
        parent::initialize();
        $this->config = Configure::read('SHK');
    }

    /**
     * Start the shell and interactive console.
     *
     * @return int|null
     */
    public function main()
    {
        $this->initialize();
        $this->out(' == START '.__CLASS__.'.'.__FUNCTION__.'()');
        $this->out(' == FINISH '.__CLASS__.'.'.__FUNCTION__.'() = 0');
    }

    /**
     * Http（POST）でjson通信を行います
     *
     * @author rsc 2019
     * @param string inUrl: （必須）リクエスト先URL
     * @param array inReqHead （必須）リクエストヘッダ
     * @param array inReqData （必須）リクエストパラメータ
     * @return array json_decode(httpレスポンス）
     *  null以外：正常終了
     *  null : エラー
     */
    public function httpPostRequest(string $inUrl, $inReqHead, $inReqData)
    {
        if($this->config['QRPAY']['Debug']){
            $this->out('  - START '.__CLASS__.'.'.__FUNCTION__.'()');
        }

        $retCd = 0;
        $resHeader = null;
        $resData = null;
        $exp = null;

        try{
            if(empty($inUrl)){
        		throw new \Exception('inUrl is empty: ', 201);
            }
            if(empty($inReqHead) || !is_array($inReqHead) || count($inReqHead) <= 0){
        		throw new \Exception('inReqHead is empty: ', 202);
            }
            if(empty($inReqData) || !is_array($inReqData) || count($inReqData) <= 0){
        		throw new \Exception('inReqData is empty: ', 203);
            }

            if($this->config['QRPAY']['Debug']){
                $this->out('#### httpPostJsonRequest ####'.PHP_EOL
                .'  inUrl='.$inUrl.PHP_EOL
                .'  inReqHead='.PHP_EOL
                .print_r($inReqHead, true).PHP_EOL
                .'  inReqData='.PHP_EOL
                .print_r($inReqData, true).PHP_EOL);
            }

            // 送信データを作成
            $inReqData['mt'] = microtime(true);
            $httpCode = null;
            try{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $inUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // post
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //Locationをたどる
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //最大何回リダイレクトをたどるか
                curl_setopt($ch, CURLOPT_AUTOREFERER,true); //リダイレクトの際にヘッダのRefererを自動的に追加させる
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inReqData)); // jsonデータを送信
                curl_setopt($ch, CURLOPT_HTTPHEADER, $inReqHead); // リクエストにヘッダーを含める
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);

                $response = curl_exec($ch);

                $resHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                $curlErrNo = curl_errno($ch);
                $curlError = curl_error($ch);

                if($this->config['QRPAY']['Debug']){
                    $this->out('#### curl exec ####'.PHP_EOL
                        .'  HeaderSize='.$resHeaderSize.PHP_EOL
                        .'  HttpCode='.$httpCode.PHP_EOL
                        .'  ErrNo='.$curlErrNo.PHP_EOL
                        .'  Error='.$curlError.PHP_EOL);
                }
                curl_close($ch);
                if(CURLE_OK !== $curlErrNo){
            		throw new \Exception($curlError, $curlErrNo);
                }

                $resHeader = substr($response, 0, $resHeaderSize);
                $resBody = substr($response, $resHeaderSize);
                $resData = json_decode($this->removeBom($resBody), true);

                if($this->config['QRPAY']['Debug']){
                    $this->out('#### curl response ####'.PHP_EOL
                        .'  responseHeader='.PHP_EOL
                        .print_r($resHeader, true).PHP_EOL
                        .'  responseData='.PHP_EOL
                        .print_r($resData, true).PHP_EOL);
                }
            } catch(\Exception $e) {
        		throw new \Exception('Curl error: ', 801, $e);
			}
			if(empty($resHeader)){
        		throw new \Exception('Curl Response header is empty: ', 802);
			}
			if(empty($resData)){
        		throw new \Exception('Curl Response body is empty: header='.$resHeader, 803);
			}
//			if($httpCode != 200){
//        		throw new \Exception('Http status error: status='.$httpCode , 804);
//			}

		} catch(\Exception $e) {
			$retCd = $e->getCode();
			if($retCd > 0){
		        $exp = $e;
			}else{
			    $retCd = 999;
		        $exp = new \Exception('Unexpected exception: ', $retCd, $e);
			}
		}

        if($this->config['QRPAY']['Debug']){
            $this->out('  - FINISH '.__CLASS__.'.'.__FUNCTION__.'() = '.$retCd);
        }
        return array('retCd'=>$retCd, 'resHeader'=>$resHeader, 'resData'=>$resData, 'exception'=>$exp);
    }

    /**
     * 文字列先頭のBOMを除去する
     *
     * @author rsc 2019
     * @param string str （必須）文字列
     * @return string BOMを除去した文字列
     */
    public function removeBom(string $str)
    {
        if (($str == NULL) || (mb_strlen($str) == 0)) {
             return $str;
        }
        if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf){
             $str = substr($str, 3);
        }
        return $str;
    }

}
