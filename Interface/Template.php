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

	/**
	 * Assign a value to a view
	 *
	 * @param string $key The variable name
	 * @param stirng $val The variable value
	 */
	function assign($key, $val);

	/**
	 * Render and load the actual view file
	 *
	 * @param string $p_sTemplateFile The template 
	 */
	function render($p_sTemplateFile);

	/**
	 * Get the default extension for the templates
	 *
	 */
	function getTemplateExtension();

	/**
	 * Get the default master template file.
	 *
	 */
	function getDefaultMasterTemplate();

}