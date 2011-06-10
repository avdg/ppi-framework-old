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
     * The config file name
     *
     * @var string
     */
	protected $_configFile = null;


	/**
	 * Initialise the config object
	 *
	 * Will check the file extension of your config filename and load up a specific parser
	 * @param string $p_sConfigFile The config filename
	 * @param array $p_aOptions The options
     *
     * @return void
	 */
	function __construct($p_sConfigFile, $p_aOptions = array()) {
		if(!file_exists(CONFIGPATH . $p_sConfigFile)) {
			die('Unable to find <b>'. CONFIGPATH . $p_sConfigFile .'</b> file, please check your application configuration');
		}
		$ext   = PPI_Helper::getFileExtension($p_sConfigFile);
		$block = isset($p_aOptions['block']) ? $p_aOptions['block'] : 'development';
		switch($ext) {
			case 'ini':
				$this->_oConfig = new PPI_Config_Ini(parse_ini_file(CONFIGPATH . $p_sConfigFile, true, INI_SCANNER_RAW), $block);
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
	 * Get the current set config object
	 *
	 * @return object
	 */
	function getConfig() {
		return $this->_oConfig;
	}

}
