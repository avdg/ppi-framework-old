<?php
class PPI_Test_CheckboxTagTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->checkbox('mycheck');
		$this->assertEquals($output, '<input type="checkbox" name="mycheck">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->checkbox('mycheck', array('id' => 'bar'));
		$this->assertEquals($output, '<input type="checkbox" name="mycheck" id="bar">');
	}

	function testDirectClass() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check',
			'name'  => 'mycheck',
			'id'    => 'bar'
		));
		$output = $checkbox->render();
		$this->assertEquals($output, '<input type="checkbox" value="foo_check" name="mycheck" id="bar">');
	}

	function testDirectClass__toString() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check',
			'name'  => 'mycheck',
			'id'    => 'bar'
		));
		$output = (string) $checkbox;
		$this->assertEquals($output, '<input type="checkbox" value="foo_check" name="mycheck" id="bar">');
	}

	function testHasAttr() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check',
			'name'  => 'mycheck',
			'id'    => 'bar'
		));
		$this->assertTrue($checkbox->hasAttr('name'));
		$this->assertFalse($checkbox->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check',
			'name'  => 'mycheck',
			'id'    => 'bar'
		));
		$this->assertEquals('foo_check', $checkbox->attr('value'));
	}

	function testSetAttr() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check'
		));
		$checkbox->attr('foo', 'bar');
		$this->assertEquals('bar', $checkbox->attr('foo'));
	}

	function testGetValues() {
		$checkbox =  new PPI_Form_Tag_Checkbox(array(
			'value' => 'foo_check'
		));
		$this->assertEquals('foo_check', $checkbox->getValue());
		$this->assertEquals('foo_check', $checkbox->attr('value'));
	}

	function testSetValue() {
		$checkbox =  new PPI_Form_Tag_Checkbox();
		$checkbox->setValue('foo_check');
		$this->assertEquals('foo_check', $checkbox->getValue());
	}

}