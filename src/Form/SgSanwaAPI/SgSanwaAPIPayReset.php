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
            $params = array(
                'MachineNo' => $data['MachineNo'],
                'CallNo'    => $data['CallNo'],
                'TrkNoList' => $data['TrkNoList']
            );

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
                "ErrorData"   => $response['ErrorData'],
                "Message"     => $response['Message']
            ];
            if ($responseConvert['ErrorData'] != null) {
                $responseConvert['ErrorData']['ErrCode'] = $response['ErrorData']['ErrCode'];
                $responseConvert['ErrorData']['ErrMessage'] = (!empty((string)$response['ErrorData']['ErrMessage'])) ? (string)$response['ErrorData']['ErrMessage'] : "";
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
