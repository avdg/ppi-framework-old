<?php
abstract class PPI_Dispatch_Helper_Abstract {
	
	/**
	 * The Config object
	 * @var object
	 */
	protected $_config;
	
	/**
	 * The Input object
	 * @var object
	 */
	protected $_input;
	
	/**
	 * The Full Url
	 * @var string
	 */
	protected $_fullUrl;
	
	
	protected $_controller;
	
	protected $_method;
	
	protected $_controllerName;
	
	function __construct() {
		$this->_config = PPI_Helper::getConfig();
		$this->_input  = PPI_Helper::getInput();
		$this->_fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . 
			'://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Checks to theee if the base url has been misconfigured or not
	 * Will return true if there is indeed a bad base url match.
	 * Will return false if there is not bad base url match.
	 * @return boolean
	 */
	function checkBadBaseUrl() {
        return stripos($this->_fullUrl, $this->_config->system->base_url)=== false;
	}
	
	/**
	 * Get the URI segments after the base url
	 * @todo Add a 3rd param to explode() to make it faster
	 * @return array
	 */
	function getURISegments() {
		return explode('/', trim(str_replace($this->_config->system->base_url, '', $this->_fullUrl), '/'));
	}
	
	/**
	 * Checks if a controller exists, if so - dispatch it otherwise return false
	 * @todo check if ($_SERVER ["REQUEST_URI"] == "/") {
	 * @return boolean
	 */
	function checkControllers() {
		
		// See if the mastercontroller exists in the config
		if(!isset($this->_config->system->masterController)) {
			throw new PPI_Exception('Unable to find mastercontroller in general.ini configuration file');
		}
		$sMasterController    = $this->_config->system->masterController;
		// If the mastercontroller is needed.
		$aUrls                = $this->getURISegments();
		$sControllerName      = ucfirst((empty($aUrls) || $aUrls[0] == '') ? $sMasterController : $aUrls[0]);
		$sLowerControllerName = strtolower($sControllerName); 		
		// Subtract the BaseUrl from the actual full URL and then what we have left is our controllers..etc
		$sContFilename = 'APP_Controller_' . $sControllerName; // eg: APP_Controller_User			
		if(class_exists($sContFilename)) {			
			$oController = new $sContFilename();
			// Did we specify a method ?
			if( ($sMethod = $this->_input->get($sLowerControllerName)) != '') {
				// Does our method exist on the class
				if(!in_array($sMethod, get_class_methods(get_class($oController)))) {
					return false;
				}
			} else {
				$sMethod = 'index';
			}
			$this->setControllerName($sLowerControllerName);
			$this->setController($oController);
			$this->setMethod($sMethod);			
			return true;	
		} 
		return false;	
	}
	
	function setController($p_oController) {
		$this->_controller = $p_oController;
	}
	
	function setControllerName($p_sController) {
		$this->_controllerName = $p_sController;
	}
	
	function getControllerName() {
		return $this->_controllerName;
	}
	
	function getController() {
		return $this->_controller;	
	}
	
	function setMethod($p_sMethod) {
		$this->_method = $p_sMethod;
	
	}
	
	function getMethod() {
		return $this->_method;
		
	}
	
}