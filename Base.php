<?php
	/**
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) Digiflex Development Team
	 * @version 1.0
	 * @author Paul Dragoonis <dragoonis@php.net>
	 * @since Version 1.0
	 */

class PPI_Base {
	
	/**
	 * Class Name Instances
	 * @var object $_instances
	 */
	protected static $_instances = array();

	function __construct($p_aArguments = "") {
	}

	public static function registerAutoload() {
		spl_autoload_register(array('PPI_Base', 'autoload'));
	}

	public static function unregisterAutoload() {
		spl_autoload_unregister(array('PPI_Base', 'autoload'));
	}

	static function autoload($className) {
		$sPath = '';
		if(strpos($className, 'PPI_') !== false) {
			$className = substr($className, 4, strlen($className));
			$sPath = SYSTEMPATH;
		} elseif(strpos($className, 'APP_') !==  false) {
			$sPath = APPFOLDER;
			$className = substr($className, 4, strlen($className));
		}
		$file = ($sPath . str_replace('_', DS, $className) . '.php');
		if(file_exists($file)) {
			require_once ($file);
		}
	}
	
	static function setErrorHandlers($p_sErrorHandler = null, $p_sExceptionHandler = null) {
		if($p_sErrorHandler !== null) {
			set_error_handler($p_sErrorHandler, E_ALL);
		}
		if($p_sExceptionHandler !== null) {
			set_exception_handler($p_sExceptionHandler);
		}
	}

	static function boot($p_oSite = null) {
		$newConfigFile =  null;
		if($p_oSite !== null && $p_oSite instanceof PPI_Site) {
			if($p_oSite->_configFile !== null) {
				$newConfigFile = $p_oSite->_configFile;
			}
		} else {

		}
		// Set the config into the registry
		$oConfig = PPI_Model_Config::getInstance()->getConfig($newConfigFile);
		PPI_Registry::getInstance()->set('PPI_Config', $oConfig);

		// ------------- Initialise the session -----------------
		if(!headers_sent()) {
			if(!isset($oConfig->system->sessionNamespace)) {
				die('Required config value not found. system.sessionNamespace');
			} else {
				session_name($oConfig->system->sessionNamespace);
			}
			session_start();
			PPI_Registry::getInstance()->set('PPI_Session', new PPI_Session());
		}

		// ---------------- CHECK FOR MAINTENANCE MODE ------------------
		if(isset($oConfig->system->maintenance) && $oConfig->system->maintenance) {
			include_once(SYSTEMPATH.'errors/maintenance.php');
			exit;
		}

		$dispatch = new PPI_Dispatch($p_oSite === null ? new PPI_Dispatch_Helper_Standard() : $p_oSite->_dispatcher);
		// Set the dispatch object in the registry for future use.
		PPI_Registry::getInstance()->set('PPI_Dispatch', $dispatch);
		
		$dispatch->dispatch();

	}
}
