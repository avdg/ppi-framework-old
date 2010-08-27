<?php
class PPI_View_Helper {

	
	protected static $_styleSheets = array();
	protected static $_javascriptFiles = array();	
	
	static function addStylesheet($p_mStylesheet) {
		self::$_styleSheets = array_merge(self::$_styleSheets, (array) $p_mStylesheet);
	}
	
	static function addJavascript($p_mJavascript) {
		self::$_javascriptFiles = array_merge(self::$_javascriptFiles, (array) $p_mJavascript);
	}
	
	static function getStylesheets() {
		return self::$_styleSheets;
	}
	
	static function getJavascripts() {
		return self::$_javascriptFiles;
	}
	
}