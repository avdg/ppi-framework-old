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
	 * The rules for this field
	 *
	 * @var array
	 */
	protected $_rules = array();


	/**
	 * Render the tag
	 *
	 * @return void
	 */
	abstract protected function render();

	/**
	 * Getter and setter for attributes
	 *
	 * @param string $name The attribute name
	 * @param string $value The attribute value
	 * @return mixed
	 */
	public function attr($name, $value = null) {

		if(null === $value) {
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
	public function hasAttr($attr) {
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

	/**
	 * When echo'ing this tag class, we call render
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Set a rule on this field
	 *
	 * @param string $ruleType
	 * @param string $ruleValue
	 * @return void
	 */
	public function setRule($ruleType, $ruleValue = '') {
		$this->_rules[$ruleType] = array('type' => $ruleType, 'value' => $ruleValue);
	}

	/**
	 * Get the rule on this field
	 *
	 * @return array
	 */
	public function getRule($ruleType) {
		return isset($this->_rules[$ruleType]) ? $this->_rules[$ruleType] : array();
	}


}
