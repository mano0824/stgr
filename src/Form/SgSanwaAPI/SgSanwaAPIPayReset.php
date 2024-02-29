<?php
namespace App\Form\SgSanwaAPI;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgSanwaAPIPayReset Form
 */
class SgSanwaAPIPayReset extends AppSanwaForm {
    private $type = "PayReset";

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
                "CallNo"    => $data['CallNo'],
                "HolderNo"  => $trkNoList
            ];

            if($this->config['Debug']){
                Log::write("debug",$params);
            }

            if($this->config['FixedResponse']){
                // 固定応答モード
                $response = Configure::read('Sgsp_Pay_Reset_Res');
            }else{
                $response = $this->getContents($params, $this->type);
            }

            $responseConvert = [
                'SgSanwa_Api' => $response['SgSanwa_Api'],
                "ReturnCode"  => $response['ReturnCode'],
                "ErrData"     => $response['ErrData'],
                "Message"     => $response['Message']
            ];
            if ($responseConvert['ErrData'] != null) {
                $responseConvert['ErrData']['ErrCode'] = $response['ErrData']['ErrCode'];
                $responseConvert['ErrData']['ErrMessage'] = (!empty((string)$response['ErrData']['ErrMessage'])) ? (string)$response['ErrData']['ErrMessage'] : "";
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
