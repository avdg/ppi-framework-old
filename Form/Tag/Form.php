<?php
/**
 * Form class will help in automating rendering forms
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
class PPI_Form_Tag_Form extends PPI_Form_Tag implements PPI_Form_Tag_Interface {

	/**
	 * The constructor
	 *
	 * @param array $options
	 */
	function __construct(array $options = array()) {
		$options['action'] = isset($options['action']) ? $options['action'] : '';
		$this->_attributes = $options;
	}

	/**
	 * Render this tag
	 *
	 * @return string
	 */
	function render() {
		$attrs = $this->buildAttrs();
		return "<form $attrs></form>";
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
