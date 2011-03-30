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

set_include_path('.' . PATH_SEPARATOR . SYSTEMPATH . PATH_SEPARATOR . get_include_path());

// Autoload registration
include_once('Autoload.php');
PPI_Autoload::register();

// General stuff
include_once('common.php');
// load up custom error handlers
include_once('errors.php');
setErrorHandlers('ppi_error_handler', 'ppi_exception_handler');

// Turn off magic quotes if it's enabled
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	set_magic_quotes_runtime(0); // Kill magic quotes
}
