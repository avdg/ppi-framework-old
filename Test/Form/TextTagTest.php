<?php
class PPI_Test_TextTagTest extends PHPUnit_Framework_TestCase {

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
			'name'  => 'username',
			'id'    => 'bar'
		));
		$output = $text->render();
		$this->assertEquals($output, '<input type="text" value="Register" name="username" id="bar">');
	}

	function testHasAttr() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'Register',
			'name'  => 'username',
			'id'    => 'bar'
		));
		$this->assertTrue($text->hasAttr('name'));
		$this->assertFalse($text->hasAttr('nonexistantattr'));
	}

	function testGetAttr() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'Register',
			'name'  => 'username',
			'id'    => 'bar'
		));
		$this->assertEquals('Register', $text->attr('value'));
	}

	function testSetAttr() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'Register'
		));
		$text->attr('foo', 'bar');
		$this->assertEquals('bar', $text->attr('foo'));
	}

	function testGetValues() {
		$text = new PPI_Form_Tag_Text(array(
			'value' => 'textvalue'
		));
		$this->assertEquals('textvalue', $text->getValue());
		$this->assertEquals('textvalue', $text->attr('value'));
	}

	function testSetValue() {
		$text = new PPI_Form_Tag_Text();
		$text->setValue('textvalue');
		$this->assertEquals('textvalue', $text->getValue());
	}

}