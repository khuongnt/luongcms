<?php

class Admin_NewsController extends Luong_Controller_Admin_Action {

    protected $_contextPath;

    public function init() {
        parent::init();
        $this->_contextPath = '/admin/news';
        $this->_text = 'Tin tức';
        $this->view->contextPath = $this->_contextPath;
    }

    public function indexAction() {
        $this->view->breadcrumb = '<ul id="breadcrumbs" class="list_link_navi">
                                        <li class="home"><a href="/admin">Trang chủ</a><span class="divider">&raquo;</span></li>
                                        <li class=""><a href="' . $this->_contextPath . '">' . $this->_text . '</a><span class="divider">&raquo;</span></li>
                                        <li>Danh sách</li>
                                        </ul>';
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $postModel = $dbname->luong_posts;
        $tagModel = $dbname->luong_tags;

        $recordPerPage = 100;
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $skip = ($currentPage * $recordPerPage) - $recordPerPage;
        $postsList = $postModel->find()->limit($recordPerPage)->skip($skip)->sort(array('time' => -1));
        $total_posts = $postModel->find();
        $this->view->totalRecords = $total_posts->count();
        $this->view->recordPerPage = $recordPerPage;
        $this->view->postsList = $postsList;

        if (isset($_GET['link'])) {
            $rss = simplexml_load_file($_GET['link']);
            foreach ($rss->channel->item as $item) {
               $content_link = file_get_contents($item->link);
                if (preg_match('/<div class="short_intro txt_666">([^<]*)<\/div>/', $content_link, $matches) > 0) {
                    $excerpt = $matches[1]; //This is text one
                }
                if (preg_match('/<h1>([^<]*)<\/h1>/', $content_link, $titles) > 0) {
                    $title = $titles[1]; //This is text one
                }
                if (preg_match('/<div class=\"fck_detail width_common">(.*?)<\/div>/s', $content_link, $match) > 0) {
                    $content = $match[1]; 
                    $content = preg_replace("/<\\/?a(\\s+.*?>|>)/", "", $content);
                    $count = strlen($content);

                    preg_match('/<div class=\"block_tag width_common space_bottom_20">(.*?)<div>/s', $content_link, $tag);
                    print_r($content);
                    $tags = preg_replace("/<\\/?a(\\s+.*?>|>)/", ".", $tag[0]);
                    $tags = str_replace('<div class="txt_tag">Tags</div>', '', $tags);
                    preg_match('/<div class="block_tag width_common space_bottom_20">([^<]*)<\/div>/', $tags, $tag);
                    //var_dump($tag[1]);
                    //$tag = preg_replace('/\s+/', '', $tag[1]);
                    //$tag = str_replace('. .', ',', $tag[1]);
                    $tags = str_replace('  ', '', $tag[1]);
                    $tags_name = explode('.', $tags);
                    $tags_name = array_map('trim',$tags_name);
                    $tags_name = array_filter($tags_name);
                    if (!empty($tags_name)) {   
                        $tags_arr = array();
                        foreach ($tags_name as $tag) {
                            if(!empty($tag)){
                                $tag = str_replace('.', '', $tag);
                                $tag_slug =  Luong_Helper_Utils::cv2urltitle($tag); 
                                $checktag = $tagModel->findOne(array('slug' => $tag_slug));
                                if(empty($checktag)) {
                                    $tag_name = $tag;  
                                    $newData = array(
                                    'name' => $tag_name,
                                    'slug' => $tag_slug,
                                    'time' => time(),
                                    'date' => (int) date("ymd"),
                                    );
                                    $tagModel->insert($newData);
                                    $recent_tag = $tagModel->findOne(array('slug' => $tag_slug));
                                    array_push($tags_arr, (string)$recent_tag['_id']);
                                } else{
                                    array_push($tags_arr, (string)$checktag['_id']);
                                }
                            }
                        }
                    }
                    //$title = utf8_decode($item->title);
                    $thumb_ex = $item->description;
                    preg_match( '@src="([^"]+)"@' , $thumb_ex, $matches );
                    $thumb = array_pop($matches);
                    $thumb = str_replace('_180x108', '', $thumb);
                    $slug = Luong_Helper_Utils::cv2urltitle($title);
                    $slug = utf8_decode($slug); 
                    $checkpost = $postModel->findOne(array('slug' => $slug));
                    if (empty($checkpost) && $count > 300) {
                        $newData = array(
                            'title' => $title,
                            'slug' => $slug,
                            'thumb' => $thumb,
                            'cat' => '542a6996d445bfb4080001b5',
                            'type' => 'post',
                            'excerpt' => $excerpt,
                            'content' => $content,
                            'tags' => $tags_arr,
                            'time' => time(),
                            'date' => (int) date("ymd"),
                            'source' => 'VnExpress',
                        );
                        $postModel->insert($newData);
                    }

                } 
                
               //$content_link = $content_link->find('div[class=short_intro]');
               //echo $content_link;
               //var_dump($content_link);
            } 
            $this->_redirect($this->_contextPath . '?do=add');
        }        

        /*
        try {
            $status = 1;
            
            $newsDAO = new NewsModel();
            $paging = new Luong_Helper_Paging();
            $itemPerPage = Luong_Helper_Constant::PAGE_ITEM;
            $totalRecords = $newsDAO->getTotalRecords();
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
            $list = $newsDAO->getList($status, $currentPages, $itemPerPage);
            $this->view->list = $list;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        */
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
        $postModel = $dbname->luong_posts;
        $catModel = $dbname->luong_category;
        $tagModel = $dbname->luong_tags;

        if ($this->_request->isPost()) {
            $title = $this->_request->getParam('title');
            $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('title'));
            $thumb = $this->_request->getParam('thumb');
            $cat = $this->_request->getParam('cat');
            $type = $this->_request->getParam('cat');
            $excerpt = stripslashes($this->_request->getParam('excerpt'));
            $content = stripslashes($this->_request->getParam('content'));
            //$tags = $this->_request->getParam('tags');
            $tags_name = $this->_request->getParam('tags');
            if (!empty($tags_name)) {            
                $tags_name = explode(',', $tags_name);
                $tags = array();
                foreach ($tags_name as $tag) {
                    if(!empty($tag)){
                        $tag_slug =  Luong_Helper_Utils::cv2urltitle($tag); 
                        $checktag = $tagModel->findOne(array('slug' => $tag_slug));
                        if(empty($checktag)) {
                            $tag_name = $tag;  
                            $newData = array(
                            'name' => $tag_name,
                            'slug' => $tag_slug,
                            'time' => time(),
                            'date' => (int) date("ymd"),
                            );
                            $tagModel->insert($newData);
                            $recent_tag = $tagModel->findOne(array('slug' => $tag_slug));
                            array_push($tags, (string)$recent_tag['_id']);
                        } else{
                            array_push($tags, (string)$checktag['_id']);
                        }
                    }
                }
            }

            if (empty($title)) {
                $error = 'Tiêu đề bài viết không được để trống';
                $this->view->error = $error;  
            } elseif (empty($content)) {
                $error = 'Nội dung bài viết không được để trống';
                $this->view->error = $error;  
            } 
            else {
            $newData = array(
                'title' => $title,
                'slug' => $slug,
                'thumb' => $thumb,
                'cat' => $cat,
                'type' => 'post',
                'excerpt' => $excerpt,
                'content' => $content,
                'tags' => $tags,
                'time' => time(),
                'date' => (int) date("ymd"),
            );
            $postModel->insert($newData);
            $this->_redirect($this->_contextPath . '?do=add');
            }
        }
        $this->view->listCat = $catModel->find()->sort(array('order' => 1));
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
        $postModel = $dbname->luong_posts;
        $catModel = $dbname->luong_category;
        $tagModel = $dbname->luong_tags;
        $post_id = $this->_request->getParam('id');

