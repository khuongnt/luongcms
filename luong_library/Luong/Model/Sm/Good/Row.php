<?php

class Model_Sm_Good_Row extends Zend_Db_Table_Row_Abstract {

    public $prices = array();
    public $price = NULL;

    public function init() {
        $goodPriceModel = new Model_Sm_Good_Price();
        $goodPriceSelect = $goodPriceModel->select()
                ->where('good_id = ?', $this->id)
                ->where('status = ?', 1)
                ->where("(sta_date <= ? OR sta_date IS NULL) AND (end_date IS NULL OR ? <= end_date)", date('Y-m-d H:i:s'))
                ->order(array('sta_date DESC', 'end_date DESC'));
        $this->prices = $goodPriceModel->fetchAll($goodPriceSelect);
        if (isset($this->prices[0])) {
            $this->price = $this->prices[0];
        }
    }

}
