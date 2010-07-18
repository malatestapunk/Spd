<?php
include dirname(__FILE__) . '/Parser.php';

/**
 * Parser that outputs docs as a text file.
 * @package Spd
 * @subpackage Parsers
 * @author Ve
 *
 */
class Spd_Parsers_TextParser extends Spd_Parsers_Parser {

	public function __construct ($path) {
		$this->setPath ($path);
	}

	public function parse () {
		$this->processSourceTree();
		$this->output = $this->processClasses ($this->classes);
		return $this;
	}

	public function processModifiers (Reflector $obj) {
		$ret = '';
		if ($obj instanceof ReflectionClass || $obj instanceof ReflectionMethod) {
			$ret .= $obj->isAbstract() ? 'abstract ' : '';
			$ret .= $obj->isFinal() ? 'final ' : '';
		}
		if (!$obj instanceof ReflectionClass) {
			$ret .= $obj->isPrivate() ? 'private ' : '';
			$ret .= $obj->isProtected() ? 'protected ' : '';
			$ret .= $obj->isPublic() ? 'public ' : '';
			$ret .= $obj->isStatic() ? 'static ' : '';
		}
		return $ret;
	}

	public function processClasses ($classes) {
		$out = array();
		foreach ($this->classes as $c) {
			$out[] = $this->processClass($c);
		}
		sort($out);
		return join("\n", $out);
	}

	public function processClass (ReflectionClass $class) {
		$ret = '';
		$ret .= $this->processModifiers($class);
		$ret .= 'class ' . $class->getName();
		$ret .= $class->getParentClass() ? "\n\t" . 'extends ' . $class->getParentClass()->getName() : '';
		/*untested*/$ret .= $class->getInterfaceNames() ? "\n\t" . 'implements ' . join(', ', $class->getInterfaceNames()) : '';
		$ret .= "::\n";

		$ret .= 'Defined in ' . $class->getFileName();
		$ret .= ' on line ' . $class->getStartLine();
		$ret .= "\n";

		$ret .= "Constants:\n";
		$ret .= $this->processConstants($class->getConstants());

		$ret .= "Properties:\n";
		$ret .= $this->processProperties($class->getProperties(), $class->getName());

		$ret .= "Methods:\n";
		$ret .= $this->processMethods($class->getMethods(), $class->getName());

		$ret .= "\n";
		return $ret;
	}

	public function processConstants ($consts) {
		$ret = '';
		foreach ($consts as $const) {
			$ret .= $this->processConstant($const);
		}
		return $ret ? $ret : "\tNone\n";
	}

	public function processConstant ($const) {
		return "\t" . var_export($const,1) . "\n";
	}

	public function processProperties ($props, $className) {
		$out = array();
		foreach ($props as $prop) {
			$out[] = $this->processProperty($prop, $className);
		}
		rsort($out);
		return join("\n", $out) . "\n";
	}

	public function processProperty (ReflectionProperty $prop, $className) {
		$ret = "\t";
		$ret .= ($className != $prop->getDeclaringClass()->getName()) ? '(inherited from ' . $prop->getDeclaringClass()->getName() . ') ' : '';
		$ret .= $this->processModifiers($prop);
		$ret .= '$' . $prop->getName();
		$ret .= $prop->getDocComment() ? "\n\t" . ' - ' . $this->processComment($prop->getDocComment()) : '';
		return $ret;
	}

	public function processMethods ($mets, $className) {
		$out = array();
		foreach ($mets as $met) {
			$out[] = $this->processMethod($met, $className);
		}
		rsort($out);
		return join("\n", $out) . "\n";
	}

	public function processMethod (ReflectionMethod $met, $className) {
		$ret = "\t";
		$ret .= ($className != $met->getDeclaringClass()->getName()) ? '(inherited from ' . $met->getDeclaringClass()->getName() . ') ' : '';
		$ret .= $this->processModifiers($met);
		$ret .= 'function ';
		$ret .= $met->getName();

		$ret .= ' (' . $this->processParameters($met->getParameters()) . ') ';

		$ret .= $met->getDocComment() ? "\n\t" . ' - ' . $this->processComment($met->getDocComment()) : '';
		return $ret;
	}

	public function processParameters ($params) {
		$out = array();
		foreach ($params as $param) {
			$out[$param->getPosition()] = $this->processParameter($param);
		}
		ksort($out);
		return join(', ', $out);
	}

	public function processParameter (ReflectionParameter $param) {
		$ret = $param->isPassedByReference() ? '&' : '';
		$ret .= '$' . $param->getName();
		$ret .= $param->isDefaultValueAvailable() ? '=' . preg_replace("/\n/", '', var_export($param->getDefaultValue(),1)) : '';
		return $ret;
	}

	public function processComment ($cmnt) {
		$out = explode("\n", $cmnt);
		for ($i=0; $i<count($out); $i++) {
			$out[$i] = preg_replace('/^\/\*\*/', '', $out[$i]);
			$out[$i] = preg_replace('/^\s\s*\*[^\/]/', '', $out[$i]);
			$out[$i] = preg_replace('/\*\//', '', $out[$i]);
		}
		$out = trim(join(' ', $out));
		return preg_replace('/\s\s*/', ' ', $out);
	}
}
?>