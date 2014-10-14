<?php

class Model_Auth_User_Row extends Zend_Db_Table_Row_Abstract {

    public static $REPORT_PER = array(
        'ROLE_SALE_MANAGER',
        'ROLE_FINANCE_MANAGER',
        'ROLE_FINANCE_STAFF',
    );
    public static $TRANSFER_PER = array(
        'ROLE_SALE_MANAGER',
    );
    public static $SALE_PER = array(
        'ROLE_SALE_MANAGER',
    );
    public static $VIEW_PER = array(
        'ROLE_SALE_MANAGER',
        'ROLE_FINANCE_MANAGER',
        'ROLE_FINANCE_STAFF',
    );
    private $roles;
    private $staff;

    public function __get($columnName) {
        switch ($columnName) {
            case 'roles':
                if (!isset($this->roles)) {
                    $this->roles = array();
                    $userroleModel = new Model_Auth_UserRole();
                    $roleModel = new Model_Auth_Role();

                    $_select = $userroleModel->getAdapter()->select();
                    $_select->from(array('t' => $userroleModel->info('name')), NULL);
                    $_select->joinLeft(array('r' => $roleModel->info('name')), 't.role_id = r.id', array('role' => 'r.name'));
                    $_select->where('t.user_id = ?', $this->id);
                    $_user_roles = $userroleModel->getAdapter()->fetchAll($_select);

                    if (count($_user_roles) > 0) {
                        foreach ($_user_roles as $_user_role) {
                            $this->roles[] = $_user_role->role;
                        }
                    }
                }
                return $this->roles;
            case 'staff':
                if (!isset($this->staff)) {
                    $_staff = $this->findDependentRowset('Model_Sm_Staff');
                    if (!empty($_staff[0]->id)) {
                        $this->staff = $_staff[0];
                    }
                }
                return $this->staff;
            case 'shops_tree':
                if (!Zend_Registry::isRegistered('user_shops_tree')) {
                    Zend_Registry::set('user_shops_tree', $this->_getShopTree());
                }
                return Zend_Registry::get('user_shops_tree');
            default:
                return parent::__get($columnName);
        }
    }

    private function _getShopTree() {
        $shopTree = new JTree();
        $myshops = new JTree();
        if (!empty($this->__get('staff')->shop_id)) {
            $shopModel = new Sale_Models_Sm_Shop();
            $shops = $shopModel->fetchAll();
            foreach ($shops as $_shop) {
                $shopTree->createNode($_shop, $_shop['id'], $_shop['parent_shop_id']);
            }
            $myshops = $shopTree->getChildTree($this->__get('staff')->shop_id);
        }
        return $myshops;
    }

    public function hasPermission($require_roles) {
        if (!is_array($require_roles)) {
            $require_roles = array($require_roles);
        }
        foreach ($this->__get('roles') as $role) {
            if (in_array($role, $require_roles)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function _sameShop($store) {
        return ($store->shop_id == $this->__get('staff')->shop_id);
    }

    private function _hasReportPer($store) {
        return ($this->hasPermission(self::$REPORT_PER) && $this->_sameShop($store));
    }

    private function _hasTransferPer($store) {
        return (empty($store->staff_id) && $this->hasPermission(self::$TRANSFER_PER) && $this->_sameShop($store));
    }

    private function _hasSalePer($store) {
        return (empty($store->staff_id) && $this->hasPermission(self::$SALE_PER) && $this->_sameShop($store));
    }

    private function _hasViewPer($store) {
        return ($this->hasPermission(self::$VIEW_PER) && $this->_sameShop($store));
    }

    public function hasStore($store) {
        return (!empty($store->staff_id) && $store->staff_id == $this->id);
    }

    public function hasReportPer($store) {
        return ($this->hasStore($store) || $this->_hasReportPer($store));
    }

    public function hasTransferPer($store) {
        return ($this->hasStore($store) || $this->_hasTransferPer($store));
    }

    public function hasSalePer($store) {
        return ($this->hasStore($store) || $this->_hasSalePer($store));
    }

    public function hasViewPer($store) {
        return ($this->hasStore($store) || $this->_hasViewPer($store) || $this->_hasSalePer($store) || $this->_hasTransferPer($store) || $this->_hasReportPer($store));
    }

}
