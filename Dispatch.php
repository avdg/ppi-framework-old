<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */


class PPI_Dispatch {
	
	protected $_helper = null;

    /**
     * Identify and store the appropriate Controller and Methods to dispatch at a later time when calling dispatch()
     *
     */
	function __construct(PPI_Dispatch_Helper_Interface $p_oHelper) {
		$this->_helper = $p_oHelper;
		if($p_oHelper->init() === false) {
			PPI_Exception::show_404('Invalid dispatchment');
		}
	}
	
	function dispatch() {
		$this->_helper->dispatch();
	}
	
	function getControllerName() {
		return $this->_helper->getControllerName();
	}
	
	function getMethodName() {
		return $this->_helper->getMethod();
	}
}
