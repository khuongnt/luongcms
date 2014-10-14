<?php

class Model_Sm_Store_Transaction_Detail extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_transaction_detail',
            'referenceMap' => array(
                'Transaction' => array(
                    'columns' => array('stock_trans_id'),
                    'refTableClass' => 'Model_Sm_Store_Transaction',
                    'refColumns' => array('id'),
                ),
                'GoodType' => array(
                    'columns' => array('good_id'),
                    'refTableClass' => 'Sale_Models_Good',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(
                'Model_Sm_Store_Transaction_Serial',
            ),
//            self::ROW_CLASS => 'Model_Sm_Store_Transaction_Detail_Row',
        ));
    }

    public function get_transactions($where = NULL, $order = NULL) {
        $staffModel = new Model_Sm_Staff();
        $storeTransactionModel = new Model_Sm_Store_Transaction();
        $select = $this->_db->select();
        $select->from(
                array('t' => $this->_name)
                , array('trans_date', 'good_name', 'good_quantity' => 'quantity')
        );
        $select->joinRight(
                array('transaction' => $storeTransactionModel->info('name'))
                , '`t`.`stock_trans_id` = `transaction`.`id`'
                , array(
            'trans_type',
            'from_stock_id',
            'to_stock_id', 'quantity',
            'trans_status',
            'created_date',
            'note'
                )
        );
        $select->joinLeft(
                array('staff' => $staffModel->info('name'))
                , '`transaction`.`staff_id` = `staff`.`id`'
                , array('staff_name' => "IFNULL(name, '?')")
        );
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->order($order);
        die($select);
        return $this->_db->fetchAll($select);
    }

}
