<?php

class Model_Vc_Recharge_History extends Zend_Db_Table_Abstract {

    const CHANNEL_CALL = 0;
    const CHANNEL_SMS = 1;
    const CHANNEL_WEB = 2;

    public function init() {
        $this->setOptions(array(
            'db' => Zend_Registry::get('db'),
            'name' => 'vc_recharge_history',
        ));
    }

    public static function channelToText($channel) {
        switch ($channel) {
            case self::CHANNEL_CALL:
                return 'Tổng đài';
            case self::CHANNEL_SMS:
                return 'SMS';
            case self::CHANNEL_WEB:
                return 'Website';
            default:
                return 'Không xác định';
        }
    }

    public function get_details($where = NULL, $order = NULL, $count = NULL, $offset = NULL, $countOnly = false) {
        $storeGoodModel = new Model_Sm_Store_Good();
        $goodModel = new Model_Sm_Good();
        $select = $this->_db->select();
        if ($countOnly) {
            $t_columns = array('total' => 'COUNT(*)');
            $s_columns = NULL;
            $g_columns = NULL;
        } else {
            $t_columns = array(
                'id',
                'phone_number',
                'serial',
                'date_time',
                'amount',
                'channel',
                'status',
            );
            $s_columns = NULL;
            $g_columns = array('good_name' => 'name');
        }
        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('s' => $storeGoodModel->info('name'))
                , 't.serial = s.serial'
                , $s_columns
        );
        $select->joinLeft(
                array('g' => $goodModel->info('name'))
                , 's.good_id = g.id'
                , $g_columns
        );
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->order($order);
        if ($count !== NULL || $offset !== NULL) {
            $select->limit($count, $offset);
        }
        if ($countOnly) {
            $count = $this->_db->fetchRow($select);
            if (empty($count->total)) {
                $count->total = 0;
            }
            return $count->total;
        } else {
            return $this->_db->fetchAll($select);
        }
    }

    public function get_recharge_count($where = NULL, $order = NULL) {
        $storeGoodModel = new Model_Sm_Store_Good();
        $goodModel = new Model_Sm_Good();
        $select = $this->_db->select();

        $t_columns = array(
            'date_time' => 'DATE(date_time)',
            'recharge_count' => 'COUNT(*)',
        );
        $s_columns = array('good_id');
        $g_columns = array('good_name' => 'name');

        $select->from(
                array('t' => $this->_name)
                , $t_columns
        );
        $select->joinLeft(
                array('s' => $storeGoodModel->info('name'))
                , 't.serial = s.serial'
                , $s_columns
        );
        $select->joinLeft(
                array('g' => $goodModel->info('name'))
                , 's.good_id = g.id'
                , $g_columns
        );
        if (!empty($where)) {
            if (!is_array($where)) {
                $where = array($where);
            }
            foreach ($where as $_cond => $_value) {
                $select->where($_cond, $_value);
            }
        }
        $select->group('DATE(date_time)');
        $select->group('g.name');
        $select->order($order);
        return $this->_db->fetchAll($select);
    }

}
