<?php

/**
 * Parent class for all Parsers.
 *
 * @package Spd
 * @subpackage Parsers
 * @author Ve
 *
 */
abstract class Spd_Parsers_Parser {
	/**
	 * Full path to the top-level target directory.
	 * @var string
	 */
	protected $path;

	protected $classes = array();

	/**
	 * Output string.
	 * @var string
	 */
	protected $output;

	private $_source;

	/**
	 * All parsers' entry point funciton.
	 * @return Spd_Parsers_Parser $this
	 */
	abstract public function parse ();

	/**
	 * Process classes info
	 * @param array $classes
	 * @return string Processed classes info
	 */
	abstract public function processClasses ($classes);

	/**
	 * Process single class info.
	 * @param ReflectionClass $class
	 * @return string Processed class info
	 */
	abstract public function processClass (ReflectionClass $class);

	/**
	 * Process class' constants.
	 * @param array $consts
	 * @return string Processed constants info
	 */
	abstract public function processConstants ($consts);

	/**
	 * Process single constant info
	 * @param unknown_type $const
	 * @return string Processed constant info
	 */
	abstract public function processConstant ($const);

	/**
	 * Process class properties
	 * @param array $props
	 * @param string $className
	 * @return string Processed properties info
	 */
	abstract public function processProperties ($props, $className);

	/**
	 * Process class properties
	 * @param ReflectionProperty $prop
	 * @param string $className
	 * @return string Processed property info
	 */
	abstract public function processProperty (ReflectionProperty $prop, $className);

	/**
	 * Process class methods.
	 * @param array $mets
	 * @param string $className
	 * @return string Processed methods info
	 */
	abstract public function processMethods ($mets, $className);

	/**
	 * Process a single method.
	 * @param ReflectionMethod $met
	 * @param string $className
	 * @return string Processed method info
	 */
	abstract public function processMethod (ReflectionMethod $met, $className);

	/**
	 * Process single methods' parameters
	 * @param array $params
	 * @return string Processed parameters info
	 */
	abstract public function processParameters ($params);

	/**
	 * Process single parameter
	 * @param ReflectionParameter $param
	 * @return string Processed parameter info
	 */
	abstract public function processParameter (ReflectionParameter $param);

	/**
	 * Process DocComments
	 * @param string $cmnt
	 * @return string Processed comment info
	 */
	abstract public function processComment ($cmnt);

	/**
	 * Process object modifiers.
	 * @param Reflector $obj
	 * @return string Processed modifiers
	 */
	abstract public function processModifiers (Reflector $obj);

	/**
	 * Gets $this->output value.
	 * @return string Output
	 */
	public function getOutput () {
		return $this->output;
	}

	/**
	 * Creates and sets target directory full path.
	 * @param string $p Path (fragment?)
	 */
	protected function setPath ($p) {
		$path = realpath($p);
		if (!$path) throw new Spd_IOException('No target path: ['  . $p . ']');
		if (!is_dir($path)) throw new Spd_IOException('Target path not dir: ' . var_export($p,1));
		$this->path = $path;
	}

	/**
	 * Gets path property.
	 * @return string $this->path
	 */
	public function getPath () {
		return $this->path;
	}

	/**
	 * Processes all sources and generate source tree.
	 */
	protected function processSourceTree () {
		$this->processFileTree();
		foreach (get_declared_interfaces() as $interface) {
		$ref = new ReflectionClass($interface);
			if ($ref->isUserDefined()) {
				$this->classes[] = $ref;
			}
		}
		foreach (get_declared_classes() as $class) {
			$ref = new ReflectionClass($class);
			if ($ref->isUserDefined() && !preg_match('/^Spd/', $class)) {
				$this->classes[] = $ref;
			}
		}
	}

	/**
	 * Traverses the file tree and processes each file.
	 */
	protected function processFileTree () {
		$files = $this->populateFileTree();
		foreach ($files as $file) {
			$this->processFile($file);
		}
	}

	/**
	 * Checks to see if file can be included.
	 * @param string $file File name to check
	 * @return bool Includible?
	 */
	protected function isIncludible ($file) {
		return preg_match('/\.php$/i', $file);
	}

	private function processFile ($file) {
		if (!$this->isIncludible($file)) return false;
		$this->includeFile($file);
		return true;
	}

	private function includeFile ($file) {
		ob_start();
		require_once ($file);
		ob_end_clean();
	}

	private function populateFileTree ($p=false) {
		$p = $p ? $p : $this->path;
		$files = array();

		$dir = dir($p);
		while (false !== ($entry = $dir->read())) {
			if ('.' == $entry || '..' == $entry) continue;
			$entry = $dir->path . '/' . $entry;
			if (is_file($entry)) $files[] = $entry;
			else if (is_dir($entry)) $files = array_merge ($files, $this->populateFileTree($entry));
		}
		return $files;
	}
}
?>