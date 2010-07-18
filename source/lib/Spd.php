<?php
require_once dirname(__FILE__) . '/exceptions.php';

/**
 * Spd factory class - pure static.
 *
 * Main entry point for Spd system.
 * @package	Spd
 * @author Ve
 *
 */
class Spd {
	/**
	 * Text parser constant.
	 * @var string
	 */
	const PARSER_TEXT = 'Text';

	/**
	 * Html parser constant.
	 * @var string
	 */
	const PARSER_HTML = 'HtmlBody';

	/**
	 * Generic parser making factory method.
	 *
	 * @param string $type Parser type to be created
	 * @param string $path Target path
	 */
	public static function getParser ($type, $path) {
		$className = self::loadParser($type);
		return new $className($path);
	}

	/**
	 * Parser class loader method.
	 *
	 * @param string $type Parser type
	 */
	public static function loadParser ($type) {
		$className = 'Spd_Parsers_' .
			ucfirst(strtolower($type)) .
			'Parser';
		if (class_exists($className)) {
			return $className;
		}
		$file = dirname(__FILE__) .
			'/Parsers/' .
			ucfirst(strtolower($type)) .
			'Parser' .
			'.php';
		if (!file_exists($file)) {
			throw new Spd_SystemException('Can\'t locate class: ' . $file);
		}
		@require_once ($file);
		if (!class_exists($className)) {
			throw new Spd_SystemException('Error loading class: ' . $className);
		}
		return $className;
	}

	/**
	 * Private constructor.
	 */
	private function __construct () {}

	/**
	 * Private __clone.
	 */
	private function __clone () {}
}