<?php

class Model_Vc_Scratch_Card extends Zend_Db_Table_Abstract {

    const STATUS_AVAILABLE = 1;
    const STATUS_USED = 2;

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'vc_scratch_card',
            'dependentTables' => array(),
            'referenceMap' => array(),
//            self::ROW_CLASS => 'Model_Sm_Good_Row',
        ));
    }

    public static function statusToText($status) {
        switch ($status) {
            case self::STATUS_AVAILABLE:
                return 'Chưa kích hoạt';
            case self::STATUS_USED:
                return 'Đã kích hoạt';
            default:
                return 'Không xác định';
        }
    }

    public static function statusToClass($status) {
        switch ($status) {
            case self::STATUS_AVAILABLE:
                return 'text-info';
            case self::STATUS_USED:
                return 'text-success';
            default:
                return 'text-danger';
        }
    }

}
