<?php

class Model_Sm_Store_Good extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock_good_serial',
            'dependentTables' => array(),
            'referenceMap' => array(
                'GoodType' => array(
                    'columns' => array('good_id'),
                    'refTableClass' => 'Model_Sm_Good',
                    'refColumns' => array('id'),
                ),
                'Store' => array(
                    'columns' => array('stock_id'),
                    'refTableClass' => 'Model_Sm_Store',
                    'refColumns' => array('id'),
                ),
            ),
            self::ROW_CLASS => 'Model_Sm_Store_Good_Row',
        ));
    }

    public function count($store_id = NULL, $good_id = NULL, $status = NULL) {
        $select = $this
                ->_db
                ->select()
                ->from($this->_name, 'COUNT(*) AS total', $this->_schema);
        if ($store_id !== NULL) {
            $select->where('stock_id = ?', $store_id);
        }
        if ($good_id !== NULL) {
            if (is_array($good_id)) {
                $select->where('good_id IN (?)', $good_id);
            } else {
                $select->where('good_id = ?', $good_id);
            }
        }
        if ($status !== NULL) {
            if (is_array($status)) {
                $select->where('status IN (?)', $status);
            } else {
                $select->where('status = ?', $status);
            }
        }
        $count = $this->_db->fetchRow($select);
        if (empty($count->total)) {
            return 0;
        } else {
            return $count->total;
        }
    }

    public function calculateStoreStatistic($store_id = NULL) {
        $select = $this
                ->_db
                ->select()
                ->from($this->_name, 'stock_id, good_id, status, COUNT(*) AS quantity', $this->_schema);
        if ($store_id !== NULL) {
            if (is_array($store_id)) {
                $select->where('store_id IN (?)', $store_id);
            } else {
                $select->where('store_id = ?', $store_id);
            }
        }
        $select->group(array('stock_id', 'good_id', 'status'));
        $statistic = $this->_db->fetchAll($select);
        return $statistic;
    }

    public function get_all_detail($where = NULL, $order = NULL, $limit = NULL, $offset = NULL, $count = FALSE) {
        $goodModel = new Model_Sm_Good();
        $scratchCardModel = new Model_Vc_Scratch_Card();
        $rechargeHistoryModel = new Model_Vc_Recharge_History();
        $select = $this->_db->select();

        if ($count) {
            $t_columns = array('total' => 'COUNT(*)');
        } else {
            $t_columns = '*';
        }
        $g_columns = array('good_code' => 'g.code', 'good_name' => 'g.name');
        $c_columns = array('card_status' => 'c.status');
        $r_columns = array('recharge_date' => 'r.date_time', 'recharge_phone' => 'r.phone_number');

        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('g' => $goodModel->info('name'))
                , 't.good_id = g.id'
                , $g_columns
        );
        $select->joinLeft(
                array('c' => $scratchCardModel->info('name'))
                , 't.serial = c.serial'
                , $c_columns
        );
        $select->joinLeft(
                array('r' => $rechargeHistoryModel->info('name'))
                , 't.serial = r.serial'
                , $r_columns
        );
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        if ($count) {
            $result = $this->_db->fetchRow($select);
            return empty($result->total) ? 0 : $result->total;
        } else {
            $select->order($order);
            if ($limit !== NULL || $offset !== NULL) {
                $select->limit($limit, $offset);
            }
            return $this->_db->fetchAll($select);
        }
    }

}
