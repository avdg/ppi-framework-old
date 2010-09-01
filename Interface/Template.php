<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   PPI
 */

interface PPI_Interface_Template {

	function assign($key, $val);

	function render($p_sTemplateFile);

	function getTemplateExtension();

	function getDefaultMasterTemplate();

}