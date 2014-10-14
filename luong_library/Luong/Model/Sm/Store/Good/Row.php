<?php

class Model_Sm_Store_Good_Row extends Zend_Db_Table_Row_Abstract {

    public function getStatusText() {
        return Model_Sm_Good::statusToText($this->status);
    }

}
