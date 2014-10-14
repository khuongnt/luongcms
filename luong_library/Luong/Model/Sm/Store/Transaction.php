<?php

class Model_Sm_Store_Transaction extends Zend_Db_Table_Abstract {

    const TRANS_TYPE_IMPORT = 'NK';
    const TRANS_TYPE_EXPORT = 'XK';
    const TRANS_TYPE_SALE = 'BT';
    const TRANS_TYPE_GIFT = 'TT';
    const TRANS_TYPE_DEBT_COLLECT = 'TN';

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_transaction',
            'dependentTables' => array(
                'Model_Sm_Store_Transaction_Detail',
            ),
            self::ROW_CLASS => 'Model_Sm_Store_Transaction_Row',
        ));
    }

    public function calculate_import_quantity($where = NULL) {
        $import_quantity = array();
        $select = $this->select();
        $select->from($this->_name, array('total' => 'IFNULL(SUM(quantity), 0)', 'stock_id' => 'to_stock_id'));
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->group('to_stock_id');
        $select->order('to_stock_id ASC');
        $rows = $this->fetchAll($select);
        if (!empty($rows)) {
            foreach ($rows as $_row) {
                $import_quantity[$_row->stock_id] = $_row->total;
            }
        }
        return $import_quantity;
    }

    public function calculate_export_quantity($where = NULL) {
        $export_quantity = array();
        $select = $this->select();
        $select->from($this->_name, array('total' => 'IFNULL(SUM(quantity), 0)', 'stock_id' => 'from_stock_id'));
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->group('from_stock_id');
        $select->order('from_stock_id ASC');
        $rows = $this->fetchAll($select);
        if (!empty($rows)) {
            foreach ($rows as $_row) {
                $export_quantity[$_row->stock_id] = $_row->total;
            }
        }
        return $export_quantity;
    }

    public function get_transactions_detail($where = NULL, $order = NULL, $count = NULL, $offset = NULL, $countOnly = false) {
        $userModel = new Model_Auth_User();
        $saleTransactionModel = new Model_Sm_Sale_Transaction();
        $storeTransactionDetailModel = new Model_Sm_Store_Transaction_Detail();
        $select = $this->_db->select();
        if ($countOnly) {
            $t_columns = array('total' => 'COUNT(*)');
            $s_columns = array('sale_trans_type' => 's.trans_type');
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity');
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        } else {
            $t_columns = array(
                'id',
                'trans_type',
                'trans_date',
                'from_stock_id',
                'to_stock_id',
                'quantity',
                'trans_status',
                'created_date',
                'note'
            );
            $s_columns = array('sale_trans_type' => 's.trans_type');
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity');
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        }
        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('s' => $saleTransactionModel->info('name'))
                , 't.id = s.stock_trans_id'
                , $s_columns
        );
        $select->joinLeft(
                array('d' => $storeTransactionDetailModel->info('name'))
                , 't.id = d.stock_trans_id'
                , $d_columns
        );
        $select->joinLeft(
                array('u' => $userModel->info('name'))
                , 't.staff_id = u.id'
                , $u_columns
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

}
