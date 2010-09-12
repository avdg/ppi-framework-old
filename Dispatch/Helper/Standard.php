<?php
class PPI_Dispatch_Helper_Standard extends PPI_Dispatch_Helper_Abstract implements PPI_Dispatch_Helper_Interface {
	
	function __construct() {
		parent::__construct();
	}
	
	function init() {
		// We found a true match for bad base url
		if($this->checkBadBaseUrl() === true) {
			PPI_Exception::show_404();
		}
		if($this->checkControllers() === false) {
			PPI_Exception::show_404();
		}
		return true;
	}
	
	function dispatch() {		
		$oController = $this->getController();
		$sMethod     = $this->getMethod();
		$oController->$sMethod();
		exit;
	}
}