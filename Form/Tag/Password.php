<?php
/**
 * Form class will help in automating rendering forms
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 *
 */
class PPI_Form_Tag_Password extends PPI_Form_Tag implements PPI_Form_Tag_Interface {

	/**
	 * The constructor
	 *
	 * @param array $options
	 */
	function __construct(array $options = array()) {
		$this->_attributes = $options;
	}

	/**
	 * Set the value of this field
	 *
	 * @param string $value
	 * @return void
	 */
	function setValue($value) {
		$this->_attributes['value'] = $value;
	}

	/**
	 * Render this tag
	 *
	 * @return string
	 */
	function render() {
		return '<input type="password" ' . $this->buildAttrs() . '>';
	}

	/**
	 * When echo'ing this tag class, we call render
	 *
	 * @return string
	 */
	function __toString() {
		return $this->render();
	}
}
