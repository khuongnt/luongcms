<?php
class Admin_Models_Catpartner extends Zend_Db_Table_Abstract {
	protected $_name = 'partner_category';
	protected $_id = 'id';
	protected $_db;

	protected function _init() {
		$this->_db =  Zend_Registry::get('db');
	}
	
	public function getTotalRecords() {
		$sql = $this->_db->select()
		->from(array('s' => $this->_name), array('count(*) as total'));
		return $this->_db->fetchCol($sql);
	}
	
	public function getList($status, $currentPages, $itemPerPage) {
		$offset = ($currentPages - 1) * $itemPerPage;
		$sql = $this->_db->select()
		->from(array('s' => $this->_name))
		->order('thutu ASC')
		->limit($itemPerPage, $offset);
		return $this->_db->fetchAll($sql);
	}
	
	public function getAll($status) {
		$sql = $this->_db->select()
		->from(array('s' => $this->_name))->where('status = ?', $status)
		->order('thutu ASC');
		return $this->_db->fetchAll($sql);
	}
}