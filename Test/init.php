<?php
/**
 * Unit test bootloader for PPI
 *
 * @package   Core
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @link      http://www.ppiframework.com
*/

date_default_timezone_set('Europe/Brussels');

set_include_path(implode(PATH_SEPARATOR, array(
       dirname(__FILE__).'/../',
       get_include_path(),
)));

require_once 'init.php';

restore_error_handler();
restore_exception_handler();


$unitDIR = realpath('../Vendor/PHPUnit');

set_include_path(implode(PATH_SEPARATOR, array(
       $unitDIR.'/dbunit/',
       $unitDIR.'/php-code-coverage/',
       $unitDIR.'/php-file-iterator/',
       $unitDIR.'/php-text-template/',
       $unitDIR.'/php-timer/',
       $unitDIR.'/php-token-stream/',
       $unitDIR.'/phpunit-mock-objects/',
       $unitDIR.'/phpunit-selenium/',
       get_include_path(),
)));

include '../Vendor/PHPUnit/phpunit.php';
