<?php

/**
 *
 * @version   1.0
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
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
		if(is_string($p_mStylesheet)) {
			self::$_styleSheets[] = $p_mStylesheet;
			return;
		}
		if(is_array($p_mStylesheet)) {
			foreach($p_mStylesheet as $stylesheet) {
				self::addStylesheet($stylesheet);
			}
			return;
		}
	}

	/**
	 * Add a javascript file to be rendered.
	 * @param mixed $p_mJavascript
	 */
	static function addJavascript($p_mJavascript) {
		if(is_string($p_mJavascript)) {
			self::$_javascriptFiles[] = $p_mJavascript;
			return;
		}
		if(is_array($p_mJavascript)) {
			foreach($p_mJavascript as $javascriptFile) {
				self::addJavascript($javascriptFile);
			}
			return;
		}
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
