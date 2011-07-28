<?php
/**
 * Form class will help in automating rendering forms
 *
 * @author    Paul Dragoonis <dragoonis@php.net>
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @package   Form
 * @link      www.ppiframework.com
 */
class PPI_Form {

	function __construct() {}

	/**
	 * Create our form
	 *
	 * @param string $action
	 * @param string $method
	 * @param array $options
	 * @return string
	 */
	function create($action = '', $method = '', array $options = array()) {
		return $this->add('form', array('method' => $method, 'action' => $action) + $options);
	}

	/**
	 * Add a text field to our form
	 *
	 * @param string $name
	 * @param array $options
	 * @return string
	 */
	function text($name, array $options = array()) {
		if(!empty($name)) {
			return $this->add('text', array('name' => $name) + $options);
		}
		return '';
	}

	/**
	 * Add a submit field
	 *
	 * @param string $value
	 * @param array $options
	 * @return void
	 */
	function submit($value = 'Submit', array $options = array()) {
		return $this->add('submit', array('value' => $value) + $options);
	}

	/**
	 * Add a select (dropdown) field
	 *
	 * @param string $name
	 * @param array $dropdownValues
	 * @param array $options
	 * @return void
	 */
	function select($name, array $dropdownValues, array $options = array()) {
		return $this->add('select', array(
			'name'           => $name,
			'dropdownValues' => $dropdownValues
		) + $options);
	}

	/**
	 * Add a field to our form.
	 *
	 * @param string $fieldType
	 * @param array $options
	 * @return void
	 */
	function add($fieldType, array $options = array()) {

		switch($fieldType) {

			case 'form':
				$field = new PPI_Form_Tag_Form($options);
				break;

			case 'text':
				$field = new PPI_Form_Tag_Text($options);
				break;

			case 'submit':
				$field = new PPI_Form_Tag_Submit($options);
				break;

			case 'select':
			case 'dropdown':

				if(isset($options['dropdownValues'])) {
					$values = $options['dropdownValues'];
					unset($options['dropdownValues']);
				}
				if(isset($options['selected'])) {
					$selected = $options['selected'];
					unset($options['selected']);
				}

				$field = new PPI_Form_Tag_Select($options);
				if(isset($values)) {
					$field->setValues($values);
				}
				if(isset($selected)) {
					$field->setValue($selected);
				}

				break;

			default:
				throw new PPI_Exception('Invalid Field Type: ' . $fieldType);
		}

		return $field->render();
	}

	/**
	 * Method to bind data from an entity to a form element
	 *
	 * @param array $data
	 * @return void
	 */
	function bindArray(array $data) {
		foreach($data as $fieldName => $value) {
			// .. tbc
		}
	}

	/**
	 * Bind data for a particular field
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 * @return void
	 */
	function bindField($fieldName, $value) {
		$this->bindArray(array($fieldName => $value));
	}

	function end() {
		return '</form>';
	}

}