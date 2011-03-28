<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Digiflex Development
 * @package   View
 */

class PPI_View_Helper {

	/**
	 * Stylesheet files to be rendered
	 * @var array $_styleSheets
	 */
	protected static $_styleSheets = array();

	/**
	 * Javascript files to be rendered
	 * @var array $_javascriptFiles
	 */
	protected static $_javascriptFiles = array();

	/**
	 * Add a stylesheet file to be rendered.
	 * @param mixed $p_mStylesheet
	 */
	static function addStylesheet($p_mStylesheet) {
		self::$_styleSheets = array_merge(self::$_styleSheets, (array) $p_mStylesheet);
	}

	/**
	 * Add a javascript file to be rendered.
	 * @param mixed $p_mJavascript
	 */
	static function addJavascript($p_mJavascript) {
		self::$_javascriptFiles = array_merge(self::$_javascriptFiles, (array) $p_mJavascript);
	}

	/**
	 * Get the stylesheets set to be rendered
	 * @return array
	 */
	static function getStylesheets() {
		return self::$_styleSheets;
	}

	/**
	 * Get the javascript files to be rendered
	 * @return array
	 */
	static function getJavascripts() {
		return self::$_javascriptFiles;
	}

}
