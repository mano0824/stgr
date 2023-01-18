<?php
namespace App\Form\Sgsp;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgspPay Form
 */
class SgspPay extends AppForm {
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
            $params = array(
                'MachineNo' => $data['MachineNo'],
                'CallNo' => $data['CallNo'],
                'TrkNoList' => $data['TrkNoList'],
                'PayKbn' => $data['PayKbn'],
                'CKingaku' => $data['CKingaku'],
                'CMemNo' => $data['CMemNo'],
                'CMemName' => $data['CMemName'],
                'CreditName' => $data['CreditName'],
                'PeriodYmd' => $data['PeriodYmd'],
                'CreditReceiptNo' => $data['CreditReceiptNo']
            );

            if($this->config['Debug']){
                Log::write("debug",$params);
            }

            if($this->config['FixedResponse']){
                // 固定応答モード
                $response = Configure::read('Sgsp_Pay_Res');
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
