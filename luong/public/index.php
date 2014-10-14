<?php
ini_set("display_errors", 0);
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
// Duong dan den thu muc /layouts
define('LAYOUT_PATH', PUBLIC_PATH . "/layouts");
// Duong dan den thu muc layouts
define('LAYOUT_URL', "/layouts");

// Ensure library/ is on include_path, them cac duong dan den cac file model de Zend co the load
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../../luong_library'),
    realpath(APPLICATION_PATH . '/models'),
    realpath(APPLICATION_PATH . '/utils'),
    realpath(APPLICATION_PATH . '/captcha'),
    realpath(APPLICATION_PATH . '/modules'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

$application->bootstrap()->run();
