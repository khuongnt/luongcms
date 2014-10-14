<?php

class Model_Sm_Store_Request extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_request',
            'dependentTables' => array(
                'Model_Sm_Store_Request_Detail',
            ),
            self::ROW_CLASS => 'Model_Sm_Store_Request_Row',
        ));
    }

    public function get_alls($where = NULL, $order = NULL, $count = NULL, $offset = NULL, $countOnly = false) {
        $userModel = new Model_Auth_User();
        $select = $this->_db->select();
        if ($countOnly) {
            $t_columns = array('total' => 'COUNT(*)');
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        } else {
            $t_columns = array(
                'id',
                'shop_id',
                'stock_id',
                'request_status',
                'request_date',
                'quantity',
                'note',
                'feedback',
                'stock_trans_id',
                'stock_trans_date',
            );
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        }
        $select->from(
                array('t' => $this->_name)
                , $t_columns
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

    public function get_details($where = NULL, $order = NULL, $count = NULL, $offset = NULL, $countOnly = false) {
        $userModel = new Model_Auth_User();
        $requestDetailModel = new Model_Sm_Store_Request_Detail();
        $transactionSerialModel = new Model_Sm_Store_Transaction_Serial();
        $select = $this->_db->select();
        if ($countOnly) {
            $t_columns = array('total' => 'COUNT(*)');
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity', 'trans_status');
            $s_columns = NULL;
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        } else {
            $t_columns = array(
                'id',
                'shop_id',
                'stock_id',
                'request_status',
                'request_date',
                'quantity',
                'request_parts',
                'note',
                'feedback',
                'stock_trans_id',
                'stock_trans_date',
            );
            $d_columns = array('good_id', 'good_name', 'good_quantity' => 'quantity', 'trans_status');
            $s_columns = array('part_quantity' => 's.quantity', 'from_serial', 'to_serial');
            $u_columns = array('staff_id' => "IFNULL(u.id, '-1')", 'staff_user_name' => "IFNULL(u.user_name, '?')");
        }
        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('d' => $requestDetailModel->info('name'))
                , 't.id = d.request_id'
                , $d_columns
        );
        $select->joinLeft(
                array('s' => $transactionSerialModel->info('name'))
                , 'd.stock_trans_detail_id = s.stock_trans_detail_id'
                , $s_columns
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
