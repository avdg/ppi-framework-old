<?php
/**
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Dispatch
 * @link      www.ppiframework.com
 */
class PPI_Dispatch {

    /**
     * The actual helper doing the dispatching
     *
     * @var object that implements PPI_Dispatch_Interface
     */
	protected $_helper = null;

    /**
     * The router doing the routing
     * 
     * @var PPI_Router
     */
	protected $_router = null;

    /**
     * Identify and store the appropriate Controller and Methods to dispatch at a later time when calling dispatch()
     *
     */
	function __construct(PPI_Dispatch_Interface $p_oDispatch) {
		$this->_helper = $p_oDispatch;
	}

	/**
	 * Call the dispatch process for the current  set helper
	 *
     * @return void
	 */
	function dispatch() {

		if($this->_helper->init() === false) {
			PPI_Exception::show_404('Invalid dispatch process');
		}
		$this->_helper->dispatch();
	}

	/**
	 * Get the currently chosen controller name
	 *
	 * @return string
	 */
	function getControllerName() {
		return $this->_helper->getControllerName();
	}

	/**
	 * Get the current set method name on the chosen class.
	 *
	 * @return string
	 */
	function getMethodName() {
		return $this->_helper->getMethod();
	}
}
