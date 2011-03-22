<?php

/**
 * Autoload class
 *
 * @category  PPI
 * @package   PPI_Autoload
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @copyright 2001-2010 Digiflex Development Team
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link      www.ppiframework.com
*/
class PPI_Autoload {

	static protected $_libraries = array(
		'PPI' => array(
			'path' => SYSTEMPATH,
			'prefix' => 'PPI_'
		),
		'PPI_APP' => array(
			'path' => APPFOLDER,
			'prefix' => 'APP_'
		)
	);

	function __construct() {}

	static function register() {
                spl_autoload_register(array('PPI_Autoload', 'autoload'));
	}

	static function unregister() {
                spl_autoload_unregister(array('PPI_Autoload', 'autoload'));
	}

	static function autoload($className) {
		foreach(self::$_libraries as $lib => $aOptions) {
			$sPrefix = $aOptions['prefix'];
			$sPath = $aOptions['path'];
	                if(strpos($className, $sPrefix) !== false) {
				// Hack for the PPI framework until path generation is delegated off elsewhere.
				// We take off the PPI_ and APP_ from the class name as they're not directly part of the include path
				if($sPrefix == 'PPI_' || $sPrefix == 'APP_') {
		                        $className = substr($className, strlen($sPrefix), strlen($className));
				}
	                }
	                $file = ($sPath . self::convertClassName($className) . '.php');
	                if(file_exists($file)) {
	                        require_once($file);
				break;
	                }
		}
	}

	static function add($key, array $p_aOptions) {
		self::$_libraries[$key] = $p_aOptions;
		if(isset($p_aOptions['path'])) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $p_aOptions['path']);
		}
	}

	static function remove($p_sKey) {
		unset(self::$_libraries[$p_sKey]);
//		isset(self::$_libraries[$p_sKey]) ? unset(self::$_libraries[$p_sKey]) : null;
	}

	static function exists($p_sKey) {
		return isset(self::$_libraries[$p_sKey]);
	}

	static function convertClassName($p_sClassName) {
		return str_replace('_', DS, $p_sClassName);
	}
}
