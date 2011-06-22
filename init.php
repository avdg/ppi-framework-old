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

defined('APPFOLDER')			|| define('APPFOLDER', ROOTPATH . 'App' . DS);


// ---- plugin paths ----
defined('PLUGINPATH')			|| define('PLUGINPATH', SYSTEMPATH . 'plugins' . DS);
defined('PLUGINCONTROLLERPATH')	|| define('PLUGINCONTROLLERPATH', PLUGINPATH . 'controllers' . DS);
defined('PLUGINMODELPATH')		|| define('PLUGINMODELPATH', PLUGINPATH . 'models' . DS);

// ---- app paths ------
defined('MODELPATH')			|| define('MODELPATH', APPFOLDER . 'Model' . DS);
defined('VIEWPATH')				|| define('VIEWPATH', APPFOLDER . 'View' . DS);
defined('CONTROLLERPATH')		|| define('CONTROLLERPATH', APPFOLDER . 'Controller' . DS);
defined('CONFIGPATH')			|| define('CONFIGPATH', APPFOLDER . 'Config' . DS);

defined('SMARTYPATH')			|| define('SMARTYPATH', SYSTEMPATH . 'Vendor' . DS . 'Smarty/');
defined('EXT')					|| define('EXT', '.php');
defined('SMARTY_EXT')			|| define('SMARTY_EXT', '.tpl');


// ------- system constants -------
defined('PPI_VERSION')			|| define('PPI_VERSION', '1.1');

set_include_path('.' . PATH_SEPARATOR . SYSTEMPATH . PATH_SEPARATOR . get_include_path());

// Autoload registration
require 'Autoload.php';
PPI_Autoload::register();

// General stuff
require 'common.php';
// load up custom error handlers
require 'errors.php';
setErrorHandlers('ppi_error_handler', 'ppi_exception_handler');
