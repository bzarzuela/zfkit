<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initDoctrine() { 

		$config = $this->getOptions();

		$manager = Doctrine_Manager::getInstance();

		// These are things you can easily include in the config
		// but I always use them anyway.
		$manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$manager->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_CHARSET, 'utf8');
		$manager->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_COLLATE, 'utf8_unicode_ci');
		$manager->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_TYPE, 'INNODB');
		$manager->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);
		$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, false);
		$manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
		$manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);

		if ($config['doctrine']['cache']) {
			// I always use APC
			$cacheDriver = new Doctrine_Cache_Apc();
			$manager->setAttribute(
				Doctrine_Core::ATTR_QUERY_CACHE,
				$cacheDriver
			);
		}

		$conn = Doctrine_Manager::connection($config['doctrine']['dsn']);
		$conn->setCharset('utf8');
		
		if (APPLICATION_ENV == 'development') {
			$profiler = new Doctrine_Connection_Profiler();
			$conn->setListener($profiler);
		}

		return $manager;

	}

	public function _initView()
	{   
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->env = APPLICATION_ENV;
		$config = new Zend_Config_Xml(APPLICATION_PATH."/configs/menu.xml", "nav");
		$nav = new Zend_Navigation($config);
		$view->navigation($nav);
		$view->addHelperPath('Zk/View/Helper', 'Zk_View_Helper');
		$render = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$render->setView($view);
		/*
		$c = $nav->findAllByController('index');
		$a = $nav->findAllByAction("index");
		$found = array_intersect($c, $a);
		*/
		return $view;
	} 

/**
* if you want true zend-framework 'rest'-style URLs
* with all that entails, uncomment this method
*/

/*
public function _initRouting()
{

$this->bootstrap('frontController');
$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();
$restRoute = new Zend_Rest_Route($front);
$front->getRouter()->addRoute('rest', $restRoute);
}
*/

}

