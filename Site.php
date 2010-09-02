<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */
class PPI_Site {

	public $_configFile = null;
	public $_templateName = null;

	/**
	 * Override the config file
	 *
	 * @param string $p_sFilename
	 */
	function setConfigFile($p_sFilename) {
		$this->_configFile = $p_sFilename;
	}

	/**
	 * Set the custom session object for the runtime
	 * @param object  $oSessionHandler The Session Handler. Instance 
of PPI_Interfaces_Session
	 */
	function setSessionHandler(PPI_Interfaces_Session 
$oSessionHandler) {

	}

	/**
	 * Override the master template name
	 *
	 * @param string $p_sTemplateName
	 */
	function setMasterTemplate($p_sTemplateName) {
		$this->_templateName = $p_sTemplateName;
	}
	
	/**
	 * Override the routes file
	 *
	 * @param string $p_sFilename
	 */
	function setRoutesFile($p_sFilename) {
		$this->_routesFile = $p_sFilename;
	}


}
