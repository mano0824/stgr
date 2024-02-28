<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use App\Controller\SgcComController;
use App\Form\SgSanwaAPI\SgSanwaAPIPayCheck;
use App\Form\SgSanwaAPI\SgSanwaAPIPay;
use App\Form\SgSanwaAPI\SgSanwaAPIPayReset;
use App\Form\SgSanwaAPI\SgSanwaAPIPayReceipt;
use App\Form\SgSanwaAPI\SgSanwaAPIPointPayCheck;
use Cake\Network\Exception\SocketException;
use Cake\Http\Client;

/**
 * SgSanwaAPI Controller
 */
class SgSanwaAPIController extends SgcComController
{
    public $viewClass = 'Json';

    // エラー
    const ERROR = '0';
    // 成功
    const SUCCESS = '1';

    /**
     * 精算情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function payCheck()
    {        
        $config = Configure::read("STGR.SanwaAPI");

        if ($config['Debug']) {
            $this->logStart("SanwaAPI | payCheck");
        }

        // 共通初期化処理
        $this->initializeAction('SanwaAPI');

        $result = array();
    
        // payCheck START
        if ($config['Debug']) {
            $this->logSgStart("SgSanwaAPIPayCheck");
        }

        $pSgSanwaAPIPayCheck = new SgSanwaAPIPayCheck();
        $retSgSanwaAPIPayCheck = $this->postJson($pSgSanwaAPIPayCheck, self::ERROR);
        if ($config['Debug']) {
            $this->log($retSgSanwaAPIPayCheck, "debug");
        }
        $responseSgSanwaAPIPayCheck = isset($retSgSanwaAPIPayCheck['response']) ? $retSgSanwaAPIPayCheck['response'] : null;

        if (self::ERROR == $retSgSanwaAPIPayCheck['success']) {
            $this->log("payCheck CONNECTION ERROR", "debug");
            throw new Exception('Pay Check 接続エラー');
        
        } else {
            if ($responseSgSanwaAPIPayCheck) {
                $result['PayCheckResult'] = $retSgSanwaAPIPayCheck['response'];
            }
        }
    
        // 処理の終了とレスポンス
        $this->finalizeAction($result);
    
        // ログの終了
        if ($config['Debug']) {
            $this->logEnd("SanwaAPI | payCheck");
        }
    }

    /**
     * 精算明細情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function payCheckDetail()
    {
        $this->log("SanwaWebAPI | payCheckDetail: Not function.", "error");
    }

    /**
     * 精算
     *
     * @return \Cake\Network\Response|null
     */
    public function pay()
    {
        $config = Configure::read("STGR.SanwaAPI");

        if ($config['Debug']){
            $this->logStart("SanwaAPI | Pay");
        }

        // 共通初期化処理
        $this->initializeAction('SanwaAPI');
        
        // pay START
        if ($config['Debug']){
            $this->logSgStart("SgSanwaAPIPay");
        }

        $pSgSanwaAPIPay = new SgSanwaAPIPay();
        $retSgSanwaAPIPay = $this->postJson($pSgSanwaAPIPay, self::ERROR);
        if ($config['Debug']) {
            $this->log($retSgSanwaAPIPay, "debug");
        }
        $responseSgSanwaAPIPay = isset($retSgSanwaAPIPay['response']) ? $retSgSanwaAPIPay['response'] : null;

        if (self::ERROR == $retSgSanwaAPIPay['success']) {
            $this->log("Pay CONNECTION ERROR", "debug");
            throw new Exception('Pay 接続エラー');
        } else {
            if ($responseSgSanwaAPIPay) {
                $result['PayResult'] = $retSgSanwaAPIPay['response'];
            }
        }

        // 処理の終了とレスポンス
        $this->finalizeAction($result);

        // ログの終了
        if ($config['Debug']){
            $this->logEnd("SanwaAPI | Pay");
        }
    }

    /**
     * 精算キャンセル
     *
     * @return \Cake\Network\Response|null
     */
    public function payReset()
    {
        $config = Configure::read("STGR.SanwaAPI");

        if ($config['Debug']){
            $this->logStart("SanwaAPI | payReset");
        }

        // 共通初期化処理
        $this->initializeAction('SanwaAPI');

        // payReset START
        if ($config['Debug']){
            $this->logSgStart("SgSanwaAPIPayReset");
        }

        $pSgSanwaAPIPayReset = new SgSanwaAPIPayReset();
        $retSgSanwaAPIPayReset = $this->postJson($pSgSanwaAPIPayReset, self::ERROR);
        if ($config['Debug']) {
            $this->log($retSgSanwaAPIPayReset, "debug");
        }
        $responseSgSanwaAPIPayReset = isset($retSgSanwaAPIPayReset['response']) ? $retSgSanwaAPIPayReset['response'] : null;

        if (self::ERROR == $retSgSanwaAPIPayReset['success']) {
            $this->log("payReset CONNECTION ERROR", "debug");
            throw new Exception('Pay Reset 接続エラー');
        } else {
            if ($responseSgSanwaAPIPayReset) {
                $result['PayResetResult'] = $retSgSanwaAPIPayReset['response'];
            }
        }

        // 処理の終了とレスポンス
        $this->finalizeAction($result);

        // ログの終了
        if ($config['Debug']){
            $this->logEnd("SanwaAPI | payReset");
        }
    }

