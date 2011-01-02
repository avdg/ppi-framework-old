<?php
	/**
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) Digiflex Development Team
	 * @version 1.0
	 * @author Paul Dragoonis <dragoonis@php.net>
	 * @since Version 1.0
	 */

class PPI_Base {
	
	/**
	 * Class Name Instances
	 * @var object $_instances
	 */
	protected static $_instances = array();

	function __construct($p_aArguments = "") {
	}
	
	/**
	 * Set the error nad exception handlers
	 *
	 * @param string $p_sErrorHandler The error handler function name
	 * @param string $p_sExceptionHandler The exception handler function name
	 */
	static function setErrorHandlers($p_sErrorHandler = null, $p_sExceptionHandler = null) {
		if($p_sErrorHandler !== null) {
			set_error_handler($p_sErrorHandler, E_ALL);
		}
		if($p_sExceptionHandler !== null) {
			set_exception_handler($p_sExceptionHandler);
		}
	}

}
