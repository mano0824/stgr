<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use App\Controller\SgcComController;
use App\Form\Sgsp\SgspPayCheck;
use App\Form\Sgsp\SgspPayCheckDetail;
use App\Form\Sgsp\SgspPay;
use App\Form\Sgsp\SgspPayReset;
use App\Form\Sgsp\SgspPayReceipt;
use App\Form\Sgsp\SgspPayPurchase;
use App\Form\Sgsp\SgspPayPurchaseDecision;
use App\Form\Sgsp\SgspPointPayCheck;
use Cake\Network\Exception\SocketException;

/**
 * Sgsp Controller
 */
class SgspController extends SgcComController
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
        // initialize
        $this->logStart("Sgsp | payCheck");

        // 設定情報を取得
        $this->initializeAction('GOLF');

        // payCheck START
        $this->logSgStart("SgspPayCheck");

        $pSgspPayCheck = new SgspPayCheck();
        $retSgspPayCheck = $this->postJson($pSgspPayCheck, self::ERROR);

        $responseSgspPayCheck = isset($retSgspPayCheck['response']) ? $retSgspPayCheck['response'] : null;

        if (self::ERROR == $retSgspPayCheck['success']) {
            $this->log("payCheck CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayCheck) {
            $result['PayCheckResult'] = $retSgspPayCheck['response'];
            }
        }
    
        $this->finalizeAction($result);
    
        $this->logEnd("Sgsp | payCheck");
    }    

    /**
     * 精算明細情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function payCheckDetail()
    {
        // initialize
        $this->logStart("Sgsp | payCheckDetail");

        // 設定情報を取得
        $this->initializeAction('GOLF');

        // SgspPayCheckDetail START
        $this->logSgStart("SgspPayCheckDetail");

        $pSgspPayCheckDetail = new SgspPayCheckDetail();
        $retSgspPayCheckDetail = $this->postJson($pSgspPayCheckDetail, self::ERROR);
        $responseSgspPayCheckDetail = isset($retSgspPayCheckDetail['response']) ? $retSgspPayCheckDetail['response'] : null;

        if (self::ERROR == $retSgspPayCheckDetail['success']) {
        $this->log("payCheckDetail CONNECTION ERROR ","debug");
        throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayCheckDetail) {
                $result['PayCheckDetailResult'] = $retSgspPayCheckDetail['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | payCheckDetail");
    }

    /**
     * 精算
     *
     * @return \Cake\Network\Response|null
     */
    public function pay()
    {
        // initialize
        $this->logStart("Sgsp | Pay");

        // 設定情報を取得
        $this->initializeAction('GOLF');
    
        // SgspPay START
        $this->logSgStart("SgspPay");

        $pSgspPay = new SgspPay();
        $retSgspPay = $this->postJson($pSgspPay, self::ERROR);
        $responseSgspPay = isset($retSgspPay['response']) ? $retSgspPay['response'] : null;

        if (self::ERROR == $retSgspPay['success']) {
            $this->log("pay CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPay) {
                $result['PayResult'] = $retSgspPay['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | pay");
    }    

    /**
     * 精算キャンセル
     *
     * @return \Cake\Network\Response|null
     */
    public function payReset()
    {
        // initialize
        $this->logStart("Sgsp | payReset");

        // 設定情報を取得
        $this->initializeAction('GOLF');
    
        // SgspPayReset START
        $this->logSgStart("SgspPayReset");

        $pSgspPayReset = new SgspPayReset();
        $retSgspPayReset = $this->postJson($pSgspPayReset, self::ERROR);
        $responseSgspPayReset = isset($retSgspPayReset['response']) ? $retSgspPayReset['response'] : null;

        if (self::ERROR == $retSgspPayReset['success']) {
            $this->log("payReset CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayReset) {
                $result['PayResetResult'] = $retSgspPayReset['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | payReset");
    }

    /**
     * 明細書発行
     *
     * @return \Cake\Network\Response|null
     */
    public function payReceipt()
    {
        // initialize
        $this->logStart("Sgsp | payReceipt");

        // 設定情報を取得
        $this->initializeAction('GOLF');
    
        // SgspPayReceipt START
        $this->logSgStart("SgspPayReceipt");

        $pSgspPayReceipt = new SgspPayReceipt();
        $retSgspPayReceipt = $this->postJson($pSgspPayReceipt, self::ERROR);
        $responseSgspPayReceipt = isset($retSgspPayReceipt['response']) ? $retSgspPayReceipt['response'] : null;

        if (self::ERROR == $retSgspPayReceipt['success']) {
            $this->log("payReceipt CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayReceipt) {
                $result['PayReceiptResult'] = $retSgspPayReceipt['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | payReceipt");
    }

    /**
     * 購買履歴計上
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchase()
    {
        // initialize
        $this->logStart("Sgsp | payPurchase");

        // 設定情報を取得
        $this->initializeAction('GOLF');
    
        // SgspPayPurchase START
        $this->logSgStart("SgspPayPurchase START");

        $pSgspPayPurchase = new SgspPayPurchase();
        $retSgspPayPurchase = $this->postJson($pSgspPayPurchase, self::ERROR);
        $responseSgspPayPurchase = isset($retSgspPayPurchase['response']) ? $retSgspPayPurchase['response'] : null;

        if (self::ERROR == $retSgspPayPurchase['success']) {
            $this->log("payPurchase CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayPurchase) {
                $result['PayPurchaseResult'] = $retSgspPayPurchase['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | payPurchase");
    }

    /**
     * 購買履歴確定
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchaseDecision()
    {
        // initialize
        $this->logStart("Sgsp | payPurchaseDecesion");

        // 設定情報を取得
        $this->initializeAction('GOLF');

        // SgspPayPurchaseDecision START
        $this->logSgStart("SgspPayPurchaseDecision");
    
        $pSgspPayPurchaseDecision = new SgspPayPurchaseDecision();
        $retSgspPayPurchaseDecision = $this->postJson($pSgspPayPurchaseDecision, self::ERROR);
        $responseSgspPayPurchaseDecision = isset($retSgspPayPurchaseDecision['response']) ? $retSgspPayPurchaseDecision['response'] : null;

        if (self::ERROR == $retSgspPayPurchaseDecision['success']) {
            $this->log("payPurchaseDecision CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPayPurchaseDecision) {
                $result['PayPurchaseDecisionResult'] = $retSgspPayPurchaseDecision['response'];
            }
        }

        $this->finalizeAction($result);

        $this->logEnd("Sgsp | payPurchaseDecision");
    }
    
    /**
     * ポイント精算情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function pointPayCheck()
    {
        // initialize
        $this->logStart("Sgsp | pointPayCheck");

        // 設定情報を取得
        $this->initializeAction('GOLF');
    
        // SgspPointPayCheck START
        $this->logSgStart("SgspPointPayCheck");

        $pSgspPointPayCheck = new SgspPointPayCheck();
        $retSgspPointPayCheck = $this->postJson($pSgspPointPayCheck, self::ERROR);
        $responseSgspPointPayCheck = isset($retSgspPointPayCheck['response']) ? $retSgspPointPayCheck['response'] : null;
        if (self::ERROR == $retSgspPointPayCheck['success']) {
            $this->log("pointPayCheck CONNECTION ERROR ","debug");
            throw new Exception('上位通信アプリ接続エラー');
        } else {
            if ($responseSgspPointPayCheck) {
                $result['PointPayCheckResult'] = $retSgspPointPayCheck['response'];
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
        $this->finalizeAction($result);

        $this->logEnd("Sgsp | pointPayCheck");
    }
}
