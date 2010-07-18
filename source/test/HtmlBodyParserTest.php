<?php
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../lib/Spd.php';

class HtmlBodyParserTest extends PHPUnit_Framework_TestCase {
	private $_parser;
	private $_sourceDir = '../../../Formulator/source/lib';
	private $_sourcePath;

	function setUp () {
		$this->_sourcePath = dirname(__FILE__) . '/' . $this->_sourceDir;
		$this->_parser = Spd::getParser(Spd::PARSER_HTML, $this->_sourcePath);
	}

	function testGetPath () {
		$this->assertSame($this->_parser->getPath(), realpath($this->_sourcePath));
	}

	function testParse () {
		$p = $this->_parser->parse();
		$this->assertTrue(($p instanceof Spd_Parsers_Parser));
	}

	function testGetOutput () {
		$txt = $this->_parser->parse()->getOutput();
		$this->assertType('string', $txt);
	}

}
?>