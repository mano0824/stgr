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
use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Simple console wrapper around Psy\Shell.
 */
class CheckQrOrdersShell extends DashBoardShell
{
    use ModelAwareTrait;

    public $baseUrl = 'localhost';

    public function initialize()
    {
        parent::initialize();

        $this->paymentMethods = TableRegistry::get('T_m_payment_method');
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
        $this->out(print_r($this->args, true));

        if(isset($this->args[0])){
            $this->baseUrl = $this->args[0];
        }

        $retCd = 0;
        $cntRec = 0;
        $cntRefund = 0;
        $cntRetry = 0;
        $cntFault = 0;
        $cntFinish = 0;
        $cntError = 0;

        try {
            $this->loadModel('TINgCodeTrade');
            $iNgCodeTrade = null;

            $now = date('Y/m/d H:i:s');
            $iNgCodeTrades = $this->TINgCodeTrade->find('all',[
                'conditions'=> [
                    'Update_Date <' => date('Y/m/d H:i:s', strtotime('-'.$this->config['QRPAY']['CheckQrOrders']['WaitingTime'].' second')),
                    'Status_ID' => 0,
                ],
            ]);

            foreach($iNgCodeTrades as $iNgCodeTrade){
                $cntRec++;
                try{
                    $ret = $this->httpPostRequest(
                        $this->baseUrl . Router::url(['controller' => 'Qrpays', 'action' => 'checkorder']),
                        ['Content-Type:application/json'],
                        ['storeOrderId'=> $iNgCodeTrade['Store_Order_ID']]
                    );
                    if($this->config['QRPAY']['Debug']){
                        $this->out(' - httpPostRequest response = '.PHP_EOL.print_r($ret, true));
                    }
                    if($ret['retCd'] == 0 && $ret['resData'] && is_array($ret['resData'])){
                        if($ret['resData']['http_status'] != 200){
                            // エラー  --> 5分後に再チェック
                            throw new \Exception(' qrpays.checkorder error (http_status='.$ret['resData']['http_status'].')', 810, $ret['exception']);
                        }
                        if($ret['resData']['returnCode'] == 'MP10000' && $ret['resData']['result'] && is_array($ret['resData']['result'])){
                            switch($ret['resData']['result']['result_code']){
                                case 'PAY_SUCCESS':     // 支払成功 --> 返金処理を行う
                                    $ret2 = $this->httpPostRequest(
                                        $this->baseUrl . Router::url(['controller' => 'Qrpays', 'action' => 'refunds']),
                                        ['Content-Type:application/json'],
                                        [
                                            'order_id'=> $iNgCodeTrade['Store_Order_ID'],
                                            'fee'=> $iNgCodeTrade['RepayAmount']
                                        ]
                                    );
                                    if($ret2['retCd'] == 0){
                                        $cntRefund++;
                                    }else{
                                        throw new \Exception(' qrpays.refunds error ('.$ret['retCd'].')', 801, $ret['exception']);
                                    }
                                    break;
                                case 'PAYING':          // 支払待ち --> 5分後に再チェック
                                    $cntRetry++;
                                    break;
                                case 'FULL_REFUND':     // 全額返金済 --> 3(finish)にして処理終了
                                    $iNgCodeTrade['Status_ID'] = 3;
                                    $iNgCodeTrade['Update_User'] = $this->config['QRPAY']['CheckQrOrders']['UpdateUserName'];
                                    $iNgCodeTrade['Update_Date'] = date('Y/m/d H:i:s');
                                    try {
                                        $this->TINgCodeTrade->save($iNgCodeTrade);
                                        $cntFinish++;
                                    } catch (\Exception $e) {
        		                        throw new \Exception(' TINgCodeTrade update error (finish): ', 802, $e);
                                    }
                                    break;
                                case 'PARTIAL_REFUND':  // 一部偏諱済 --> 2(fault)にして処理終了
                                default:                // その他 --> 2(fault)にして処理終了
                                    $iNgCodeTrade['Status_ID'] = 2;
                                    $iNgCodeTrade['Update_User'] = $this->config['QRPAY']['CheckQrOrders']['UpdateUserName'];
                                    $iNgCodeTrade['Update_Date'] = date('Y/m/d H:i:s');
                                    try {
                                        $this->TINgCodeTrade->save($iNgCodeTrade);
                                        $cntFault++;
                                    } catch (\Exception $e) {
                                        throw new \Exception(' TINgCodeTrade update error (fault): ', 803, $e);
                                    }
                                    break;
                            }
                        }elseif($ret['resData']['returnCode'] == 'MP30002'){
                            $iNgCodeTrade['Status_ID'] = 3;
                            $iNgCodeTrade['Update_User'] = $this->config['QRPAY']['CheckQrOrders']['UpdateUserName'];
                            $iNgCodeTrade['Update_Date'] = date('Y/m/d H:i:s');
                            try {
                                $this->TINgCodeTrade->save($iNgCodeTrade);
                                $cntFinish++;
                            } catch (\Exception $e) {
        		                throw new \Exception(' TINgCodeTrade update error (finish): ', 802, $e);
                            }
                        }else{
                            switch($ret['resData']['msgSummaryCode']){
                                case 'S0016':           // 元取引が無効または見つからない --> 3(finish)にして処理終了
                                    $iNgCodeTrade['Status_ID'] = 3;
                                    $iNgCodeTrade['Update_User'] = $this->config['QRPAY']['CheckQrOrders']['UpdateUserName'];
                                    $iNgCodeTrade['Update_Date'] = date('Y/m/d H:i:s');
                                    try {
                                        $this->TINgCodeTrade->save($iNgCodeTrade);
                                        $cntFinish++;
                                    } catch (\Exception $e) {
                                        throw new \Exception(' TINgCodeTrade update error (finish): ', 804, $e);
                                    }
                                    break;
                                default:                // その他 --> 5分後に再チェック
                                    $cntRetry++;
                            }
                        }
                    }else{
                        // エラー  --> 5分後に再チェック
                        throw new \Exception(' qrpays.checkorder error (retCd='.$ret['retCd'].')', 820, $ret['exception']);
                    }
                } catch (\Exception $e) {
                    $cntError++;
                    $this->out(' ## TINgCodeTrade update error in '.__CLASS__.'.'.__FUNCTION__);
                    $buf = ' storeOrderId = '.$iNgCodeTrade['Store_Order_ID'].PHP_EOL;
                    $buf .= '(code:'.(string)$e->getCode().') '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
	                $prev = $e->getPrevious();
	                while($prev){
	                    $buf .= PHP_EOL . '(code:'.(string)$prev->getCode().') '.$prev->getMessage().' at '.$prev->getFile().':'.$prev->getLine();
    		            $prev = $prev->getPrevious();
    	            }
    	            $this->out($buf);
    	        }
    	    }

        }catch(\Exception $e){
            $this->out(' ### EXCEPTION in '.__CLASS__.'.'.__FUNCTION__);
            $buf = '';
            $buf .= '(code:'.(string)$e->getCode().') '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
		    $prev = $e->getPrevious();
		    while($prev){
                $buf .= PHP_EOL . '(code:'.(string)$prev->getCode().') '.$prev->getMessage().' at '.$prev->getFile().':'.$prev->getLine();
    		    $prev = $prev->getPrevious();
    	    }
            $this->out($buf);
        }
        $this->out(' == FINISH '.__CLASS__.'.'.__FUNCTION__.'() = '.$retCd);
        $this->out('     proc='.$cntRec.', refund='.$cntRefund.', retry='.$cntRetry .', fault='.$cntFault.', finish='.$cntFinish.', error='.$cntError);
     }

}
