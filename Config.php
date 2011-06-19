<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Config
 * @link      wwww.ppiframework.com
 */

class PPI_Config {

    /**
     * The config object doing the parsing
     *
     * @var null|PPI_Config_Ini
     */
	protected $_oConfig    = null;

	/**
	 * The configuration options
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Initialise the config object
	 *
	 * Will check the file extension of your config filename and load up a specific parser
	 * @param array $options The options
     *
	 */
	function __construct(array $options = array()) {
		$this->_options = $options;
	}

	/**
	 * Get the current set config object
	 *
	 * @return object
	 */
	function getConfig() {
		if($this->_oConfig === null) {
			if(isset($this->_options['cacheConfig']) && $this->_options['cacheConfig']) {
				$this->_oConfig = $this->cacheConfig();
			} else {
				$this->_oConfig = $this->parseConfig();
			}
		}
		return $this->_oConfig;
	}

	/**
	 * Get a cached version of the framework, if no cached version exists it parses the config and caches it for you.
	 *
	 * @throws PPI_Exception
	 * @return void
	 */
	function cacheConfig() {
		if(!isset($this->_options['configCachePath'])) {
			throw new PPI_Exception('Missing path to the config cache path');
		}

		$path = sprintf('%s%s.%s.cache',
			$this->_options['configCachePath'],
			$this->_options['configFile'],
			$this->_options['configBlock']);

		if(file_exists($path)) {
			return unserialize(file_get_contents($path));
		}
		$config = $this->parseConfig();
		file_put_contents($path, serialize($config));
		return $config;
	}

	/**
	 * Parse the config file
	 *
	 * @return object
	 */
	function parseConfig() {

		// Make sure our config file exists
		if(!file_exists(CONFIGPATH . $this->_options['configFile'])) {
			die('Unable to find <b>'. CONFIGPATH . $this->_options['configFile'] .'</b> file, please check your application configuration');
		}

		// Switch the file extension
		switch(PPI_Helper::getFileExtension($this->_options['configFile'])) {
			case 'ini':
				return new PPI_Config_Ini(parse_ini_file(CONFIGPATH . $this->_options['configFile'], true, INI_SCANNER_RAW), $this->_options['configBlock']);

			case 'xml':
				die('Trying to load a xml config file but no parser yet created.');
				break;

			case 'php':
				die('Trying to load a php config file but no parser yet created.');
				break;

		}
	}

}
