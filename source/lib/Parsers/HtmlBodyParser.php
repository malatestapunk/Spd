<?php
include dirname(__FILE__) . '/TextParser.php';

/**
 * Parser that outputs docs as HTML.
 * This parser renders only BODY element.
 * @package Spd
 * @subpackage Parsers
 * @author Ve
 *
 */
class Spd_Parsers_HtmlBodyParser extends Spd_Parsers_TextParser {
	private $_toc = array();

	public function getOutput () {
		sort($this->_toc);
		$ret = '<h1>Classes</h1><ul>';
		foreach ($this->_toc as $head) {
			$ret .= '<li><a href="#' . md5($head) . '">' . $head . '</a></li>' . "\n";
		}
		$ret .= '</ul>';
		return $ret . $this->output;
	}
	public function processClasses($classes) {
		return '<div class="classes">' . "\n" .
			parent::processClasses($classes) .
			"\n" . '</div>';
	}

	public function processClass (ReflectionClass $class) {
		$this->_toc[] = $class->getName();
		$ret = '<div class="class_doc" id="class_' . md5($class->getName()) . '">' . "\n";
		$ret .= '<h2><a id="' . md5($class->getName()) . '"></a>';
		$ret .= '<span class="access_modifiers">' . $this->processModifiers($class) . '</span>';
		$ret .= 'class ' . $this->getClassName($class->getName(), false);
		$ret .= '</h2>';
		$ret .= '<div class="class_hierarchy">';
		$ret .= $class->getParentClass() ? "\n\t" . '<b>extends</b> ' . $this->getClassName($class->getParentClass()->getName()) : '';
		$ret .= $class->getInterfaceNames() ? "\n\t" . 'implements ' . join(', ', $class->getInterfaceNames()) : '';
		$ret .= '</div>';

		$ret .= '<p>Defined in <code>' . $class->getFileName();
		$ret .= '</code> on line ' . $class->getStartLine();
		$ret .= "</p>\n";

		$ret .= "\n<h3>Constants:</h3>\n";
		$ret .= '<div class="elements constants">' .
			$this->processConstants($class->getConstants()) .
			'</div>';

		$ret .= "\n<h3>Properties:</h3>\n";
		$ret .= '<div class="elements properties">' .
			$this->processProperties($class->getProperties(), $class->getName()) .
			'</div>';

		$ret .= "\n<h3>Methods:</h3>\n";
		$ret .= '<div class="elements methods">' .
			$this->processMethods($class->getMethods(), $class->getName()) .
			'</div>';

		$ret .= "\n</div><!-- .class_doc -->\n";
		return $ret;
	}

	public function processProperties ($cls, $cn) {
		return '<dl class="properties">' .
			parent::processProperties($cls, $cn) .
			"</dl>\n";
	}

	public function processMethods ($mets, $cn) {
		return '<dl class="methods">' .
			parent::processMethods($mets, $cn) .
			"</dl>\n";
	}

	public function processProperty (ReflectionProperty $prop, $className) {
		$inherited = ($className != $prop->getDeclaringClass()->getName());
		$ret = $inherited ? '<div class="inherited">' : '';
		$ret .= '<dt>';
		$ret .= $inherited ? '<span class="inherited">(inherited from ' . $this->getClassName($prop->getDeclaringClass()->getName()) . ')</span> ' : '';
		$ret .= $this->processModifiers($prop);
		$ret .= $this->getDefinition('$' . $prop->getName());
		$ret .= '</dt><dd>';
		$ret .= $prop->getDocComment() ? "\n\t" . ' - ' . $this->processComment($prop->getDocComment()) : '&nbsp;';
		$ret .= '</dd>';
		$ret .= $inherited ? '</div>' : '';
		return $ret;
	}

	public function processMethod (ReflectionMethod $met, $className) {
		$inherited = ($className != $met->getDeclaringClass()->getName());
		$ret = $inherited ? '<div class="inherited">' : '';
		$ret .= '<dt>';
		$ret .= $inherited ? '<span>(inherited from ' . $this->getClassName($met->getDeclaringClass()->getName()) . ')</span> ' : '';
		$ret .= $this->processModifiers($met);
		$ret .= 'function ';
		$ret .= $this->getDefinition(
			$met->getName() .
			' (' .
				$this->processParameters($met->getParameters()) .
			') '
		);
		$ret .= '</dt><dd>';
		$ret .= $met->getDocComment() ? $this->processComment($met->getDocComment()) : '&nbsp;';
		$ret .= '</dd>';
		$ret .= $inherited ? '</div>' : '';
		return $ret;
	}

	public function processComment ($cmnt) {
		return '<p>' .
			preg_replace ("/\n/", '</p><p>', parent::processComment($cmnt)) .
			'</p>';
	}

	private function getClassName ($c, $showAsLink=true) {
		$ret =  '<code class="class_name">';
		$ret .= $showAsLink ?
			'<a href="#' . md5($c) . '">' . $c . '</a>'
			:
			$c;
		$ret .= '</code>';
		return $ret;
	}

	private function getDefinition ($n) {
		return '<code class="name">' . $n . '</code>';
	}
}
?>