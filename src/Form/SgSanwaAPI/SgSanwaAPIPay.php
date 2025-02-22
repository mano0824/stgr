<?php
namespace App\Form\SgSanwaAPI;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgSanwaAPIPay Form
 */
class SgSanwaAPIPay extends AppSanwaForm {
    private $type = "Pay";

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
            if (!isset($data['CMemNo']) || strlen($data['CMemNo']) <= 0)
            {
                $data['CMemNo'] = " ";
            }
            if (!isset($data['CMemName']) || strlen($data['CMemName']) <= 0)
            {
                $data['CMemName'] = " ";
            }
            if (!isset($data['CreditName']) || strlen($data['CreditName']) <= 0)
            {
                $data['CreditName'] = " ";
            }
            if (!isset($data['PeriodYmd']) || strlen($data['PeriodYmd']) <= 0)
            {
                $data['PeriodYmd'] = " ";
            }

            $trkNoList = [];
            for ($i = 0; $i < COUNT($data['TrkNoList']); $i++) {
                $trkNoList[$i] = $data['TrkNoList'][$i]['TrkNo'] ?? "";
            }
            $params = array(
                'MachineNo'       => $data['MachineNo'],
                'CallNo'          => $data['CallNo'],
                'TrkNoList'       => $trkNoList,
                'PaymentType'     => $data['PayKbn'],
                'PaymentAmount'   => $data['CKingaku']
            );

            if($config['DEBUG']){
                Log::write("debug",$params);
            }

            if($config['FIXED_RESPONSE']){
                // 固定応答モード
                $response = Configure::read('SgSanwaAPI_Pay_Res');
            }else{
                $response = $this->getContents($params, $this->type);
            }

            // 項目名変換
            $responseConvert = [
                'SgSanwa_Api' => $response['SgSanwa_Api'],
                'MachineNo'   => (int)$response['ResultInformation']['MachineNo'],
                'CallNo'      => (int)$response['ResultInformation']['CallNo'],
                'HostBillNo'  => (int)$response['ResultInformation']['BillNo'],
                'Status'      => (mb_strlen((int)$response['ResultInformation']['Status']) <= 2) ? (int)$response['ResultInformation']['Status'] : (string)$response['ResultInformation']['Status'], // 最大桁数 STGRが2桁整数、三和システム側が5桁整数
                'Message'     => (string)$response['Message'],
            ];

            if($config['DEBUG']){
                Log::write("debug", $responseConvert);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $responseConvert;
    }
}