        $editPost = $postModel->findOne(array('_id' => new MongoId($post_id)));
        $this->view->editPost = $editPost;
        if(!empty($editPost['tags'])){
            $crtag = $editPost['tags'];
            $current_tag = array();
            foreach ($crtag as $key => $tag) {
                if($tag !== ''){
                    $t = $tagModel->findOne(array('_id' => new MongoId($tag)));
                    array_push($current_tag, $t['name']);
                }
            }
            $this->view->current_tag = $current_tag;
        }

        $title = $this->_request->getParam('title');
        $slug = Luong_Helper_Utils::cv2urltitle($this->_request->getParam('title'));
        $thumb = $this->_request->getParam('thumb');
        $cat = $this->_request->getParam('cat');
        //$type = $this->_request->getParam('cat');
        $excerpt = stripslashes($this->_request->getParam('excerpt'));
        $content = stripslashes($this->_request->getParam('content'));
        $tags_name = $this->_request->getParam('tags');
        if (!empty($tags_name)) {            
            $tags_name = explode(',', $tags_name);
            $tags = array();
            foreach ($tags_name as $tag) {
                if(!empty($tag)){
                    $tag_slug =  Luong_Helper_Utils::cv2urltitle($tag); 
                    $checktag = $tagModel->findOne(array('slug' => $tag_slug));
                    if(empty($checktag)) {
                        $tag_name = $tag;  
                        $newData = array(
                        'name' => $tag_name,
                        'slug' => $tag_slug,
                        'time' => time(),
                        'date' => (int) date("ymd"),
                        );
                        $tagModel->insert($newData);
                        $recent_tag = $tagModel->findOne(array('slug' => $tag_slug));
                        array_push($tags, (string)$recent_tag['_id']);
                    } else{
                        array_push($tags, (string)$checktag['_id']);
                    }
                }
            }
        }
        
