<?php

class Model_Sm_Store extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_stock',
            'referenceMap' => array(
                'Shop' => array(
                    'columns' => array('shop_id'),
                    'refTableClass' => 'Model_Sm_Shop',
                    'refColumns' => array('id'),
                ),
                'ParentStore' => array(
                    'columns' => array('parent_stock_id'),
                    'refTableClass' => 'Model_Sm_Store',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(
                'Model_Sm_Store_Good',
                'Model_Sm_Store',
            ),
            self::ROW_CLASS => 'Model_Sm_Store_Row',
        ));
    }

    public function count($select = '*', $where = array(), $group = array(), $order = array()) {
        $select = $this->getAdapter()
                ->select()
                ->from($this->_name, array('count' => 'COUNT(' . $select . ')'), $this->_schema);
        foreach ($where as $_code => $_value) {
            if ($_value !== NULL) {
                $select->where($_code, $_value);
            }
        }
        $select->group($group);
        $select->order($order);
        $count = $this->getAdapter()->fetchRow($select);

        if (!empty($count->count)) {
            return $count->count;
        } else {
            return 0;
        }
    }

    public static function get_statistic($store_id) {
        $store_statistic = array();

        $storeGoodModel = new Model_Sm_Store_Good();

        $store_good_where = array();
        $store_good_where['stock_id = ?'] = $store_id;

        $store_statistic['total'] = $storeGoodModel->get_all_detail($store_good_where, NULL, NULL, NULL, TRUE);

        $store_good_where['t.status = ?'] = Model_Sm_Good::STATUS_AVAILABLE;
        $store_statistic['available'] = $storeGoodModel->get_all_detail($store_good_where, NULL, NULL, NULL, TRUE);

        $store_good_where['t.status = ?'] = Model_Sm_Good::STATUS_IMPORTING;
        $store_statistic['importing'] = $storeGoodModel->get_all_detail($store_good_where, NULL, NULL, NULL, TRUE);

        $store_good_where['t.status = ?'] = Model_Sm_Good::STATUS_SOLD;
        $store_statistic['sold'] = $storeGoodModel->get_all_detail($store_good_where, NULL, NULL, NULL, TRUE);

        unset($store_good_where['t.status = ?']);
        $store_good_where['c.status = ?'] = Model_Vc_Scratch_Card::STATUS_USED;
        $store_statistic['used'] = $storeGoodModel->get_all_detail($store_good_where, NULL, NULL, NULL, TRUE);

        return $store_statistic;
    }

}
