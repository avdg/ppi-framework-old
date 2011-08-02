<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @copyright Digiflex Development
 * @package   PPI
 */
// ---- site wide -----
defined('DS')					|| define('DS', DIRECTORY_SEPARATOR);
defined('ROOTPATH')				|| define('ROOTPATH', getcwd() . DS);
defined('SYSTEMPATH')			|| define('SYSTEMPATH', dirname(__FILE__) . DS);
defined('BASEPATH')				|| define('BASEPATH', dirname(__FILE__) . DS);
defined('TESTPATH')             || define('TESTPATH', SYSTEMPATH . 'Test' . DS);
defined('APPFOLDER')			|| define('APPFOLDER', ROOTPATH . 'App' . DS);
defined('VENDORPATH')           || define('VENDORPATH',   SYSTEMPATH . 'Vendor' . DS);

// ---- plugin paths ----
defined('PLUGINPATH')			|| define('PLUGINPATH', SYSTEMPATH . 'plugins' . DS);
defined('PLUGINCONTROLLERPATH')	|| define('PLUGINCONTROLLERPATH', PLUGINPATH . 'controllers' . DS);
defined('PLUGINMODELPATH')		|| define('PLUGINMODELPATH', PLUGINPATH . 'models' . DS);

// ---- app paths ------
defined('MODELPATH')			|| define('MODELPATH', APPFOLDER . 'Model' . DS);
defined('VIEWPATH')				|| define('VIEWPATH', APPFOLDER . 'View' . DS);
defined('CONTROLLERPATH')		|| define('CONTROLLERPATH', APPFOLDER . 'Controller' . DS);
defined('CONFIGPATH')			|| define('CONFIGPATH', APPFOLDER . 'Config' . DS);

defined('EXT')					|| define('EXT', '.php');

// ------- system constants -------
defined('PPI_VERSION')			|| define('PPI_VERSION', '1.1');

set_include_path('.' . PATH_SEPARATOR . SYSTEMPATH . PATH_SEPARATOR . get_include_path());

// Autoload registration
require 'Autoload.php';
PPI_Autoload::register();

PPI_Autoload::add('PPI', array('path' => SYSTEMPATH, 'prefix' => 'PPI_'));
PPI_Autoload::add('APP', array('path' => APPFOLDER, 'prefix' => 'APP_'));

// General stuff
require 'common.php';
// load up custom error handlers
require 'errors.php';
setErrorHandlers('ppi_error_handler', 'ppi_exception_handler');

if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	set_magic_quotes_runtime(0); // Kill magic quotes
}
