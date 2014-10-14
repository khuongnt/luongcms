<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoLoad() {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH . '/modules/admin',
            'namespace' => 'Admin',
        ));

        $loader->addResourceType('form', 'forms', 'Form')
                ->addResourceType('model', 'models', 'Models');
        return $loader;
    }

}
