<?php

class Luong_Auth_Result extends Zend_Auth_Result {

    /**
     * General Failure
     */
    const FAILURE = 0;

    /**
     * Failure due to identity not being found.
     */
    const FAILURE_IDENTITY_NOT_FOUND = -1;

    /**
     * Failure due to identity being ambiguous.
     */
    const FAILURE_IDENTITY_AMBIGUOUS = -2;

    /**
     * Failure due to invalid credential being supplied.
     */
    const FAILURE_CREDENTIAL_INVALID = -3;

    /**
     * Failure due to permission denied reasons.
     */
    const FAILURE_PERMISSION_DENIED = -4;

    /**
     * Failure due to uncategorized reasons.
     */
    const FAILURE_UNCATEGORIZED = -9;

    /**
     * Authentication success.
     */
    const SUCCESS = 1;

    protected $_data = NULL;

    public function getData() {
        return $this->_data;
    }

    public function __construct($code, $identity, array $messages = array(), $data = NULL) {
        if (!empty($data)) {
            $this->_data = $data;
        }
        parent::__construct($code, $identity, $messages);
    }

}
