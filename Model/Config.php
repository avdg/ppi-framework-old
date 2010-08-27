<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 * @subpackage core
 */

class PPI_Model_Config extends PPI_Base {

	private $bRead       = null;
	private $_oConfig    = null;
	private $_configFile = null;

    /**
     * Registry object provides storage for shared objects.
     * @var PPI
     */
    private static $_instance = null;

    /**
     * Initialize the default registry instance.
     *
     * @return void
     */
    protected static function init()
    {
        self::setInstance(new PPI_Model_Config());
    }

    /**
     * Set the default registry instance to a specified instance.
     *
     * @param PPI $registry An object instance of type PPI,
     *   or a subclass.
     * @return void
     * @throws PPI_Exception if registry is already initialized.
     */
    public static function setInstance(PPI_Model_Config $instance)
    {
        if (self::$_instance !== null) {
            throw new PPI_Exception('Registry is already initialized');
        }

        self::$_instance = $instance;
    }
    /**
     * Retrieves the default registry instance.
     * @return PPI_Model_Config
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::init();
        }

        return self::$_instance;
    }

	function __construct() {}

	function getConfig($p_sConfigFile = null) {
		$this->_configFile = $p_sConfigFile !== null ? $p_sConfigFile : 'general.ini';
		if ($this->bRead === null) {
			$this->readConfig();
			$this->bRead = true;
		}
		return $this->_oConfig;
	}

	function readConfig () {
		global $siteTypes;
		if(!file_exists(CONFIGPATH . $this->_configFile)) {
			die('Unable to find '. $this->_configFile .' file, please check your application configuration');
		}
		$ext = PPI_Helper::getFileExtension($this->_configFile);
		$sHostname = getHTTPHostname();
		$siteType = array_key_exists($sHostname, $siteTypes) ? $siteTypes[$sHostname] : 'development';
		switch($ext) {
			case 'ini':
				$this->_oConfig = new PPI_Config_Ini(parse_ini_file(CONFIGPATH . $this->_configFile, true), $siteType);
				break;

			case 'xml':
				die('Trying to load a xml config file but no parser yet created.');
				break;

			case 'php':
				die('Trying to load a php config file but no parser yet created.');
				break;

		}
	}

	function getRoleNameFromID($p_roleID) {
		foreach($this->roleMapping as $key => $val) {
			if($val == $p_roleID) {
				return $key;
			}
		}
		return '';
	}
	function getRoleIDFromName($p_roleName) {
		foreach($this->_oConfig->system->roleMapping as $key => $val) {
			if($key == $p_roleName) {
				return $val;
			}
		}
		return '';
	}
	function getDefaultViewLanguage($name = false) {
		if(!$name) {
			return $this->_oConfig->layout->default_view_language;
		} else {
			return $this->languageCodeToName($this->_oConfig->layout->default_view_language);
		}
	}
	function languageCodeToName($code) {
		if(array_key_exists($code, $this->_oConfig->layout->languages)) {
			return $this->languages[$code];
		}
		die("Language code not found in config View Languages. Code: $code");
	}
	function languageNameToCode($name) {
		foreach($this->_oConfig->layout->languages as $langkey => $langval) {
			if($this->_oConfig->layout->languages[$key] == $name) {
				return $langval;
			}
		}
		die("Language name not found in config View Languages. Name: $name");

	}
}