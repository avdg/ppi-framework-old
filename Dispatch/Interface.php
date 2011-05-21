<?php
/**
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   Dispatch
 * @link      www.ppiframework.com
 */
interface PPI_Dispatch_Interface {

    /**
     * The init() function of the driver.
     *
     * @abstract
     * @return void
     */
	function init();

    /**
     * Actually perform the dispatch
     *
     * @abstract
     * @return void
     */
	function dispatch();

}