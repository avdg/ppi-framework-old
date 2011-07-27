<?php

/**
 * Form class will help in automating rendering forms
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Core
 * @link      www.ppiframework.com
 *
 */
class PPI_Form_Html implements PPI_Form_Tag {

	/**
	 * @var array
	 */
	private $_attributes = array();

	function __construct($action, $method = '') {
		if (empty($action)) {
			throw new PPI_Exception("Action attribute can't be empty!");
		}

		$this->_attributes['action'] = $action;
		$this->_attributes['method'] = $method;
	}

	function getAttribute($name) {
		return isset($this->_attributes[$name]) ? $this->_attributes[$name] : '';
	}

	function setAttribute($name, $value) {
		$this->_attributes[$name] = $value;
	}

	function render() {
		$attributes = $this->buildAttributes();
		return "<form $attributes></form>";
	}

	private function buildAttributes() {
		$filled_attributes = array();
		foreach($this->_attributes as $key => $item) {
			$filled_attributes[] = $this->buildAttribute($key);
		}

		return implode(" ", array_filter($filled_attributes));
	}

	private function buildAttribute($name) {
		if (!$value = $this->getAttribute($name)) {
			return '';
		}
		return sprintf('%s="%s"', $name, $this->escape($value));
	}

	private function escape($value) {
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}

	function __toString() {
		return $this->render();
	}
}
