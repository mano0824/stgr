<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use App\Controller\AppController;
use App\Common\SQLiteFunction;

/**
 * Api Controller
 */
class ApiController extends AppController
{
    // エラー
    const ERROR = '1';
    // 成功
    const SUCCESS = '0';
    // エラー
    const ERR_FORBIDDEN = '99999';

    /**
     * constructor.
     */
    public function __construct($request, $response) {
        parent::__construct($request, $response);
    }

    public function initialize()
    {
        parent::initialize();
        $config = Configure::read('STGR');
        $this->config = $config['API'];

    }
    /**
     * テスト
     * 
     * @return \Cake\Network\Response|null
     */
    public function test()
    {
        // initialize
        $this->log("======================================================", "debug");
        $this->log("■ test START (ApiController)", "debug");

        $result = array();

        if ($this->isAccessAllowed()) {
            $result['success'] = self::SUCCESS;
        } else {
            $result['success'] = self::ERROR;
            $result['message'] = self::ERR_FORBIDDEN;
            $result['http_status'] = 403;
        }

        $this->set('result', $result);
        $this->render('response');
        $this->log("■ test END (ApiController)", "debug");
    }

    /**
     * 精算機　動作状況
     *
     * @return \Cake\Network\Response|null
     */
    public function getOperationStatus()
    {
        // initialize
        if ($this->config['Debug']) {
            $this->log("======================================================", "debug");
            $this->log("■ getOperationStatus START (ApiController)", "debug");
        }

        $result = array();
        $result['success'] = self::ERROR;
        if ($this->isAccessAllowed()) {
            if ($this->request->is('post')) {
                $data = $this->request->data;
                if ($this->config['Debug']) {
                    $this->log(" -- post params (begin)", "debug");
                    $this->log($data, "debug");
                    $this->log(" -- post params (finish)", "debug");
                }
                $operationData = array();
                $operationData['DetailUrl'] = $this->config['DashboardURL'];
                $operationData['Status'] = '';
                $operationData['UnitID'] = '';
                $operationData['ErrorCode'] = '';
                $operationData['Message'] = '';
                $operationData['KindCode'] = '';
                $operationData['OptionKind'] = '';
                // 動作状況を取得
                $this->loadModel('TOperationStatus');
                $objModel = $this->TOperationStatus->find('all')->first();
                if ($this->config['Debug']) {
                    $this->log(" -- get db (begin)", "debug");
                    $this->log($objModel, "debug");
                    $this->log(" -- get db (finish)", "debug");
                }
                if ($objModel) {
                    if ($this->config['Debug']) {
                        $this->log(" -- TOperationStatus START", "debug");
                    }
                    $objDb = new SQLiteFunction();
                    $operationData['Status'] = $objModel['Status'];
                    $operationData['ErrorCode'] = $objModel['ErrorCode'];
                    $operationData['UnitID'] = $objModel['ErrorUnitID'];
                    if (($errors = $objDb->GetErrorInfo($objModel['ErrorUnitID'], $objModel['ErrorCode'])) !== false)
                    {
                        $operationData['Message'] = $errors['Message'];
                        $operationData['KindCode'] = $errors['KindCode'];
                        $operationData['OptionKind'] = $errors['Option1Kind'];
                    }
                    if ($this->config['Debug']) {
                        $this->log(" -- TOperationStatus END", "debug");
                    }
                }
                $result['success'] = self::SUCCESS;
                $result['OperationData'] = $operationData;
            }
        } else {
            $result['success'] = self::ERROR;
            $result['message'] = self::ERR_FORBIDDEN;
            $result['http_status'] = 403;
        }

        $this->set('result', $result);
        $this->render('response');
        if ($this->config['Debug']) {
            $this->log($result, "debug");
            $this->log("■ getOperationStatus END (ApiController)", "debug");
        }
    }

    /**
    　* アクセス許可を判定する
    　* @return boolean true:アクセス許可あり、false:アクセス許可なし
     */
    private function isAccessAllowed()
    {
        $ret = false;
        if ($this->config['AllowExternalAccess']) {
            $ret = true;
        }
        return $ret;
    }
}
