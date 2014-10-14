<?php
    $mongo = new MongoClient("mongodb://luong:luongmongo!#$@localhost:27017/luong", array('connect' => true));
    $dbname = $mongo->luong;
    $postModel = $dbname->luong_posts;
    $tagModel = $dbname->luong_tags;
    $inserted = 0;
    $link = 'http://vnexpress.net/rss/cuoi.rss';
    if (isset($link)) {
        $rss = simplexml_load_file($link);
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
                $content = strip_tags($content, '<p><strong><img><em><i><br>');
                //$content = preg_replace('#<div class="embed-container">(.*?)</div>#', '', $content);
                ///$content = preg_replace('/<div id="video[^>]+"\>/i', "", $content);
                //$content = preg_replace('/<div style="text-align:center;">/i', "", $content);
                /*$content = preg_replace("/<\\/?a(\\s+.*?>|>)/", "", $content);*/
                $count = strlen($content);

                preg_match('/<div class=\"block_tag width_common space_bottom_20">(.*?)<div>/s', $content_link, $tag);
                if(!empty($tag[0])){
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
                }
                if (!empty($tags_name)) {   
                    $tags_arr = array();
                    foreach ($tags_name as $tag) {
                        if(!empty($tag)){
                            $tag = str_replace('.', '', $tag);
                            $tag_slug =  cv2urltitle($tag); 
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
                
                $thumb_ex = $item->description;
                preg_match( '@src="([^"]+)"@' , $thumb_ex, $matches );
                $thumb = array_pop($matches);
                $thumb = str_replace('_180x108', '', $thumb);
                $slug = cv2urltitle($title);
                $slug = utf8_decode($slug); 
                $checkpost = $postModel->findOne(array('slug' => $slug));
                if (empty($checkpost) && $count > 300) {
                    $newData = array(
                        'title' => $title,
                        'slug' => $slug,
                        'thumb' => $thumb,
                        'cat' => '542af441d445bffc080001a1',
                        'type' => 'post',
                        'excerpt' => $excerpt,
                        'content' => $content,
                        'tags' => $tags_arr,
                        'time' => time(),
                        'date' => (int) date("ymd"),
                        'source' => 'VnExpress',
                    );
                    $postModel->insert($newData);
                    $inserted++;
                    echo 'insert record '.$inserted.' = true <br />';
                }

            } 
            //
        } // for each
        echo 'insert '.$inserted.' record';
    }  
    // link 
    function cv2urltitle($text) {
    $text = str_replace(
            array(' ', '%', "/", "\\", '"', '?', '<', '>', "#", "^", "`", "'", "=", "!", ":", ",,", "..", "*", "&", "__", "▄"), array('-', '', '', '', '', '', '', '', '', '', '', '', '-', '', '-', '', '', '', "_", "", ""), $text);
    $text = str_replace(
            array('_quot;', '”', '“', ',', '.'), array('', '', '', '', ''), $text);
    $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");
    $uni[0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �");
    $uni[1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");
    $uni[2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ");
    $uni[3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");
    $uni[4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");
    $uni[5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");
    $uni[6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ");
    $uni[7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");
    $uni[8] = array("í", "ì", "ị", "ỉ", "ĩ");
    $uni[9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");
    $uni[10] = array("đ");
    $uni[11] = array("Đ");
    $uni[12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");
    $uni[13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");
    for ($i = 0; $i <= 13; $i++) {
        $text = str_replace($uni[$i], $chars[$i], $text);
    }
    return strtolower($text);
}
?>