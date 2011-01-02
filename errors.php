<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */


/**
 * The default PPI error handler, will play with some data then throw an exception, thus the set_exception_handler callback is ran
 *
 * @param string $errno The error level (number)
 * @param string $errstr The error message
 * @param string $errfile The error filename
 * @param string $errline The error line
 * @throws PPI_Exception
 */
function ppi_error_handler($errno = '', $errstr = "", $errfile = "", $errline = "") {
	$ppi_exception_thrown = true;
	$error = array ();
	$error ['code']   	= $errno;
	$error ['message']  = $errstr;
	$error ['file'] 	= $errfile;
	$error ['line'] 	= $errline;
	/* throw exception to user */
	$oException = new PPI_Exception();
	if(property_exists($oException, '_traceString')) {
		$error['backtrace'] = $oException->_traceString;
	}
	// this function has the exit() call in it, so we must put it last
	$oException->show_exceptioned_error($error);
}

/**
 * The default exception handler
 *
 * @param object $oException The exception object
 * @return void
 */
function ppi_exception_handler($oException) {
	if(!$oException instanceof Exception) {
		return false;
	}
	$error = array();
	foreach(array('code', 'message', 'file', 'line', 'traceString') as $field) {
		$fieldName = "_$field";
		if(!property_exists($oException, $fieldName)) { continue; }
		if($field == 'traceString') {
			$error['backtrace'] = $oException->$fieldName;
		} else {
			$error[$field] = $oException->$fieldName;
		}
	}

	try {
		
		if(!PPI_Registry::getInstance()->exists('PPI_Config')) {
			$oException->show_exceptioned_error($error);
			return;
		}

		$oConfig = PPI_Helper::getConfig();		
		$error['sql'] = PPI_Helper::getRegistry()->get('PPI_Model::PPI_Model_Queries', array());

		// email the error with the backtrace information to the developer
		if(!isset($oConfig->system->log_errors) || $oConfig->system->log_errors != false) {
			
			// get the email contents
			$emailContent = $oException->getErrorForEmail($error);
			
			$oLog = new PPI_Model_Log();
			$oLog->addExceptionLog(array(
				'code' 		=> $oException->_code,
				'message' 	=> $oException->_message,
				'file' 		=> $oException->_file,
				'line'		=> $oException->_line,
				'backtrace' => $error['backtrace'],
				'post'      => serialize($_POST),
				'cookie'    => serialize($_COOKIE),
				'get'       => serialize($_GET),
				'session'   => serialize($_SESSION),
				'content'	=> $emailContent								
			));
		
			if($oConfig->system->email_errors) {
				//@mail($oConfig->system->developer_email, 'PHP Exception For '.getHostname(), $emailContent);
				//include CORECLASSPATH.'mail.php';
				//$mail = new Mail();
				//$mail->send();
			}
			
			// write the error to the php error log
			writeErrorToLog($error['message'] . ' in file: '.$error['file'] . ' on line: '.$error['line']);			
			$oException->show_exceptioned_error($error); 
		}

	}
	catch(PPI_Exception $e) {
		writeErrorToLog($e->getMessage());
	}
	catch(Exception $e) {
		writeErrorToLog($e->getMessage());
	}
	catch(PDOException $e) {
		writeErrorToLog($e->getMessage());
	}
	$oException->show_exceptioned_error($error); 
	
	// @todo This should go to an internal error page which doesn't use framework components and show the error code
//	ppi_show_exceptioned_error($error);
	
}

function writeErrorToLog($message) {
	if(ini_get('log_errors') !== 'On') {
		return false;
	}
	$oConfig   = PPI_Helper::getConfig();
	if( ($sErrorLog = ini_get('error_log')) == 'syslog') {
		syslog(LOG_ALERT, "\n$message\n");
	} elseif($sErrorLog !== '' && is_writable($sErrorLog)) {
		file_put_contents($sErrorLog, "\n$message\n", FILE_APPEND);
	}
}

function ppi_show_exceptioned_error() {
	
}

/**
 * Set the error and exception handlers
 *
 * @param string $p_sErrorHandler The error handler function name
 * @param string $p_sExceptionHandler The exception handler function name
 * @return void
 */
function setErrorHandlers($p_sErrorHandler = null, $p_sExceptionHandler = null) {
        if($p_sErrorHandler !== null) {
                set_error_handler($p_sErrorHandler, E_ALL);
         }
         if($p_sExceptionHandler !== null) {
                set_exception_handler($p_sExceptionHandler);
        }
}

