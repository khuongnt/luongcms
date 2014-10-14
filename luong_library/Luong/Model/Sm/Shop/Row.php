<?php

class Model_Sm_Shop_Row extends Zend_Db_Table_Row_Abstract {

    public $store;
    public $stores_tree;

    public function init() {
        if (!isset($this->stores_tree)) {
            $this->stores_tree = new JTree();
            $stores = $this->get_stores();
            foreach ($stores as $_store) {
                if (empty($_store->parent_stock_id)) {
                    if (!isset($this->store)) {
                        $this->store = $_store;
                        $this->stores_tree->createNode($_store, $_store->id, $_store->parent_stock_id);
                    }
                } else {
                    $this->stores_tree->createNode($_store, $_store->id, $_store->parent_stock_id);
                }
            }
        }
    }

    public function __get($columnName) {
        if ($columnName == 'status_text') {
            switch (parent::__get('status')) {
                case 0:
                    return 'Không hoạt động';
                case 1:
                    return 'Hoạt động';
                default:
                    return '';
            }
        }
        return parent::__get($columnName);
    }

    public function get_stores() {
        $storeModel = new Model_Sm_Store();
        $select = $storeModel->select();
        $select->where('shop_id = ?', $this->id);
        $select->order(array('parent_stock_id ASC', 'id ASC'));
        return $storeModel->fetchAll($select);
    }

}
