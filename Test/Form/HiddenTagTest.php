<?php
class PPI_Form_HiddenTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->hidden('hiddenName', array('value' => 'hiddenValue'));
		$this->assertEquals($output, '<input type="hidden" name="hiddenName" value="hiddenValue">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->hidden('hiddenName', array('value' => 'hiddenValue', 'id' => 'bar'));
		$this->assertEquals($output, '<input type="hidden" name="hiddenName" value="hiddenValue" id="bar">');
	}

	function testDirectClass() {
		$submit = new PPI_Form_Tag_Hidden(array(
			'value' => 'hiddenValue',
			'name'  => 'hiddenName',
			'id'    => 'bar'
		));
		$output = $submit->render();
		$this->assertEquals($output, '<input type="hidden" value="hiddenValue" name="hiddenName" id="bar">');
	}


	function testDirectClass__toString() {
		$submit = new PPI_Form_Tag_Hidden(array(
			'value' => 'hiddenValue',
			'name'  => 'hiddenName',
			'id'    => 'bar'
		));
		$output = (string) $submit;
		$this->assertEquals($output, '<input type="hidden" value="hiddenValue" name="hiddenName" id="bar">');
	}

	function testHasAttr() {
		$submit = new PPI_Form_Tag_Hidden(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$this->assertTrue($submit->hasAttr('name'));
		$this->assertFalse($submit->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$submit = new PPI_Form_Tag_Hidden(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$this->assertEquals('Register', $submit->attr('value'));
	}

	function testSetAttr() {
		$submit = new PPI_Form_Tag_Hidden(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$submit->attr('foo', 'bar');
		$this->assertEquals('bar', $submit->attr('foo'));
	}

	function testGetValues() {
		$hidden = new PPI_Form_Tag_Hidden(array(
			'value' => 'hiddenvalue'
		));
		$this->assertEquals('hiddenvalue', $hidden->getValue());
		$this->assertEquals('hiddenvalue', $hidden->attr('value'));
	}

	function testSetValue() {
		$hidden = new PPI_Form_Tag_Hidden();
		$hidden->setValue('hiddenvalue');
		$this->assertEquals('hiddenvalue', $hidden->getValue());
	}

	function testGetSetRule() {

		$field = new PPI_Form_Tag_Hidden();

		$field->setRule('required');
		$this->assertTrue(count($field->getRule('required')) > 0);

		$field->setRule('maxlength', 32);
		$rule = $field->getRule('maxlength');
		$this->assertEquals($rule['value'], 32);
		$this->assertEquals($rule['type'], 'maxlength');
	}

}