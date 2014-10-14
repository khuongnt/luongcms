<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoload() {
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->registerNamespace('App_');
        $autoLoader->setFallbackAutoloader(true);
        return $autoLoader;
    }

    protected function _initDb() {
        
        /*
        $dbOptionds = $this->getOption('resources');
        $dbOptionds = $dbOptionds['db'];
        $db = Zend_Db::factory($dbOptionds['adapter'], $dbOptionds['params']);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Registry::set('db', $db);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        return $db;
        */
    }
	
	/**
	 * Init View cho Smarty
	 */
	protected function _initView() {
		$viewOption = $this->getOption('resources');
		//$viewRender = Zend_View();
		//$viewRender->setScriptPath($viewOption['view']['basePath']);
		//$view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
		//$view->addScriptPath($viewOption['view']['basePath']);
	}

    protected function _initFrontController() {
        $front = Zend_Controller_Front::getInstance();
        // Dang ki plug in, sau nay se dung
        //$front->registerPlugin(new Sunnet_Controller_Device());
        $front->addModuleDirectory(APPLICATION_PATH . "/modules");
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'thietlap');
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($config, 'routes');
        $front->setRouter($router);
        return $front;
    }
    
    protected function _initMail() {
        /*
    	$mailOption = $this->getOption('resources');
    	$mailOption = $mailOption['mail'];
    	$host 		= $mailOption['host'];
    	$mailOption = $mailOption['info'];
    	$transport 	= new Zend_Mail_Transport_Smtp($host, $mailOption);
    	Zend_Registry::set('mailTransport',$transport);
    	Zend_Mail::setDefaultTransport ( $transport );
        */
    }

}
