<?php

class Model_Sm_Staff_Row extends Zend_Db_Table_Row_Abstract {

    public function __get($columnName) {
        if ($columnName == 'status_text') {
            return $this->_table->statusToText($this->status);
        }
        return parent::__get($columnName);
    }

}
