<?php
/**
 * The PPI Autoloader
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
 */
class PPI_Autoload {

	protected static $_classes = array();

	/**
	 * The base list of libraries to check in the autoloader, these are the base two ones required
	 * for the framework and the skeleton app classes to be autoloaded
	 *
	 * @var array
	 */
	static protected $_libraries = array();


	function __construct() {}

	/**
	 * Register The PPI Autoload Function
	 *
	 * @return void
	 */
	static function register() {
		spl_autoload_register(array('PPI_Autoload', 'autoload'));
	}

	/**
	 * Unregister The PPI Autoload Function
	 *
	 * @return void
	 */
	static function unregister() {
		spl_autoload_unregister(array('PPI_Autoload', 'autoload'));
	}

	/**
	 * The actual autoloading function
	 *
	 * @param string $className The Class Name To Be Autoloaded
	 * @return void
	 */
	static function autoload($className) {

		$file = self::find($className);
		if($file !== '') {
			require($file);
		}
	}

	static function find($className) {

		if(isset(self::$_classes[$className])) {
			return self::$_classes[$className];
		}

		foreach(self::$_libraries as $lib => $aOptions) {
			$sPrefix   = $aOptions['prefix'];
			$sPath     = $aOptions['path'];

			// Check for valid prefix here
			if(strpos($className, $sPrefix) !== false) {

				// Convert Zend_Mail to just Mail and Solar_View to just view
				$file = $sPath . self::convertClassName(str_replace($sPrefix, '', $className));

				if(file_exists($file)) {
					self::$_classes[$className] = $file;
					return $file;
				}
			}

		}
		return '';
	}

	/**
	 * Add a library to the autoloader
	 *
	 * @example
	 * PPI_Autoload::add('Zend', array(
	 *     'path' => SYSTEMPATH . 'Vendor/',
	 *     'prefix' => 'Zend_'
	 * ));
	 *
	 * @todo This appears to be setting the include path to /Vendor/ whereas it should be
	 *       setting it to /Vendor/Zend/ or /Vendor/Solar/
	 *
	 * @param string $key The Key, This is used for exists() and remove()
	 * @param array $p_aOptions
	 */
	static function add($key, array $p_aOptions) {
		self::$_libraries[$key] = $p_aOptions;
/*
		if(isset($p_aOptions['path'])) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $p_aOptions['path']);
		}
*/
	}

	/**
	 * Remove a library from the autoloader
	 *
	 * @param string $p_sKey The key
	 * @return void
	 */
	static function remove($p_sKey) {
		unset(self::$_libraries[$p_sKey]);
//		isset(self::$_libraries[$p_sKey]) ? unset(self::$_libraries[$p_sKey]) : null;
	}


	/**
	 * Checks if a library has been added
	 *
	 * @param string $p_sKey The key
	 * @return boolean
	 */
	static function exists($p_sKey) {
		return isset(self::$_libraries[$p_sKey]);
	}

	/**
	 * Converts the class name to a file path, currently only PEAR naming convention is supported
	 * EG: PPI_Cache_Disk => PPI/Cache/Disk.php
	 *
	 * @param string $p_sClassName The class name
	 * @return string
	 */
	static function convertClassName($p_sClassName, $ret = false) {
		return strtr($p_sClassName, '_', DS) . '.php';
	}
}
