<?php
/**
 * Custom exceptions definition file.
 */

/**
 * Top-level exception, used only in inheritance.
 * @package Spd
 * @subpackage Exceptions
 * @author Ve
 *
 */
class Spd_Exception extends Exception {}

/**
 * Top-level system exception. This one actually gets thrown.
 * @package Spd
 * @subpackage Exceptions
 * @author Ve
 *
 */
class Spd_SystemException extends Spd_Exception {}

/**
 * Top-level system IO exception.
 * @package Spd
 * @subpackage Exceptions
 * @author Ve
 *
 */
class Spd_IOException extends Spd_SystemException {}

?>