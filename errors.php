<?php

// never throw an exception in this file or any functions that it calls.
// This results in an infinite exception loop and you get a PHP fatal error.

function show_404 ($sLocation = "") {
	$oErr =  new PPI_Exception ();
	$oErr->show_404 ($sLocation);
	return;
}

function show_403 ($sMessage = "") {
	$oErr =  new PPI_Exception ();
	$oErr->show_403 ($sMessage);
	return;
}

function show_error ($sError = "") {
	$oErr =  new PPI_Exception();
	$oErr->show_error ($sError);
	return;
}

function ppi_error_handler($errno = '', $errstr = "", $errfile = "", $errline = "") {
	global $ppi_exception_thrown, $sql_queries;
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

function ppi_exception_handler($oException) {

	$oConfig = PPI_Helper::getConfig();

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

		$error['sql'] = PPI_Helper::getRegistry()->get('PPI_Model::PPI_Model_Queries', array());

		// email the error with the backtrace information to the developer
		if(!isset($oConfig->system->log_errors) || $oConfig->system->log_errors != false) {
			// write the error to the php error log
			writeErrorToLog($error['message'] . ' in file: '.$error['file'] . ' on line: '.$error['line']);
			// get the email contents
			$emailContent = $oException->getErrorForEmail($error);
			$oLog = new PPI_Model_Log();
			$oLog->addExceptionLog(array(
				'code' 		=> $oException->_code,
				'message' 	=> $oException->_message,
				'file' 		=> $oException->_file,
				'line'		=> $oException->_line,
				'content'	=> $emailContent
			));
			if($oConfig->system->email_errors) {
				//@mail($oConfig->system->developer_email, 'PHP Exception For '.getHostname(), $emailContent);
				//include CORECLASSPATH.'mail.php';
				//$mail = new Mail();
				//$mail->send();
			}
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

	ppi_show_exceptioned_error($error);
}

/**
 * PPI_Exception::show_exceptioned_error()
 * Show this exception
 * @param string $p_aError Error information from the custom error log
 * @return void
 */
function ppi_show_exceptioned_error($p_aError = "") {
	global $siteTypes;
	$oConfig = PPI_Helper::getConfig();

	$p_aError['sql'] = PPI_Helper::getRegistry()->get('PPI_Model::Query_Backtrace', array());
	$sHostName = getHTTPHostname();
	if($siteTypes[$sHostName] == 'development') {
		$heading = "Exception";
		$baseUrl = $oConfig->system->base_url;
		require SYSTEMPATH.'View/code_error.php';
		echo $header.$html.$footer;
	} else {
		$oView = new PPI_View();
		$oView->loadSmarty('error', array('message' => $p_aError['message']));
	}
	exit;
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
