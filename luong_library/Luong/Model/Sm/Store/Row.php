<?php

class Model_Sm_Store_Row extends Zend_Db_Table_Row_Abstract {

    public $goods = array();
    public $total_sold_quantity = 0;
    public $total_current_quantity = 0;
    public $total_available_quantity = 0;
    protected $_shop;

    public function init() {
        $storeGoodModel = new Model_Sm_Store_Statistic();
        $storeGoodTotal = $storeGoodModel->count($this->id, NULL);
        $storeGoodSerialModel = new Model_Sm_Store_Good();
        $storeGoodSerialSold = $storeGoodSerialModel->count($this->id, NULL, 3);
        if (!empty($storeGoodTotal)) {
            $this->total_sold_quantity = $storeGoodSerialSold;
            $this->total_current_quantity = $storeGoodTotal->current;
            $this->total_available_quantity = $storeGoodTotal->available;
        }
    }

    public function getShop() {
        if (!isset($this->_shop)) {
            $this->_shop = $this->findParentRow('Model_Sm_Shop');
        }
        return $this->_shop;
    }

    public function get_statistic() {
        return $this->_table->get_statistic($this->id);
    }

}
