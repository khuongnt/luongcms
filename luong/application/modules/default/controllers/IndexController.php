<?php

class IndexController extends Luong_Controller_Default_Action {

    public function indexAction() {
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $postModel = $dbname->luong_posts;
        $catModel = $dbname->luong_category;

        $posts_top = $postModel->find()->limit(16)->sort(array('time' => -1));
        $this->view->posts_top = $posts_top;
        $cat = $catModel->find()->sort(array('order' => 1));
        
        $posts = $postModel->find(array('cat'=>'5424419ed445bfe886000029'))->limit(4)->sort(array('time' => -1)); // cong nghe
        $posts2 = $postModel->find(array('cat'=>'542449fbd445bf1c8f000029'))->limit(4)->sort(array('time' => -1)); // the thao
        $posts3 = $postModel->find(array('cat'=>'542a271ed445bfec0800004c'))->limit(4)->sort(array('time' => -1));// thoi su
        $posts4 = $postModel->find(array('cat'=>'5424425ad445bf088e000029'))->limit(4)->sort(array('time' => -1));// phap luat

        $posts5 = $postModel->find(array('cat'=>'542a515dd445bfd408000036'))->limit(4)->sort(array('time' => -1)); // the gioi
        $posts6 = $postModel->find(array('cat'=>'542a5180d445bfd408000037'))->limit(4)->sort(array('time' => -1)); // giai tri
        $posts7 = $postModel->find(array('cat'=>'542a51a4d445bfd408000038'))->limit(4)->sort(array('time' => -1));// khoa hoc
        $posts8 = $postModel->find(array('cat'=>'542a51b7d445bfd408000039'))->limit(4)->sort(array('time' => -1));// doi song

        $posts9 = $postModel->find(array('cat'=>'542a6880d445bfb40800009d'))->limit(4)->sort(array('time' => -1));// kinh doanh
        $posts10 = $postModel->find(array('cat'=>'542a6907d445bfb408000157'))->limit(4)->sort(array('time' => -1));// giao duc
        $posts11 = $postModel->find(array('cat'=>'542a6996d445bfb4080001b5'))->limit(4)->sort(array('time' => -1));// tinh cam
        $posts12 = $postModel->find(array('cat'=>'542a6a7cd445bfac0800014c'))->limit(4)->sort(array('time' => -1));// xe
        $posts13 = $postModel->find(array('cat'=>'542a2675d445bfac08000041'))->limit(4)->sort(array('time' => -1));// du lich


        $this->view->list_posts_by_cat = $posts;
        $this->view->list_posts_by_cat2 = $posts2;
        $this->view->list_posts_by_cat3 = $posts3;
        $this->view->list_posts_by_cat4 = $posts4;

        $this->view->list_posts_by_cat5 = $posts5;
        $this->view->list_posts_by_cat6 = $posts6;
        $this->view->list_posts_by_cat7 = $posts7;
        $this->view->list_posts_by_cat8 = $posts8;
        $this->view->list_posts_by_cat9 = $posts9;
        $this->view->list_posts_by_cat10 = $posts10;
        $this->view->list_posts_by_cat11 = $posts11;
        $this->view->list_posts_by_cat12 = $posts12;
        $this->view->list_posts_by_cat13 = $posts13;

        $this->view->cat = $cat;
        foreach ($cat as $cats) {
            $posts = $postModel->find(array('cat'=>(string)$cats['_id']))->limit(4)->sort(array('time' => -1));
            foreach ($posts as $key => $post) {
                $newposts = array('cat'=> (string)$cats['name'],'title' => $post['title'], 'excerpt' => $post['excerpt'], 'slug' => $post['slug'], 'thumb' => $post['thumb'], '_id' => (string)$post['_id']);
            }
        }
    }

