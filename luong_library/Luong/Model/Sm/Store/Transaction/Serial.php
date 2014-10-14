<?php

class Model_Sm_Store_Transaction_Serial extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_transaction_serial',
            'referenceMap' => array(
                'TransactionDetail' => array(
                    'columns' => array('stock_trans_detail_id'),
                    'refTableClass' => 'Model_Sm_Store_Transaction_Detail',
                    'refColumns' => array('id'),
                ),
            ),
//            self::ROW_CLASS => 'Model_Sm_Store_Transaction_Serial_Row',
        ));
    }

}
