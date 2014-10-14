<?php

class Model_Sm_Sale_Transaction extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_sale_transaction',
            self::ROW_CLASS => 'Model_Sm_Sale_Transaction_Row',
            'referenceMap' => array(
                'StoreTransaction' => array(
                    'columns' => array('stock_trans_id'),
                    'refTableClass' => 'Model_Sm_Store_Transaction',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(
                'Model_Sm_Sale_Transaction_Detail',
            ),
        ));
    }

    public function get_transactions_detail($where = NULL, $order = NULL, $count = NULL, $offset = NULL, $countOnly = false) {
        $staffModel = new Model_Sm_Staff();
        $saleTransactionDetailModel = new Model_Sm_Sale_Transaction_Detail();
        $select = $this->_db->select();
        if ($countOnly) {
            $t_columns = array('total' => 'COUNT(*)');
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity', 'good_price' => 'IFNULL(price, 0)', 'good_amount' => 'IFNULL(amount, 0)');
            $s_columns = array('staff_id' => "IFNULL(s.id, '-1')", 'staff_name' => "IFNULL(s.name, '?')");
        } else {
            $t_columns = array(
                'id',
                'trans_date',
                'trans_status',
                'shop_id',
                'order_date',
            );
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity', 'good_price' => 'IFNULL(price, 0)', 'good_amount' => 'IFNULL(amount, 0)');
            $s_columns = array('staff_id' => "IFNULL(s.id, '-1')", 'staff_name' => "IFNULL(s.name, '?')");
        }
        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('d' => $saleTransactionDetailModel->info('name'))
                , 't.id = d.sale_trans_id'
                , $d_columns
        );
        $select->joinLeft(
                array('s' => $staffModel->info('name'))
                , 't.staff_id = s.id'
                , $s_columns
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
        if ($count !== NULL || $offset !== NULL) {
            $select->limit($count, $offset);
        }
        if ($countOnly) {
            $count = $this->_db->fetchRow($select);
            if (empty($count->total)) {
                $count->total = 0;
            }
            return $count->total;
        } else {
            return $this->_db->fetchAll($select);
        }
    }

    public function get_transactions_goods($where = NULL, $order = NULL) {
        $saleTransactionDetailModel = new Model_Sm_Sale_Transaction_Detail();
        $select = $this->_db->select();

        $t_columns = array(
            'trans_date' => 'DATE(trans_date)',
        );
        $d_columns = array('good_id', 'good_name', 'good_quantity' => 'SUM(quantity)', 'good_amount' => 'SUM(IFNULL(amount, 0))');

        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('d' => $saleTransactionDetailModel->info('name'))
                , 't.id = d.sale_trans_id'
                , $d_columns
        );
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->group('DATE(trans_date)');
        $select->group('good_name');
        $select->order($order);
        return $this->_db->fetchAll($select);
    }

}
