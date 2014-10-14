<?php

/**
 * Application Paging.
 *  
 * @author Nguyen Van Hien
 * @version 1.0
 */
class Luong_Helper_Paging {

    public $itemPerPages = 6;
    public $offset = 6;
    public $display = array('Hiện thị', 'của', 'bản ghi');
    public $navigation = array('First&nbsp;', '&lt;&lt;', '&gt;&gt', '&nbsp;Last');

    /**
     * function: paging.
     * param: $totalRecords, $urlAction, $currentPages, $itemPerPages, $offset.
     * return: links paging.
     */
    public function doPaging($totalRecords = 0, $urlAction = "", $currentPages = 1, $itemPerPages = 0, $offset = 0, $navigation = array(), $class = "", $class2 = "") {
        $class = '';

        if (count($navigation) == 0)
            $navigation = $this->navigation;
        list($a[0], $a[1], $a[2], $a[3]) = $navigation;
        if ($itemPerPages == 0)
            $itemPerPages = $this->itemPerPages;
        if ($offset == 0)
            $offset = $this->offset;
        $totalPages = floor((int) $totalRecords / $itemPerPages) + ((int) $totalRecords % $itemPerPages == 0 ? 0 : 1);
        if ($offset % 2 == 0) {
            $page_offset = floor($offset / 2) - 1;
        } else {
            $page_offset = floor($offset / 2);
        }
        $response = "";
        if ($totalPages > 1) {
            $response.='<div class="pagination"><ul class="pagination">';
            if ($totalPages > $offset) {
                if ($currentPages > $offset - $page_offset) {
                    $response.="<li><a href='" . $urlAction . "?page=1'>" . $a[0] . "</a></li><li><a href='javácript://'>...</a></li>";
                }
            }
            if (($currentPages - 1) > 0) {
                $response.="<li><a href='" . $urlAction . "?page=" . ($currentPages - 1) . "'>" . $a[1] . "</a></li>";
            }
            if ($totalPages > $offset) {
                if ($currentPages < floor($offset / 2) + 1) {
                    $idx_fst = 1;
                    $idx_lst = $offset;
                } elseif ($currentPages > ($totalPages - (floor($offset / 2) + 1))) {
                    $idx_fst = $totalPages - $offset + 1;
                    $idx_lst = $totalPages;
                } else {
                    if ($offset % 2 == 0) {
                        $idx_fst = $currentPages - floor($offset / 2);
                        $idx_lst = $currentPages + (floor($offset / 2) - 1);
                    } else {
                        $idx_fst = $currentPages - floor($offset / 2);
                        $idx_lst = $currentPages + floor($offset / 2);
                    }
                }
                if ($offset == 0) {
                    $idx_fst = 1;
                    $idx_lst = $totalPages;
                }
            } else {
                $idx_fst = max($currentPages - $offset, 1);
                $idx_lst = min($currentPages + $offset, $totalPages);
            }
            for ($i = $idx_fst; $i <= $idx_lst; $i++) {
                if ($i == $currentPages) {
                    $response.='<li class="active"><a tabindex="0">' . $i . '</a></li>';
                } else {
                    $response.='<li><a tabindex="0" href=' . $urlAction . '?page=' . $i . '>' . $i . '</a></li>';
                }
            }
            if (($currentPages + 1) <= $totalPages) {
                $response.="<li><a href='" . $urlAction . "?page=" . ($currentPages + 1) . "'>" . $a[2] . "</a></li>";
            }
            if ($totalPages > $offset) {
                if ($currentPages <= $totalPages - $offset + $page_offset) {
                    $response.="<li><a href='javácript://'>...</a></li><li><a class='$class' href='" . $urlAction . "?page=$totalPages'>" . $a[3] . "</a></li>";
                }
            }
            $response.="</ul></div>";
        }
        return $response;
    }

