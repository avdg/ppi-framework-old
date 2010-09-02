<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

	/* PHP Fatal Error Information
	 * The Problem
	 * -------------
	 * Fatal error: Exception thrown without a stack frame in Unknown on line 0
	 *
	 * The Cause
	 * ----------
	 * We previously extended the model class here to DB logged purposes.
	 * The error was because, if an exception was thrown from Model, we're re-accessing model to log the error.
	 * Thus an infinite exception loop.
	 *
	 * The Solution
	 * ---------------
	 * Removed the extends to the Model class and make it it's own object.
	 *
	 */
	class PPI_Model_Log {

		function addExceptionLog(array $p_aError) {
			$oDB = new PPI_Model_Shared('ppi_exceptions', 'id');
			$oDB->putRecord($p_aError);
		}

		function addErrorLog(array $p_aError) {
			$oDB = new PPI_Model_Shared('ppi_errors', 'id');
			$oDB->putRecord($p_aError);
		}

		function addEmailLog(array $p_aError) {
			$oDB = new PPI_Model_Shared('ppi_email_log', 'id');
			$oDB->putRecord($p_aError);
		}

		/**
		 * Get email logs with an optional clause
		 *
		 * @param string array $p_mWhere
		 */
		function getEmailLogs($p_mWhere = '') {
			$oDB = new PPI_Model_Shared('ppi_email_log', 'id');
			return $oDB->getList($p_mWhere);
		}

		function addSystemLog(array $p_aError) {
			$oDB = new PPI_Model_Shared('ppi_system_log', 'id');
			$oDB->putRecord($p_aError);
		}
		/**
		 * Get system logs with an optional clause
		 *
		 * @param string array $p_mWhere
		 */
		function getSystemLogs($p_mWhere = '') {
			$oDB = new PPI_Model_Shared('ppi_system_log', 'id');
			return $oDB->getList($p_mWhere, 'created desc');
		}

	/*	function getLogs($where) {

		}

		function delLogs($logIDs = array()) {
			if(!is_array($logIDs)) {
				$logIDs = explode(',', $logIDs);
			}
			$inIDs = $this->makeIN($logIDs);
		}*/
	}
