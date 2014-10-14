<?php

class Model_Sm_Store_Statistic extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_good',
//            self::ROW_CLASS => 'Model_Sm_Store_Statistic_Row',
        ));
    }

    public function count($stock_id = NULL, $good_id = NULL) {
        $select = $this
                ->_db
                ->select()
                ->from($this->_name, 'IFNULL(SUM(current_quantity), 0) AS current, IFNULL(SUM(available_quantity), 0) AS available', $this->_schema);
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
        return $this->_db->fetchRow($select);
    }

}
