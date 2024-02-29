<?php
namespace App\Form\SgSanwaAPI;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgSanwaAPIPointPayCheck Form
 */
class SgSanwaAPIPointPayCheck extends AppSanwaForm {
    private $type = "PointPayCheck";

    protected function _buildSchema(Schema $schema) {
        return $schema
                    ->addField('MachineNo',     ['type' => 'integer'])
                    ->addField('CallNo',        ['type' => 'integer'])
                    ;
    }
    
    // バリデーション後に実行する処理
    protected function _execute(array $data= array()) {
        try {
            $trkNoList = [];
            for ($i = 0; $i < COUNT($data['TrkNoList']); $i++) {
                $trkNoList[$i] = $data['TrkNoList'][$i]['TrkNo'] ?? "";
            }
            $params = [
                "MachineNo" => $data['MachineNo'],
                "CallNo"    => $data['CallNo']['callNo'],
                "HolderNo"  => $trkNoList
            ];


            if($this->config['Debug']){
                Log::write("debug",$params);
            }

            if($this->config['FixedResponse']){
                // 固定応答モード
                $response = Configure::read('Sgsp_Pay_Check_Res');
            }else{
                $response = $this->getContents($params, $this->type);
            }

            // 項目名変換
            $responseConvert = [
                'SgSanwa_Api' => $response['SgSanwa_Api'],
                'MachineNo'   => (int)$response['ResultInformation']['MachineNo'],
                'CallNo'      => (int)$response['ResultInformation']['CallNo'],
                'ListDetail'  => [],
            ];

            foreach ($response['ResultInformation']['HolderNoArray'] as $item) {
                $responseInnerItem = [
                    'TrkNo'       => (int)$item['HolderNo'],
                    'TrkName'     => (!empty((string)$item['HolderName'])) ? (string)$item['HolderName'] : "",
                    'TrkKana'     => (!empty((string)$item['HolderNameKana'])) ? (string)$item['HolderNameKana'] : "",
                    'Kingaku'     => (mb_strlen((int)$item['BillingAmount']) <= 6) ? (int)$item['BillingAmount'] : (int)$item['BillingAmount'], // 最大桁数 STGRが6桁整数、三和システム側が8桁整数
                    'Status'      => (mb_strlen((int)$item['Status']) == 2) ? (int)$item['Status'] : (int)$item['Status'], // 最大桁数 STGRが2桁整数、三和システム側が5桁整数
                    'Message'     => (!empty((string)$item['Message'])) ? (string)$item['Message'] : "",
                    'PayDoneFlag' => 0,
                ];

                $responseOuterItem = [
                    'BillingAmount'       => (int)$response['ResultInformation']['BillingAmountTotal'],
                    'UsePoints'           => (int)$params['UsePoints'],
                    'UsePointsKind'       => 0,
                    'PointBalance'        => (int)$item['PointBalance'],
                    'UsePointsLimitUpper' => (int)$item['AvailablePointMax'],
                    'UsePointsLimitLower' => (int)$item['AvailablePointMin'],
                    'UsePointsUnit'       => (int)$item['PointUseUnit'],
                    'UsePointsMessage'    => '1ポイントにつき1円の割引が可能です。',
                    'Message'             => !(empty((string)$response['Message'])) ? (string)$response['Message'] : "00000",
                ];

                $responseConvert['ListDetail'][] = $responseInnerItem;
                $responseConvert += $responseOuterItem;
            }

            if($this->config['Debug']){
                Log::write("debug", $responseConvert);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $responseConvert;
    }
}
