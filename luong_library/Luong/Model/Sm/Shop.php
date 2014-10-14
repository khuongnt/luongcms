<?php

class Model_Sm_Shop extends Zend_Db_Table_Abstract {

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_shop',
            'referenceMap' => array(
                'ParentShop' => array(
                    'columns' => array('parent_shop_id'),
                    'refTableClass' => 'Model_Sm_Shop',
                    'refColumns' => array('id'),
                ),
                'SaleStaff' => array(
                    'columns' => array('sale_staff_id'),
                    'refTableClass' => 'Model_Sm_Staff',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(
                'Model_Sm_Shop',
                'Model_Sm_Store',
            ),
            self::ROW_CLASS => 'Model_Sm_Shop_Row',
        ));
    }

    public function count_by_staff($where = array(), $order = array(), $group = array()) {
        $select = $this->_db->select();
        $select->from(array('t' => $this->_name), array('sale_staff_id', 'shop_count' => 'COUNT(*)'));
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_val) {
                $select->where($_cond, $_val);
            }
        }
        if (!empty($group)) {
            $select->group($group);
        }
        if (!empty($order)) {
            $select->order($order);
        }
        $counts = $this->_db->fetchAll($select);
        $result = array();
        foreach ($counts as $_count) {
            $result[$_count->sale_staff_id] = $_count->shop_count;
        }
        return $result;
    }

}
