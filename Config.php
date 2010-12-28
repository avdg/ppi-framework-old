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

class PPI_Config {
	
	private $_oConfig    = null;
	private $_configFile = null;

	
	/**
	 * Initialise the config object
	 *
	 * Will check the file extension of your config filename and load up a specific parser 
	 * @param string $p_sConfigFile The config filename
	 * @param array $p_aOptions The options
	 */
	function __construct($p_sConfigFile, $p_aOptions = array()) {
		if(!file_exists(CONFIGPATH . $p_sConfigFile)) {
			die('Unable to find <b>'. CONFIGPATH . $p_sConfigFile .'</b> file, please check your application configuration');
		}
		$ext   = PPI_Helper::getFileExtension($p_sConfigFile);
		$block = isset($p_aOptions['block']) ? $p_aOptions['block'] : 'development';
		switch($ext) {
			case 'ini':
				$this->_oConfig = new PPI_Config_Ini(parse_ini_file(CONFIGPATH . $p_sConfigFile, true), $block);
				break;

			case 'xml':
				die('Trying to load a xml config file but no parser yet created.');
				break;

			case 'php':
				die('Trying to load a php config file but no parser yet created.');
				break;

		}
	}
	
	/**
	 * Get the current set config objct
	 *
	 * @return object
	 */
	function getConfig() {
		return $this->_oConfig;
	}

}
