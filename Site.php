<?php

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

	function setSessionHandler(PPI_Session_Interface $oSessionHandler) {

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