<?php
namespace App\Form\Sgsp;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgspPayPurchase Form
 */
class SgspPayPurchase extends AppForm {
    private $type = "PayPurchase";

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
                'CallNo' => $data['CallNo'],
                'TrkNo' => $data['TrkNo'],
                'SecurityCode' => $data['SecurityCode'],
                'Application' => $data['Application'],
                'ExpirationDate' => $data['ExpirationDate'],
                'Balance' => $data['Balance'],
                'InitialMoney' => $data['InitialMoney'],
                'HistoryNumber' => $data['HistoryNumber'],
                'ListDetail' => $data['ListDetail']
            );

            if($this->config['Debug']){
                Log::write("debug",$params);
            }

            if($this->config['FixedResponse']){
                // 固定応答モード
                $response = Configure::read('Sgsp_Pay_Purchase_Res');
            }else{
                $response = $this->getContents($params, $this->type);
            }

            if($this->config['Debug']){
                Log::write("debug", $response);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $response;
    }
}
