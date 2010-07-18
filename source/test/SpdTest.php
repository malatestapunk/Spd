<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/Spd.php';

class SpdTest extends PHPUnit_Framework_TestCase {

	function testLoadParser () {
		$class = Spd::loadParser(Spd::PARSER_TEXT);
		$this->assertTrue(('Spd_Parsers_TextParser' == $class));
	}

	/**
     * @expectedException Spd_SystemException
     */
	function testLoadParserThrowsException () {
		$class = Spd::loadParser('random non-existent parser');
	}

	function testGetTextParser () {
		$p = Spd::getParser(Spd::PARSER_TEXT, '.');
		$this->assertTrue(($p instanceof Spd_Parsers_Parser));
		$this->assertTrue(($p instanceof Spd_Parsers_TextParser));
	}
}
?>