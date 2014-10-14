<?php

class Admin_ConfigController extends Luong_Controller_Admin_Action {

    protected $_params;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/config';
        $this->_text = 'Cấu hình';
        $this->view->contextPath = $this->_contextPath;
    }

    public function indexAction() {
        $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
        $this->view->breadcrumb = '<li><a href="/admin">Home</a><span class="divider">&raquo;</span></li>
                                 <li class="active">Settings</li>';
        try {
           
            $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
            $dbname = $mongo->luong;
            $optionsModel = $dbname->luong_options;
            $editOptions = $optionsModel->findOne(array('_id' => new MongoId('542a49dad445bfac08000042')));
             $this->view->options = $editOptions;
            if ($this->_request->isPost()) {
                $params = $this->_request->getParams();
                
                unset($params['Submit']);
                unset($params['module']);
                unset($params['controller']);
                unset($params['action']);
                unset($params['do']);

                $ga = $params['ga'];
                $header = $params['header'];
                $footer = $params['footer'];
                $site_name = $params['site_name'];
                $meta_title = $params['meta_title'];
                $description = $params['description'];
                $keywords = $params['keywords'];
                
                $editOptions['ga'] = $ga;
                $editOptions['header'] = $header;
                $editOptions['footer'] = $footer;
                $editOptions['title'] = $site_name;
                $editOptions['meta_title'] = $meta_title;
                $editOptions['description'] = $description;
                $editOptions['keywords'] = $keywords;
                $editOptions['time'] = time();
                $editOptions['date'] = (int) date("ymd");
                
                $optionsModel->save($editOptions);
                $this->_redirect('/admin/config?do=success');
            }
            if ($this->_request->getParam('do'))
                $this->view->success = "Cập nhập thành công";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
