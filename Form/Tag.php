<?php
/**
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
abstract class PPI_Form_Tag {

	/**
	 * @var array
	 */
	protected $_attributes = array();


	/**
	 * Getter and setter for attributes
	 *
	 * @param string $name The attribute name
	 * @param string $value The attribute value
	 * @return mixed
	 */
	protected function attr($name, $value = '') {

		if(empty($value)) {
			return isset($this->_attributes[$name]) ? $this->_attributes[$name] : '';
		} else {
			$this->_attributes[$name] = $value;
		}
	}

	/**
	 * Check if an attribute exists
	 *
	 * @param string $attr
	 * @return bool
	 */
	protected function hasAttr($attr) {
		return isset($this->_attributes[$attr]);
	}

	/**
	 * Build up the attributes
	 *
	 * @return string
	 */
	protected function buildAttrs() {

		$attrs = array();
		foreach($this->_attributes as $key => $name) {
			$attrs[] = $this->buildAttr($key);
		}
		return implode(' ', $attrs);
	}

	/**
	 * Build an attribute
	 *
	 * @param string $name
	 * @return string
	 */
	protected function buildAttr($name) {
		return sprintf('%s="%s"', $name, $this->escape($this->attr($name)));
	}

	/**
	 * Escape an attributes value
	 *
	 * @param string $value
	 * @return string
	 */
	protected function escape($value) {
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}


}