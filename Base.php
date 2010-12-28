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

	/**
	 * Register the autoload function
	 *
	 */
	public static function registerAutoload() {
		spl_autoload_register(array('PPI_Base', 'autoload'));
	}

	/**
	 * Unregister the autoload function
	 *
	 */
	public static function unregisterAutoload() {
		spl_autoload_unregister(array('PPI_Base', 'autoload'));
	}

	/**
	 * The autoload function, set by spl_autoload_register
	 *
	 * @param string $className The class name
	 */
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
	
	/**
	 * Set the error nad exception handlers
	 *
	 * @param string $p_sErrorHandler The error handler function name
	 * @param string $p_sExceptionHandler The exception handler function name
	 */
	static function setErrorHandlers($p_sErrorHandler = null, $p_sExceptionHandler = null) {
		if($p_sErrorHandler !== null) {
			set_error_handler($p_sErrorHandler, E_ALL);
		}
		if($p_sExceptionHandler !== null) {
			set_exception_handler($p_sExceptionHandler);
		}
	}

}
