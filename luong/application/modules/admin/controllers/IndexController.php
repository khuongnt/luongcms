<?php

class Admin_IndexController extends Luong_Controller_Admin_Action {

    public function indexAction() {
        $this->_redirect("admin/news");
    }

}

//END CLASS