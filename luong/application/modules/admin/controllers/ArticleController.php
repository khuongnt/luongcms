<?php

class Admin_ArticleController extends Luong_Controller_Admin_Action {

    protected $_contextPath;
    protected $_text;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/article';
        $this->_text = 'Bài viết';
        $this->view->contextPath = $this->_contextPath;
    }

    public function indexAction() {
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
							        	<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
							        	<li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
							        	<li>Danh sách</li>
							        	</ul>';
        $articleDAO = new ArticleModel();
        $paging = new Luong_Helper_Paging();
        $itemPerPage = Luong_Helper_Constant::PAGE_ITEM;
        $totalRecords = $articleDAO->getTotalRecords($status = 1);
        $totalRecords = $totalRecords['0'];
        $currentPages = (int) $this->_request->getParam('page');
        if ($currentPages == 0)
            $currentPages = 1;
        $url = $this->_contextPath . "/index";
        $page = $paging->doPaging($totalRecords, $url, $currentPages, $itemPerPage);
        $page_title = $paging->doPageSeparator($totalRecords, $currentPages, $itemPerPage);
        $this->view->page = $page;
        $this->view->page_title = $page_title;
        $this->view->currentPage = $currentPages;
        $list = $articleDAO->getList($status, $currentPages, $itemPerPage);
        $this->view->list = $list;
        if ($this->_request->getParam('do') == 'add')
            $this->view->success = 'Thêm mới thành công';
        elseif ($this->_request->getParam('do') == 'update')
            $this->view->success = 'Cập nhập thành công';
    }

    public function addAction() {
        try {
            $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
            $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
							        	<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
							        	<li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
							        	<li>Thêm mới</li>
							        	</ul>';
            if ($this->_request->isPost()) {
                $data['art_name'] = $this->_request->getParam('art_name');
                if ($this->_request->getParam('art_seo') != '')
                    $data['art_seo'] = $this->_request->getParam('art_seo');
                else
                    $data['art_seo'] = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('art_name'));
                $data['art_brief'] = $this->_request->getParam('art_brief');
                $data['art_content'] = stripslashes($this->_request->getParam('art_content'));
                $data['cat_id'] = $this->_request->getParam('cat_id');
                $data['status'] = $this->_request->getParam('status');
                $articleDAO = new ArticleModel();
                $articleDAO->insert($data);
                $this->_redirect($this->_contextPath . '?do=add');
            }
            $newCat = new NewscategoryModel();
            $this->view->listCat = $newCat->fetchAll('1=1', 'cat_order ASC');
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function editAction() {
        try {
            $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
            $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
							        	<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
							        	<li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
							        	<li>Cập nhập</li>
							        	</ul>';
            $id = $this->_request->getParam('id');
            $articleDAO = new ArticleModel();
            $row = $articleDAO->fetchRow('art_id=' . $id);
            $this->view->row = $row;
            if ($this->_request->isPost()) {
                $data['art_name'] = $this->_request->getParam('art_name');

                if ($this->_request->getParam('art_seo') != '')
                    $data['art_seo'] = $this->_request->getParam('art_seo');
                else
                    $data['art_seo'] = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('art_name'));

                $data['art_brief'] = $this->_request->getParam('art_brief');
                $data['art_content'] = stripslashes($this->_request->getParam('art_content'));
                $data['cat_id'] = $this->_request->getParam('cat_id');
                $data['status'] = $this->_request->getParam('status');
                $articleDAO->update($data, 'art_id=' . $id);
                $this->_redirect($this->_contextPath . '?do=update');
            }
            $newCat = new NewscategoryModel();
            $this->view->listCat = $newCat->fetchAll('1=1', 'cat_order ASC');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function changeAction() {
        $id = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
        $artDAO = new ArticleModel();
        $where = "art_id = " . $id;
        $row = $artDAO->fetchRow($where);
        if ($row->status == 1) {
            $data = array('status' => 0);
        } else if ($row->status == 0) {
            $data = array('status' => 1);
        }
        $artDAO->update($data, $where);
        $this->_redirect($this->_contextPath . '/index?page=' . $page);
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id');
        $articleDAO = new ArticleModel();
        $articleDAO->delete('art_id='. $id);
        $this->_redirect($this->_contextPath);
    }

}
