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
                'UsePointTrkNo' => $data['UsePointTrkNo'],
                'UsePoints' => $data['UsePoints'],
                'UsePointsKind' => $data['UsePointsKind']
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
