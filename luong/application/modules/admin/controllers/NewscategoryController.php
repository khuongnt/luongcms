<?php

class Admin_NewscategoryController extends Luong_Controller_Admin_Action {

    protected $_contextPath;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/newscategory';
        $this->_text = 'Danh mục tin tức';
        $this->view->contextPath = $this->_contextPath;
    }

    public function indexAction() {
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $catModel = $dbname->luong_category;

        $recordPerPage = 8;
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $skip = ($currentPage * $recordPerPage) - $recordPerPage;
        $catList = $catModel->find()->limit($recordPerPage)->skip($skip)->sort(array('order' => 1));
        $total_cat = $catModel->find();
        $this->view->totalRecords = $total_cat->count();
        $this->view->recordPerPage = $recordPerPage;
        $this->view->catList = $catList;

        try {
            $status = 1;
            $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
        	<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
        	<li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
        	<li>Danh sách</li>
        	</ul>';
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addAction() {
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $catModel = $dbname->luong_category;

        $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
									<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
                                    <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
									<li>Thêm mới</li>
								</ul>';
        $name = $this->_request->getParam('name');
        if($this->_request->getParam('slug')):
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('slug'));
        else:
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('name'));
        endif;
        $order = $this->_request->getParam('order');

        if ($this->_request->isPost()) {
            if (empty($name)) {
                $error = 'Tên danh mục không được để trống';
                $this->view->error = $error;
            } else {
                $newData = array(
                    'name' => $name,
                    'slug' => $slug,
                    'order' => $order,
                    'time' => time(),
                    'date' => (int) date("ymd"),
                );
                $catModel->insert($newData);
                $this->_redirect($this->_contextPath . '?do=add');
            }
        }
    }

    public function editAction() {
        $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
										<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
	                                    <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
										<li>Cập nhập</li>
									</ul>';
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $catModel = $dbname->luong_category;
        $cat_id = $this->_request->getParam('id');
        $editCat = $catModel->findOne(array('_id' => new MongoId($cat_id)));
        $this->view->editCat = $editCat;
        $name = $this->_request->getParam('name');
        $description = $this->_request->getParam('description');
        if($this->_request->getParam('slug')):
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('slug'));
        else:
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('name'));
        endif;
        $order = $this->_request->getParam('order');

        if ($this->_request->isPost()) {
            if (empty($name)) {
                $error = 'Tên danh mục không được để trống';
                $this->view->error = $error;
            } else {
                $editCat['name'] = $name;
                $editCat['slug'] = $slug;
                $editCat['order'] = $order;
                $editCat['description'] = $description;
                $catModel->save($editCat);
                $this->_redirect($this->_contextPath . '/edit?id='.$cat_id.'&update=true');
            }
        }
    }

    public function changeAction() {
        $id = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
        $newsDAO = new NewscategoryModel();
        $where = "cat_id = " . $id;
        $row = $newsDAO->fetchRow($where);
        if ($row->status == 1) {
            $data = array('status' => 0);
        } else if ($row->status == 0) {
            $data = array('status' => 1);
        }
        $newsDAO->update($data, $where);
        $this->_redirect($this->_contextPath . '/index?page=' . $page);
    }

    public function deleteAction() {
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $catModel = $dbname->luong_category;
        $cat_id = $this->_request->getParam('id');
        $catModel->remove(array('_id' => new MongoId($cat_id)));
        //$catModel->findOne(array('_id' => new MongoId($cat_id)));

        $this->_redirect($this->_contextPath);
    }

}

//END CLASS