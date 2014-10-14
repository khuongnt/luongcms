<?php
class Admin_Models_Partner extends Zend_Db_Table_Abstract {
	protected $_name = 'partner';
	protected $_id = 'id';
	protected $_db;

	protected function _init() {
		$this->_db =  Zend_Registry::get('db');
	}
	
	public function getTotalRecords($cat_id) {
		$sql = $this->_db->select()
		->from(array('s' => $this->_name), array('count(*) as total'));
		if($cat_id !=0)
			$sql->where('s.cat_id = ?', $cat_id);
		return $this->_db->fetchCol($sql);
	}
	
	public function getList($status, $currentPages, $itemPerPage, $cat_id) {
		$offset = ($currentPages - 1) * $itemPerPage;
		$sql = $this->_db->select()
		->from(array('s' => $this->_name));
		if($cat_id !=0)
			$sql->where('s.cat_id = ?', $cat_id);
		$sql->order('thutu DESC') ->limit($itemPerPage, $offset);
		return $this->_db->fetchAll($sql);
	}
}