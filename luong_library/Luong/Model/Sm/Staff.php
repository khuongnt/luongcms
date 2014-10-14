<?php

class Model_Sm_Staff extends Zend_Db_Table_Abstract {

    private static $STATUS = array(
        0 => 'Không hoạt động',
        1 => 'Hoạt động',
    );

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'sm_staff',
            'referenceMap' => array(
                'User' => array(
                    'columns' => array('user_id'),
                    'refTableClass' => 'Model_Auth_User',
                    'refColumns' => array('id'),
                ),
                'Shop' => array(
                    'columns' => array('shop_id'),
                    'refTableClass' => 'Model_Sm_Shop',
                    'refColumns' => array('id'),
                ),
            ),
            'dependentTables' => array(
                'Model_Sm_Shop',
                'Model_Sm_Store',
            ),
            self::ROW_CLASS => 'Model_Sm_Staff_Row',
        ));
    }

    public static function getStatus() {
        return self::$STATUS;
    }

    public static function statusToText($status) {
        if (array_key_exists($status, self::$STATUS)) {
            return self::$STATUS[$status];
        } else {
            return 'Không xác định';
        }
    }

    public function get_by_roles($role_codes, $where = NULL, $order = NULL, $count = NULL, $offset = NULL) {
        $roleModel = new Model_Auth_Role();
        $userRoleModel = new Model_Auth_UserRole();

        $role_ids = array();
        $roles = $roleModel->fetchAll(array('name IN (?)' => $role_codes));
        if (count($roles) > 0) {
            foreach ($roles as $_userRole) {
                $role_ids[] = $_userRole->id;
            }
        }

        $user_ids = array();
        $userRoles = $userRoleModel->fetchAll(array('role_id IN (?)' => array_unique($role_ids)));
        if (count($userRoles) > 0) {
            foreach ($userRoles as $_userRole) {
                $user_ids[] = $_userRole->user_id;
            }
        }
        $where['user_id IN (?)'] = array_unique($user_ids);

        return $this->get_all($where, $order, $count, $offset);
    }

    public function get_all($where = NULL, $order = NULL, $count = NULL, $offset = NULL) {
        $userModel = new Model_Auth_User();
        $select = $this->_db->select();

        $t_columns = array('shop_id', 'company', 'address', 'description', 'budget_limit');
        $u_columns = array('id', 'user_name', 'first_name', 'middle_name', 'last_name', 'full_name', 'email', 'gender', 'created_date', 'is_verified', 'status', 'user_type');

        $select->from(array('t' => $this->_name), $t_columns);
        $select->join(array('u' => $userModel->info('name')), 't.user_id = u.id', $u_columns);
        $select->where('u.user_type = ?', 4);
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        if (!empty($order)) {
            if (!is_array($order)) {
                $order = array($order);
            }
            foreach ($order as $_spec) {
                $select->order($_spec);
            }
        }
        if ($count != NULL || $offset !== NULL) {
            $select->limit($count, $offset);
        }
        $_users = $this->_db->fetchAll($select);
        $users = array();
        foreach ($_users as $_user) {
            $users[$_user->id] = $_user;
        }
        return $users;
    }

}
