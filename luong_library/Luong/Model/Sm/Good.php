<?php

class Model_Sm_Good extends Zend_Db_Table_Abstract {

    const STATUS_AVAILABLE = 1;
    const STATUS_IMPORTING = 2;
    const STATUS_SOLD = 3;

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_good',
            'dependentTables' => array('Model_Sm_Shop'),
            'referenceMap' => array(),
            self::ROW_CLASS => 'Model_Sm_Good_Row',
        ));
    }

    public function count($stock_id = NULL, $good_id = NULL, $status = NULL) {
        $select = $this
                ->_db
                ->select()
                ->from($this->_name, 'COUNT(*) AS total', $this->_schema);
        if ($stock_id !== NULL) {
            $select->where('stock_id = ?', $stock_id);
        }
        if ($good_id !== NULL) {
            if (is_array($good_id)) {
                $select->where('good_id IN (?)', $good_id);
            } else {
                $select->where('good_id = ?', $good_id);
            }
        }
        if ($status !== NULL && in_array($status, array(1, 2, 3))) {
            $select->where('status = ?', $status);
        }
        $count = $this->_db->fetchRow($select);
        if (empty($count->total)) {
            return 0;
        } else {
            return $count->total;
        }
    }

    public static function statusToText($status) {
        switch ($status) {
            case self::STATUS_AVAILABLE:
                return 'Trong kho';
            case self::STATUS_IMPORTING:
                return 'Đang chờ nhập';
            case self::STATUS_SOLD:
                return 'Đã bán';
            default:
                return 'Không xác định';
        }
    }

    public static function statusToClass($status) {
        switch ($status) {
            case self::STATUS_AVAILABLE:
                return 'text-info';
            case self::STATUS_IMPORTING:
                return 'text-warning';
            case self::STATUS_SOLD:
                return 'text-success';
            default:
                return 'text-danger';
        }
    }

}
