<?php
class PPI_Form_TextTagTest extends PHPUnit_Framework_TestCase {

	function setUp() {
		$this->_form = new PPI_Form();
	}

	function tearDown() {
		unset($this->_form);
	}

	function testCreate() {
		$output = $this->_form->text('username');
		$this->assertEquals($output, '<input type="text" name="username">');
	}

	function testCreateWithAttrs() {
		$output = $this->_form->text('username', array('id' => 'bar'));
		$this->assertEquals($output, '<input type="text" name="username" id="bar">');
	}

	function testDirectClass() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'Register',
			'name'  => 'foo',
			'id'    => 'bar'
		));
		$output = $text->render();
		$this->assertEquals($output, '<input type="text" value="Register" name="foo" id="bar">');
	}
/*
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
			'value' => 'Register'
		));
		$submit->attr('foo', 'bar');
		$this->assertEquals('bar', $submit->attr('foo'));
	}

*/

}