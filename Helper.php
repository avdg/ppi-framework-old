<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Helper
 * @link      www.ppiframework.com
 */
class PPI_Helper {

	/**
	 *
	 *
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * The initialise function to create the instance
	 * @return void
	 */
	protected static function init() {
		self::setInstance(new PPI_Helper());
	}


	/**
	 * The function used to initially set the instance
	 *
	 * @param PPI_Helper $instance
	 * @throws PPI_Exception
	 * @return void
	 */
	static function setInstance(PPI_Helper $instance) {
		if (self::$_instance !== null) {
			throw new PPI_Exception('PPI_Helper is already initialised');
		}
		self::$_instance = $instance;
	}

	/**
	 * Obtain the instance if it exists, if not create it
	 *
	 * @return PPI_Helper
	 */
	static function getInstance() {
		if (self::$_instance === null) {
			self::init();
		}
		return self::$_instance;
	}

	/**
	 * Function to recursively trim strings
	 *
	 * @param mixed $input The input to be trimmed
	 * @return mixed
	 */
	function arrayTrim($input){
		if (!is_array($input)) {
			return trim($input);
		}
		return array_map(array($this, 'arrayTrim'), $input);
	}

	/**
	 * Get the config object
	 *
	 * @return object
	 */
	static function getConfig() {
		return PPI_Registry::getInstance()->get('PPI_Config');
	}

	/**
	 * Get the dispatcher object
	 *
	 * @return object
	 */
	static function getDispatcher() {
		return PPI_Registry::getInstance()->get('PPI_Dispatch');
	}

	/**
	 * Get the view object
	 *
	 * @static
	 * @return mixed
	 */
	static function getView() {
		return self::getObjectFromRegistry('PPI_View');
	}

	/**
	 * Get the session object
	 *
	 * @param mixed $p_mOptions The information to get a different cache object
	 * @return PPI_Session
	 */
	static function getSession($p_mOptions = null) {
		if (!is_array($p_mOptions)) {
			$config  = self::getConfig();
			$options = isset($config->session) ? $config->session->toArray() : array();
			$p_mOptions = $options;
		}
		return new PPI_Session($p_mOptions);
	}

	/**
	 * Get the cache object
	 *
	 * @param mixed $p_mOptions The information to get a different cache object
	 * @return PPI_Cache
	 */
	static function getCache($p_mOptions = null) {
		if (!is_array($p_mOptions)) {
			$config  = self::getConfig();
			$options = isset($config->cache) ? $config->cache->toArray() : array();
			if (is_string($p_mOptions) && $p_mOptions !== '') {
				$options['handler'] = $p_mOptions;
			}
			$p_mOptions = $options;
		}
		return new PPI_Cache($p_mOptions);
	}

	/**
	 * Get the router object
	 *
	 * @return object
	 */
	static function getRouter() {
		return self::getObjectFromRegistry('PPI_Router');
	}

	/**
	 * Get the router object
	 *
	 * @return object
	 */
	static function getSecurity() {
		return self::getObjectFromRegistry('PPI_Security');
	}

	static function getObjectFromRegistry($p_sClass) {
		$registry = PPI_Registry::getInstance();
		if(!$registry->exists($p_sClass)) {
		    $oClass = new $p_sClass();
			$registry->set($p_sClass, $oClass);
			return $oClass;
		}
		return $registry->get($p_sClass);
	}

	/**
	 * Get the registry object
	 *
	 * @static
	 * @return object
	 */
	static function getRegistry() {
		return PPI_Registry::getInstance();
	}

	/**
	 * Get the PPI_Request object cached from the registry
	 *
	 * @static
	 * @return mixed
	 */
	static function getRequest() {
		return self::getObjectFromRegistry('PPI_Request');

	}

	/**
	 * Get the PPI_Response object cached from the registry
	 *
	 * @static
	 * @return mixed
	 */
	static function getResponse() {
		return self::getObjectFromRegistry('PPI_Response');

	}

	/**
	 * Obtain the extension from a filename
	 *
     * @todo Move to PPI_File
	 * @todo Convert to pathinfo
	 * @param string $fileName The file's filename
	 * @return string
	 */
	static function getFileExtension($fileName, $includeDot = false) {
		$i = strrpos($fileName,".");
		if ($i === false) { return ''; }
		$l = strlen($fileName) - $i;
		return strtolower((($includeDot === true) ? '.' : '') . substr($fileName, $i+1, $l));
	}

	/**
	 * Check if a file has a particular extension. If it does not have the extension then we add it.
     *
	 * @todo Make this look for the last "." then take it from there so we're not vulnerable to file.php.txt
	 * @param string $p_sTemplateFile Filename
	 * @param string $p_sExtension Extension
	 * @return string
	 */
	static function checkExtension($p_sTemplateFile, $p_sExtension) {
		if(strripos($p_sTemplateFile, $p_sExtension) === false) {
			$p_sTemplateFile .= $p_sExtension;
		}
		return $p_sTemplateFile;
	}

	/**
	 * Get the current full url
     *
	 * @todo match this with $this->getCurrUrl()
	 * @return string
	 */
	static function getFullUrl() {
		$sProtocol  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		return $sProtocol . '://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];
	}

	static function getCurrUrl() {
		return rtrim($_SERVER['REQUEST_URI'], '/') . '/';
	}

	static function getFileType($fileName) {
		if (substr($fileName, 0, 7) == 'http://') {
			return 'http';
		} elseif (substr($fileName, 0, 8) == 'https://') {
			return 'https';
		} elseif (substr($fileName, 0, 6) == 'ftp://') {
			return 'ftp';
		} elseif (substr($fileName, 0, 7) == 'ftps://') {
			return 'ftps';
		} elseif (is_file($fileName)) {
			return 'file';
		} elseif (is_dir($fileName)) {
			return 'directory';
		} else {
			return 'unknown';
		}
	}
}