    public function luongsingleAction() {
        $post_slug = $this->_request->getParam('slug');
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $postModel = $dbname->luong_posts;
        $catModel = $dbname->luong_category;
        $tagModel = $dbname->luong_tags;
        $single_post = $postModel->findOne(array('slug' => $post_slug));
        $post_id = $single_post['_id'];
        $keywords = array();
        if (isset($single_post['tags'])) {
            $list_tagid = $single_post['tags'];
            $tags = array();
            foreach ($list_tagid as $key => $tag_id) {
                $tag = $tagModel->findOne(array('_id' => new MongoId($tag_id)));
                array_push($tags, $tag);
                array_push($keywords, $tag['name']);
            }
            $this->view->tags = $tags;
        };
        $keywords = implode(', ', $keywords);
        $cat = $catModel->findOne(array('_id' => new MongoId($single_post['cat'])));
        $cat_id = (string)$cat['_id'];

        $related_post = $postModel->find(array('cat'=> $cat_id,'_id' => array('$nin' => array(new MongoId($post_id)))))->limit(14)->skip(3)->sort(array('time' => -1));
        $top3_post = $postModel->find(array('cat'=> $cat_id, '_id' => array('$nin' => array(new MongoId($post_id))) ) )->limit(3)->sort(array('time' => -1));

        $this->view->post = $single_post;
        $this->view->related_post = $related_post;
        $this->view->top3_post = $top3_post;
        $this->view->cat = $cat;
        $this->view->titlePage = $single_post['title'].' | '.$cat['name'];
        $this->view->keywords = $keywords;
    }
    public function luongcategoryAction() {
        $cat_slug = $this->_request->getParam('slug');
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $catModel = $dbname->luong_category;
        $postModel = $dbname->luong_posts;
        $cat = $catModel->findOne(array('slug' => $cat_slug));
        $cat_id = (string)$cat['_id'];
        $posts = $postModel->find(array('cat' => $cat_id))->limit(6)->sort(array('time' => -1));
        $this->view->cat = $cat;
        $this->view->posts = $posts;
        $this->view->cat_id = $cat_id;
        $this->view->titlePage = $cat['name'];
        if (isset($cat['description'])) {
            $this->view->description = $cat['description'];
        }else {
            $this->view->description = 'Thông tin, bài viết, tin tức mới nhất về '.$cat['name'];
        }
        
    }

    public function luongtagAction() {
        $tag_slug = $this->_request->getParam('slug');
        $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
        $dbname = $mongo->luong;
        $tagModel = $dbname->luong_tags;
        $postModel = $dbname->luong_posts;
        $tag = $tagModel->findOne(array('slug' => $tag_slug));
        if (isset($tag)) {
            $tag_name = (string)$tag['_id'];
            $posts = $postModel->find(array('tags' => array('$in' => array($tag_name)) ))->limit(10)->sort(array('time' => -1));
            $this->view->tag = $tag;
            $this->view->posts = $posts;
            $this->view->titlePage = 'Tin tức, bài viết mới nhất về '.$tag['name'];
            if (isset($tag['description'])) {
                $this->view->description = $tag['description'];
            } else {
                $this->view->description = 'Thông tin, bài viết, tin tức mới nhất về '.$tag['name'];
            }   
        } 
    }
    public function hdmhAction() {
        $this->_helper->layout()->disableLayout();
    }
    public function callbackAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $accessToken = $this->_request->getParam('_accessToken');
        $returnUrl = urldecode($this->_request->getParam('_ru'));
        if ($accessToken) {
            $url = Luong_Helper_Constant::URL_GETUSERINFO . "?accessToken=" . $accessToken;
            $http = new Zend_Http_Client();
            $http->setUri($url);
            $http->setHeaders('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            $http->request('POST');
            $time = time();
            $str = md5(md5($time . Luong_Helper_Constant::ASK_SECREPT));
            $http->setParameterPost(array(
                'time' => $time,
                'str' => $str,
                'accessToken' => $accessToken,
            ));
            $response = $http->request();
            $response = json_decode($response->getBody());
            if ($response->status == 0) {
                $this->_auth->getStorage()->write($response->data);
                if (!empty($returnUrl)) {
                    $this->_redirect($returnUrl);
                }
            }
        }
        $this->_redirect('/');
    }

}
