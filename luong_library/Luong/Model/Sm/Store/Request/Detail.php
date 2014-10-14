<?php

class Model_Sm_Store_Request_Detail extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_request_detail',
            'referenceMap' => array(
                'Request' => array(
                    'columns' => array('request_id'),
                    'refTableClass' => 'Model_Sm_Store_Request',
                    'refColumns' => array('id'),
                ),
                'GoodType' => array(
                    'columns' => array('good_id'),
                    'refTableClass' => 'Sale_Models_Good',
                    'refColumns' => array('id'),
                ),
            ),
        ));
    }

    public function get_requests($where = NULL, $order = NULL) {
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
        return $this->_db->fetchAll($select);
    }

}
