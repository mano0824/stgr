<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use App\Controller\AppController;
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
class SgspController extends AppController
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
      $this->log("======================================================","debug");
      $this->log("■ payCheck START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payCheck';

      // payCheck START
      $this->log("SgspPayCheck START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payCheck END (SgspController)","debug");
    }

    /**
     * 精算明細情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function payCheckDetail()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ payCheckDetail START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payCheckDetail';

      // SgspPayCheckDetail START
      $this->log("SgspPayCheckDetail START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payCheckDetail END (SgspController)","debug");
    }

    /**
     * 精算
     *
     * @return \Cake\Network\Response|null
     */
    public function pay()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ pay START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'pay';

      // SgspPay START
      $this->log("SgspPay START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ pay END (SgspController)","debug");
    }

    /**
     * 精算キャンセル
     *
     * @return \Cake\Network\Response|null
     */
    public function payReset()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ payReset START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payReset';

      // SgspPayReset START
      $this->log("SgspPayReset START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payReset END (SgspController)","debug");
    }

    /**
     * 明細書発行
     *
     * @return \Cake\Network\Response|null
     */
    public function payReceipt()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ payReceipt START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payReset';

      // SgspPayReceipt START
      $this->log("SgspPayReceipt START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payReceipt END (SgspController)","debug");
    }

    /**
     * 購買履歴計上
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchase()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ payPurchase START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payReset';

      // SgspPayPurchase START
      $this->log("SgspPayPurchase START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payPurchase END (SgspController)","debug");
    }

    /**
     * 購買履歴確定
     *
     * @return \Cake\Network\Response|null
     */
    public function payPurchaseDecision()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ payPurchaseDecision START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payReset';

      // SgspPayPurchaseDecision START
      $this->log("SgspPayPurchaseDecision START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ payPurchaseDecision END (SgspController)","debug");
    }

    /**
     * ポイント精算情報問合せ
     *
     * @return \Cake\Network\Response|null
     */
    public function pointPayCheck()
    {
      // initialize
      $this->log("======================================================","debug");
      $this->log("■ pointPayCheck START (SgspController)","debug");

      // 設定情報を取得
      $config = Configure::read('STGR');

      $logData = $this->request->data;

      $this->log($logData,"debug");

      $result = array();
      //$result['Php_Api'] = 'payReset';

      // SgspPointPayCheck START
      $this->log("SgspPointPayCheck START","debug");

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

      $this->log($result,"debug");
      $this->set('result', $result);
      $this->render('response');

      $this->log("■ pointPayCheck END (SgspController)","debug");
    }

    /**
     * 通信を行う
     *
     * @return null
     */
    private function postJson($obj, $errCode)
    {
      // $this->log(" postJson START","debug");

      $result = array();
      $result['success'] = $errCode;
      $result['result_code'] = '';
      $result['message'] = '';

      try {
            if($this->request->is('post')) {

              // $this->log("  Pms Api execute START","debug");
              $response = $obj->execute($this->request->data);
              // $this->log("  Pms APi execute END","debug");

              $result['response'] = $response;

              $result['success'] = self::SUCCESS;
            }
        } catch (Exception $e) {
            $result = json_decode($e->getMessage(), true);
            $result['success'] = $errCode;
        }

        // $this->log(" postJson END","debug");

        return $result;
    }

  }
