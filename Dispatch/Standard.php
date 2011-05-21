<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Dispatch
 * @link      www.ppiframework.com
 */
class PPI_Dispatch_Standard extends PPI_Dispatch_Abstract implements PPI_Dispatch_Interface {

    /**
     * Optionally pass in a routing driver to take care of the routing.
     * @todo This feature is experimental and isn't ready for production yet
     *
     * @param null|PPI_Router_Interface $p_oRouter
     */
	function __construct($p_oRouter = null) {
		/*
		if($p_oRouter !== null && $p_oRouter instanceof PPI_Router_Interface) {
			$this->_router = $p_oRouter;
		}
		*/
		parent::__construct();
	}

	/**
	 * Run the init() method and check for a misconfigured baseUrl from the config vs the current url
	 * Check for a controller to dispatch by using the _Abstract version of checkControllers()
	 *
	 * @return boolean
	 */
	function init() {

		if($this->_router !== null) {
			$this->_router->init();
		}
		// We found a true match for bad base url
		if($this->checkBadBaseUrl() === true) {
			PPI_Exception::show_404();
		}
		if($this->checkControllers() === false) {
			PPI_Exception::show_404();
		}
		return true;
	}

	/**
	 * Dispatch the set controller
	 *
     * @return void
	 */
	function dispatch() {
		$oController = $this->getController();
		$sMethod     = $this->getMethod();
		$oController->$sMethod();
		exit;
	}
}