    public function doPagingSearch($totalRecords = 0, $urlAction = "", $currentPages = 1, $itemPerPages = 0, $offset = 0, $navigation = array(), $class = "", $class2 = "") {
        $class = 'active';

        if (count($navigation) == 0)
            $navigation = $this->navigation;
        list($a[0], $a[1], $a[2], $a[3]) = $navigation;
        if ($itemPerPages == 0)
            $itemPerPages = $this->itemPerPages;
        if ($offset == 0)
            $offset = $this->offset;
        $totalPages = floor((int) $totalRecords / $itemPerPages) + ((int) $totalRecords % $itemPerPages == 0 ? 0 : 1);
        if ($offset % 2 == 0) {
            $page_offset = floor($offset / 2) - 1;
        } else {
            $page_offset = floor($offset / 2);
        }
        $response = "";
        if ($totalPages > 1) {
            $response.='<div class="pagination">';
            if ($totalPages > $offset) {
                if ($currentPages > $offset - $page_offset) {
                    $response.="<li><a class='$class' href='" . $urlAction . "&page=1'>" . $a[0] . "</a></li>&nbsp;...&nbsp;";
                }
            }
            if (($currentPages - 1) > 0) {
                $response.="<li><a class='$class' href='" . $urlAction . "&page=" . ($currentPages - 1) . "'>" . $a[1] . "</a></li>";
            }
            if ($totalPages > $offset) {
                if ($currentPages < floor($offset / 2) + 1) {
                    $idx_fst = 1;
                    $idx_lst = $offset;
                } elseif ($currentPages > ($totalPages - (floor($offset / 2) + 1))) {
                    $idx_fst = $totalPages - $offset + 1;
                    $idx_lst = $totalPages;
                } else {
                    if ($offset % 2 == 0) {
                        $idx_fst = $currentPages - floor($offset / 2);
                        $idx_lst = $currentPages + (floor($offset / 2) - 1);
                    } else {
                        $idx_fst = $currentPages - floor($offset / 2);
                        $idx_lst = $currentPages + floor($offset / 2);
                    }
                }
                if ($offset == 0) {
                    $idx_fst = 1;
                    $idx_lst = $totalPages;
                }
            } else {
                $idx_fst = max($currentPages - $offset, 1);
                $idx_lst = min($currentPages + $offset, $totalPages);
            }
            for ($i = $idx_fst; $i <= $idx_lst; $i++) {
                if ($i == $currentPages) {
                    $response.='<a tabindex="0" class="fg-button ui-button ui-state-default ui-state-disabled">' . $i . '</a>';
                } else {
                    $response.='<li><a tabindex="0" class="' . $class . '" href=' . $urlAction . '&page=' . $i . '>' . $i . '</a></li>';
                }
            }
            if (($currentPages + 1) <= $totalPages) {
                $response.="<li><a class='$class' href='" . $urlAction . "&page=" . ($currentPages + 1) . "'>" . $a[2] . "</a></li>";
            }
            if ($totalPages > $offset) {
                if ($currentPages <= $totalPages - $offset + $page_offset) {
                    $response.="&nbsp;...&nbsp;<li><a class='$class' href='" . $urlAction . "&page=$totalPages'>" . $a[3] . "</a></li>";
                }
            }
            $response.="</div>";
        }
        return $response;
    }

    /**
     * function: getSeparatorItemOnPage.
     * param: $totalRecords, $currentPages, $itemPerPages.
     * return: out of record.
     */
    public function doPageSeparator($totalRecords = 0, $currentPages = 0, $itemPerPages = 0, $info = array()) {
        $response = "";
        if (count($info) == 0)
            $info = $this->display;
        list($a[0], $a[1], $a[2]) = $info;
        $totalPages = floor((int) $totalRecords / $itemPerPages) + ((int) $totalRecords % $itemPerPages == 0 ? 0 : 1);
        $item_fst = $currentPages * $itemPerPages - $itemPerPages + 1;
        if ($currentPages == $totalPages) {
            $item_lst = $totalRecords;
        } else {
            $item_lst = $currentPages * $itemPerPages;
        }
        if ($totalRecords > 0) {
            $response.='<div class="dataTables_info" id="jtable_info" style="margin-top:0px">' . $a[0] . " <b>" . $item_fst . "</b> - <b>" . $item_lst . "</b> " . $a[1] . " <b>" . $totalRecords . "</b> " . $a[2] . '</div>';
        }
        return $response;
    }

}
