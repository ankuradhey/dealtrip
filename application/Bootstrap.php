<?php

    defined('APPLICATION_PATH') or define('APPLICATION_PATH', dirname(__FILE__));
    include_once(APPLICATION_PATH . 'application/configs/config.inc.php');
    include_once(APPLICATION_PATH . 'application/configs/general.php');
    defined('APPLICATION_ENVIRONMENT') or define('APPLICATION_ENVIRONMENT', 'development');

    //date_default_timezone_set('Europe/London');
    date_default_timezone_set('UTC');
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);
//    error_reporting(0);

    $frontController = Zend_Controller_Front::getInstance();
    $loader = Zend_Loader_Autoloader::getInstance();
    $frontController->setControllerDirectory(APPLICATION_PATH . 'application/controllers');

    $frontController->setParam('env', APPLICATION_ENVIRONMENT);

    Zend_Layout::startMvc(APPLICATION_PATH . 'application/layouts/scripts', false, "layout");
    Zend_Controller_Action_HelperBroker::addPath('application/helpers/', 'Zend_Controller_Action_Helper_');


    $configuration = new Zend_Config_Ini(APPLICATION_PATH . 'application/configs/application.ini', 'development');
    $dbAdapter = Zend_Db::factory($configuration->database);


    $dbAdapter->query("SET NAMES 'utf8'");
    Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);


    $registry = Zend_Registry::getInstance();
    $registry->configuration = $configuration;
    $registry->dbAdapter = $dbAdapter;

    $view = new Zend_View();
    $view->headTitle('Dealatrip')->setSeparator(' - ');
    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
    /*
      Ponky Meta code here
      ----------------------------------- */

    /* gcm($registry);
      prd($frontController->getResource());
      $view = $frontController->getResource('view'); */

    /* $view->keywords = 'default keywords'; */



    $fcontroller = Zend_Controller_Front::getInstance();
    $router = $fcontroller->getRouter();
    $appRoutes = array();
    $appRoutes['country'] = new Zend_Controller_Router_Route('/search/property/:country', array('module' => 'default', 'controller' => 'search', 'action' => 'index'));
    $appRoutes['state'] = new Zend_Controller_Router_Route('/search/property/:country/:state', array('module' => 'default', 'controller' => 'search', 'action' => 'index'));
    $appRoutes['city'] = new Zend_Controller_Router_Route('/search/property/:country/:state/:city', array('module' => 'default', 'controller' => 'search', 'action' => 'index'));
    $appRoutes['sub_area'] = new Zend_Controller_Router_Route('/search/property/:country/:state/:city/:sub_area', array('module' => 'default', 'controller' => 'search', 'action' => 'index'));
    $appRoutes['local_area'] = new Zend_Controller_Router_Route('/search/property/:country/:state/:city/:sub_area/:local_area', array('module' => 'default', 'controller' => 'search', 'action' => 'index'));


    $appRoutes['static_pages'] = new Zend_Controller_Router_Route(
                    'page/:slug', array(
                'action' => 'pages',
                'controller' => 'contents',
                'module' => 'default'
                    ));
    
    $appRoutes['location_page_country'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)', array(
                'action' => 'index',
                'controller' => 'location',
                'module' => 'default'
                    ), array(
                1 => 'country'
            ));

    $appRoutes['location_page_state'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)', array(
                'action' => 'index',
                'controller' => 'location',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state'
            ));

    $appRoutes['location_page_city'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)', array(
                'action' => 'index',
                'controller' => 'location',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city'
            ));

    $appRoutes['location_page_subarea'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)', array(
                'action' => 'index',
                'controller' => 'location',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area'
            ));

    $appRoutes['location_page_localarea'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+)', array(
                'action' => 'index',
                'controller' => 'location',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area',
                5 => 'local_area'
            ));

    $appRoutes['preview'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+DAT.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'property_type',
                5 => 'property_code'
            ));

    $appRoutes['preview_sub_area'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+)/(.+DAT.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area',
                5 => 'property_type',
                6 => 'property_code'
            ));

    $appRoutes['preview_local_area'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)/(.+DAT.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area',
                5 => 'local_area',
                6 => 'property_type',
                7 => 'property_code'
            ));


    $appRoutes['preview_tab'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+DAT.+)/(.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'property_type',
                5 => 'property_code',
                6 => 'tab'
            ));

    $appRoutes['preview_sub_area_tab'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+)/(.+DAT.+)/(.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area',
                5 => 'property_type',
                6 => 'property_code',
                7 => 'tab'
            ));

    $appRoutes['preview_local_area_tab'] = new Zend_Controller_Router_Route_Regex(
                    'holiday-rentals/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)', array(
                'action' => 'searchdetail',
                'controller' => 'search',
                'module' => 'default'
                    ), array(
                1 => 'country',
                2 => 'state',
                3 => 'city',
                4 => 'sub_area',
                5 => 'local_area',
                6 => 'property_type',
                7 => 'property_code',
                8 => 'tab'
            ));




    foreach ($appRoutes as $key => $cRouter)
    {
        $router->addRoute($key, $cRouter);
    }
//prd($fcontroller->getPlugins());

    $fcontroller->dispatch();


    unset($frontController, $view, $configuration, $dbAdapter, $registry);

    function gcm($var)
    {
        echo $var;
        exit;
        if (is_object($var))
            $var = get_class($var);
        echo '<pre>';
        prn(get_class_methods($var));
        echo '</pre>';
    }

    function prn($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    function prepareQuery($args)
    {
        $sql = $args[0];
        $_sqlSplit = preg_split('/(\?|\:[a-zA-Z0-9_]+)/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $params = 0;
        foreach ($_sqlSplit as $key => $val)
        {
            if ($val == '?')
            {
                $_sqlSplit[$key] = $args[1][$params];
                $params++;
            }
        }

        $query = implode($_sqlSplit);
        return($query);
    }