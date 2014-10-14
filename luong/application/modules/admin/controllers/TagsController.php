<?php

class Admin_TagsController extends Luong_Controller_Admin_Action {

    protected $_contextPath;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/tags';
        $this->_text = 'Tags';
        $this->view->contextPath = $this->_contextPath;
    }

    public function indexAction() {
            $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
            $dbname = $mongo->luong;
            $tagModel = $dbname->luong_tags;

            $recordPerPage = 20;
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
            $skip = ($currentPage * $recordPerPage) - $recordPerPage;
            $tagList = $tagModel->find()->limit($recordPerPage)->skip($skip)->sort(array('order' => 1));
            $total_tag = $tagModel->find();
            $this->view->totalRecords = $total_tag->count();
            $this->view->recordPerPage = $recordPerPage;
            $this->view->tagList = $tagList;

            $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
            <li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
            <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
            <li>Danh sách</li>
            </ul>';
    }

    public function addAction() {
        $this->view->headScript()->appendFile('/ckeditor/ckeditor.js');
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
									<li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
                                    <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
									<li>Thêm mới</li>
								</ul>';
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $tagModel = $dbname->luong_tags;
        if ($this->_request->isPost()) {
            $name = $this->_request->getParam('name');
            $slug = $this->_request->getParam('slug');
            if (!empty($slug)) {
                $slug = Luong_Helper_Utils::cv2urltitle($slug);
            } else {
                $slug = Luong_Helper_Utils::cv2urltitle($name);
            };
            if (empty($name)) {
                $error = 'Bạn hãy vui lòng nhập tiêu đề tag';
            }
            $check_tag = $tagModel->findOne(array('slug' => $slug));
            if (!empty($check_tag)) {
                $error = 'Tag '.$name.' đã có, bạn vui lòng tạo tag khác!';
            }
            if(isset($error)) {
                $this->view->error = $error;
            } else {
                $newData = array(
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $this->_request->getParam('description'),
                    'time' => time(),
                    'date' => (int) date("ymd"),
                    );
                $tagModel->insert($newData);
                $this->_redirect($this->_contextPath . '?do=add');
            };
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
        $tagModel = $dbname->luong_tags;
        $tag_id = $this->_request->getParam('id');
        $editTag = $tagModel->findOne(array('_id' => new MongoId($tag_id)));
        $this->view->editTag = $editTag;
        $name = $this->_request->getParam('name');
        if($this->_request->getParam('slug')):
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('slug'));
        else:
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('name'));
        endif;
        $description = $this->_request->getParam('description');

        if ($this->_request->isPost()) {
            if (empty($name)) {
                $error = 'Bạn hãy vui lòng nhập tiêu đề tag';
            }
            $check_tag = $tagModel->findOne(array('slug' => $slug, '_id' => array('$nin' => array(new MongoId($tag_id)))));
            if (!empty($check_tag)) {
                $error = 'Tag '.$name.' đã có, bạn vui lòng tạo tag khác!';
            }
            if(isset($error)) {
                $this->view->error = $error;
            } else {
                $editTag['name'] = $name;
                $editTag['slug'] = $slug;
                $editTag['description'] = $description;
                $tagModel->save($editTag);
                $this->_redirect($this->_contextPath . '?do=update');
            }
        }
    }

    public function changeAction() {
        $id = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
        $medDAO = new MedcategoryModel();
        $where = "cat_id = " . $id;
        $row = $medDAO->fetchRow($where);
        if ($row->status == 1) {
            $data = array('status' => 0);
        } else if ($row->status == 0) {
            $data = array('status' => 1);
        }
        $medDAO->update($data, $where);
        $this->_redirect($this->_contextPath . '/index?page=' . $page);
    }

    public function deleteAction() {
        $tag_id = $this->_request->getParam('id');
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $tagModel = $dbname->luong_tags;
        $tagModel->remove(array('_id' => new MongoId($tag_id)));
        $this->_redirect($this->_contextPath);
    }

}

//END CLASS