        if ($this->_request->isPost()) {
            if (empty($title)) {
                $error = 'Tiêu đề bài viết không được để trống';
                $this->view->error = $error;  
            } elseif (empty($content)) {
                $error = 'Nội dung bài viết không được để trống';
                $this->view->error = $error;  
            } 
            else {
                $editPost['title'] = $title;
                $editPost['slug'] = $slug;
                $editPost['thumb'] = $thumb;
                $editPost['cat'] = $cat;
                $editPost['excerpt'] = $excerpt;
                $editPost['content'] = $content;
                $editPost['tags'] = $tags;
                $editPost['tags_slug'] = $tags_slug;

                $postModel->save($editPost);
                $this->_redirect($this->_contextPath . '/edit?id='.$post_id.'&update=true');
            }
        }
        $this->view->listCat = $catModel->find()->sort(array('order' => 1));
    }

    public function changeAction() {
        $id = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
        $newsDAO = new NewsModel();
        $where = "news_id = " . $id;
        $row = $newsDAO->fetchRow($where);
        if ($row->news_active == 1) {
            $data = array('news_active' => 0);
        } else if ($row->news_active == 0) {
            $data = array('news_active' => 1);
        }
        $newsDAO->update($data, $where);
        $this->_redirect($this->_contextPath . '/index?page=' . $page);
    }

    public function changeHotAction() {
        $id = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
        $newsDAO = new NewsModel();
        $where = "news_id = " . $id;
        $row = $newsDAO->fetchRow($where);
        if ($row->news_type == 1) {
            $data = array('news_type' => 0);
        } else if ($row->news_type == 0) {
            $data = array('news_type' => 1);
        }
        $newsDAO->update($data, $where);
        $this->_redirect($this->_contextPath . '/index?page=' . $page);
    }

    public function deleteAction() {
        $id = $this->_request->getParam('id');

        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $postModel = $dbname->luong_posts;
        $post_id = $this->_request->getParam('id');
        $postModel->remove(array('_id' => new MongoId($post_id)));

        $this->_redirect($this->_contextPath);
    }

}

//END CLASS