<?php
class PPI_Form_SubmitTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->submit('Register');
		$this->assertEquals($output, '<input type="submit" value="Register">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->submit('Register', array('name' => 'foo', 'id' => 'bar'));
		$this->assertEquals($output, '<input type="submit" value="Register" name="foo" id="bar">');
	}

	function testDirectClass() {
		$submit = new PPI_Form_Tag_Submit(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$output = $submit->render();
		$this->assertEquals($output, '<input type="submit" value="Register" name="foo" id="bar">');
	}

	function testHasAttr() {
		$submit = new PPI_Form_Tag_Submit(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$this->assertTrue($submit->hasAttr('name'));
		$this->assertFalse($submit->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$submit = new PPI_Form_Tag_Submit(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$this->assertEquals('Register', $submit->attr('value'));
	}

	function testSetAttr() {
		$submit = new PPI_Form_Tag_Submit(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$submit->attr('foo', 'bar');
		$this->assertEquals('bar', $submit->attr('foo'));
	}

	function testGetValues() {
		$submit = new PPI_Form_Tag_Submit(array(
			'value' => 'submitvalue'
		));
		$this->assertEquals('submitvalue', $submit->getValue());
		$this->assertEquals('submitvalue', $submit->attr('value'));
	}

	function testSetValue() {
		$submit = new PPI_Form_Tag_Submit();
		$submit->setValue('submitvalue');
		$this->assertEquals('submitvalue', $submit->getValue());
	}



}