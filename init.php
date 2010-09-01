<?php
/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

// ---- site wide -----
defined('DS')                   or define('DS', DIRECTORY_SEPARATOR);
defined('ROOTPATH')          	or define('ROOTPATH', getcwd() . DS);
defined('SYSTEMPATH')        	or define('SYSTEMPATH', dirname(__FILE__) . DS);
defined('BASEPATH')          	or define('BASEPATH', dirname(__FILE__) . DS);

defined('APPFOLDER')         	or define('APPFOLDER', ROOTPATH . 'App' . DS);


// ---- plugin paths ----
defined('PLUGINPATH')        	or define('PLUGINPATH',SYSTEMPATH.'plugins' . DS);
defined('PLUGINCONTROLLERPATH') or define('PLUGINCONTROLLERPATH', PLUGINPATH.'controllers' . DS);
defined('PLUGINMODELPATH')   	or define('PLUGINMODELPATH', PLUGINPATH.'models' . DS);

// ---- app paths ------
defined('MODELPATH')           	or define('MODELPATH', APPFOLDER.'Model' . DS);
defined('VIEWPATH')            	or define('VIEWPATH',   APPFOLDER . 'View' . DS);
defined('CONTROLLERPATH')       or define('CONTROLLERPATH', APPFOLDER . 'Controller' . DS);
defined('CONFIGPATH')        	or define('CONFIGPATH', APPFOLDER.'Config' . DS);

defined('SMARTYPATH')          	or define('SMARTYPATH', SYSTEMPATH . 'Vendor' . DS . 'Smarty/');
defined('EXT')               	or define('EXT', '.php');
defined('SMARTY_EXT')           or define('SMARTY_EXT', '.tpl');


// ------- system constants -------
defined('PPI_VERSION')     		or define('PPI_VERSION', '1.1');

global $siteTypes;

/*
// Autoload preparation
set_include_path('.'
	. PATH_SEPARATOR . PLUGINMODELPATH
	. PATH_SEPARATOR . MODELPATH
	. PATH_SEPARATOR . COREMODELPATH
	. PATH_SEPARATOR . CORECONTROLLERPATH
	. PATH_SEPARATOR . COREHELPERPATH
	. PATH_SEPARATOR . COREINTERFACEPATH
	. PATH_SEPARATOR . get_include_path());

*/

set_include_path('.' . PATH_SEPARATOR . SYSTEMPATH . PATH_SEPARATOR . get_include_path());
// Autload registration
include_once('Base.php');
PPI_Base::registerAutoload();


// General stuff
include_once('common.php');
// load up custom error handlers
include_once('errors.php');
PPI_Base::setErrorHandlers('ppi_error_handler', 'ppi_exception_handler');

// Turn off magic quotes if it's enabled
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	set_magic_quotes_runtime(0); // Kill magic quotes
}

// If there are no site types defined in the bootstrap then let the developer know about it
if(!isset($siteTypes) || (is_array($siteTypes) && empty($siteTypes))) {
	die('Unable to find your site in the bootstrap file. Please add a site and its type to your index.php file.');
}

// Identify if the hostname exists in the bootstrap.
$sHostName = getHTTPHostname();
if(!isset($siteTypes[$sHostName])) {
	die('Unable to find your site: <b>'. $sHostName .'</b> in index.php bootstrap site types');
}

// ------------ ERROR LEVEL CHECKING --------------
if($siteTypes[$sHostName] == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
} else {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}