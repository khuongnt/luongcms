<?php

class Model_Sm_Sale_Transaction_Detail extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_sale_transaction_detail',
            'referenceMap' => array(
                'Transaction' => array(
                    'columns' => array('sale_trans_id'),
                    'refTableClass' => 'Model_Sm_Sale_Transaction',
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

}