    /**
     * 明細書発行
     *
     * @return \Cake\Network\Response|null
     */
    public function payReceipt()
    {
        $config = Configure::read("STGR.SanwaAPI");

        if ($config['Debug']){
            $this->logStart("SanwaAPI | payReceipt");
        }

        // 共通初期化処理
        $this->initializeAction('SanwaAPI');

        // payReceipt START
        if ($config['Debug']){
            $this->logSgStart("SgSanwaAPIPayReceipt");
        }

        $pSgSanwaAPIPayReceipt = new SgSanwaAPIPayReceipt();
        $retSgSanwaAPIPayReceipt = $this->postJson($pSgSanwaAPIPayReceipt, self::ERROR);
        if ($config['Debug']) {
            $this->log($retSgSanwaAPIPayReceipt, "debug");
        }
        $responseSgSanwaAPIPayReceipt = isset($retSgSanwaAPIPayReceipt['response']) ? $retSgSanwaAPIPayReceipt['response'] : null;

        if (self::ERROR == $retSgSanwaAPIPayReceipt['success']) {
            $this->log("payReceipt CONNECTION ERROR", "debug");
            throw new Exception('Pay Receipt 接続エラー');
        } else {
            if ($responseSgSanwaAPIPayReceipt) {
                $result['PayReceiptResult'] = $retSgSanwaAPIPayReceipt['response'];
            }
        }

        // 処理の終了とレスポンス
        $this->finalizeAction($result);

        // ログの終了
        if ($config['Debug']){
            $this->logEnd("SanwaAPI | payReceipt");
        }
    }

    /**
     * 購買履歴計上
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchase()
    {
        throw new Exception('SanwaWebAPI | Pay Purchase is not Function.');
    }

    /**
     * 購買履歴確定
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchaseDecision()
    {
        throw new Exception('SanwaWebAPI | Pay Purchase Decision is not Function.');
    }

    /**
     * ポイント精算情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function pointPayCheck()
    {
        $config = Configure::read("STGR.SanwaAPI");

        if ($config['Debug']){
            $this->logStart("SanwaAPI | pointPayCheck");
        }
    
        // 設定情報を取得
        $this->initializeAction('SanwaAPI');
    
        // pointPayCheck START
        if ($config['Debug']){
            $this->logSgStart("SgSanwaAPIPointPayCheck");
        }

        $pSgSanwaAPIPointPayCheck = new SgSanwaAPIPointPayCheck();
        $retSgSanwaAPIPointPayCheck = $this->postJson($pSgSanwaAPIPointPayCheck, self::ERROR);
        if ($config['Debug']) {
            $this->log($retSgSanwaAPIPointPayCheck, "debug");
        }
        $responseSgSanwaAPIPointPayCheck = isset($retSgSanwaAPIPointPayCheck['response']) ? $retSgSanwaAPIPointPayCheck['response'] : null;

        if (self::ERROR == $retSgSanwaAPIPointPayCheck['success']) {
            $this->log("pointPayCheck CONNECTION ERROR", "debug");
            throw new Exception('Point Pay Check 接続エラー');
        } else {
            if ($responseSgSanwaAPIPointPayCheck) {
                $result['PointPayCheckResult'] = $retSgSanwaAPIPointPayCheck['response'];
                if (isset($result['PointPayCheckResult'])
                    && isset($result['PointPayCheckResult']['BillingAmount'])) {
                    // 請求金額がマイナスの場合
                    if ($result['PointPayCheckResult']['BillingAmount'] < 0) {
                        $result['PointPayCheckResult']['Message'] = '900002';
                    }
                    // 利用可能ポイントの上限が精算金額を超える場合
                    else if (isset($result['PointPayCheckResult']['UsePointsLimitUpper'])
                            && $result['PointPayCheckResult']['UsePointsLimitUpper'] > $result['PointPayCheckResult']['BillingAmount']
                            && isset($this->request->data['UsePoints']) && $this->request->data['UsePoints'] <= 0) {
                        $result['PointPayCheckResult']['Message'] = '900001';
                    }
                    // 請求金額が精算機の精算金額の上限
                    else if (isset($this->request->data['PaymentLimit'])
                            && $this->request->data['PaymentLimit'] > 0
                            && $result['PointPayCheckResult']['BillingAmount'] > $this->request->data['PaymentLimit']) {
                        $result['PointPayCheckResult']['Message'] = '900003';
                    }
                }
            }
        }
    
        // 処理の終了とレスポンス
        $this->finalizeAction($result);
    
        // ログの終了
        if ($config['Debug']){
            $this->logEnd("SanwaAPI | pointPayCheck");
        }
    }
}
