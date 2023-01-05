<?php
namespace App\Form\Sgsp;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Validation\Validator;

/**
 * SgspPointPayCheck Form
 */
class SgspPointPayCheck extends AppForm {
    private $type = "PointPayCheck";

    protected function _buildSchema(Schema $schema) {
        return $schema
                    // ->addField('RoomAccountID',     ['type' => 'string'])
                    // ->addField('RoomNo',            ['type' => 'integer'])
                    ;
    }
    
    // バリデーション後に実行する処理
    protected function _execute(array $data= array()) {
        try {
            $requestHeader = $this->getRequestHeader();

            $params = $requestHeader + array(
                // 'CheckIn_RequestList' => array(
                //     'RoomAccountID' => $data['RoomAccountID'],
                //     'RoomNo' => $data['RoomNo']
                // )
            );

            if($this->config['Debug']){
                Log::write("debug",$params);
            }

            if($this->config['FixedResponse']){
                // 固定応答モード
                $response = Configure::read('Sgsp_PointPay_Check_Res');
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
