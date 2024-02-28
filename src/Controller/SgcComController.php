<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use App\Controller\AppController;
use Cake\Network\Exception\SocketException;

/**
 * SgcComController Abstract Class
 */
abstract class SgcComController extends AppController
{
    public $viewClass = 'Json';

    const ERROR = '0';
    const SUCCESS = '1';

    abstract protected function payCheck();             // 精算情報問合せ
    abstract protected function payCheckDetail();       // 精算明細情報問合せ
    abstract protected function pay();                  // 精算処理
    abstract protected function payReset();             // 精算キャンセル
    abstract protected function payReceipt();           // 明細書発行 
    abstract protected function payPurchase();          // 購買履歴計上
    abstract protected function payPurchaseDecision();  // 購買履歴確定
    abstract protected function pointPayCheck();        // ポイント生産情報問合せ

    /**
     * 共通の初期化処理
     */
    protected function initializeAction($type)
    {
        // 設定情報を取得
        $config = Configure::read("STGR.{$type}");

        $logData = $this->request->data;
        if ($config['Debug']) {
            $this->log($logData, "debug");
        }
    }

    /**
     * メソッド開始時の共通ログ記録
     */
    protected function logStart($methodName)
    {
        $this->log("======================================================", "debug");
        $this->log("■ {$methodName} START " . "(" . str_replace(__NAMESPACE__ . '\\', '', get_class()) . ")", "debug");
    }

    /**
     * メソッド中の共通ログ記録
     */
    protected function logSgStart($methodName)
    {
        $this->log("■ {$methodName} START", "debug");
    }

    /**
     * メソッド終了時の共通ログ記録
     */
    protected function logEnd($methodName)
    {
        $this->log("■ {$methodName} END " . "(" . str_replace(__NAMESPACE__ . '\\', '', get_class()) . ")", "debug");
    }

    /**
     * JSON形式での通信処理
     * @param mixed $obj 処理対象のオブジェクト
     * @param string $errCode エラーコード
     * @return array 結果配列
     */
    protected function postJson($obj, $errCode)
    {
        // $this->log("postJson START", "debug");

        $result = array();
        $result['success'] = $errCode;
        $result['result_code'] = '';
        $result['message'] = '';

        try {
            if ($this->request->is('post')) {
                $response = $obj->execute($this->request->data);

                $result['response'] = $response;
                $result['success'] = self::SUCCESS;
            }
        } catch (Exception $e) {
            // 例外処理
            $result = json_decode($e->getMessage(), true);
            $result['success'] = $errCode;
        }


        // $this->log("postJson END","debug");
        return $result;
    }

    protected function finalizeAction($result)
    {
        // $this->log($result,"debug");
        $this->set('result', $result);
        $this->render('response');

        return $result;
    }
}