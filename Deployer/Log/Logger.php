<?php

namespace Deployer\Log;

use Deployer\Log\LoggerInterface as PsrLoggerInterface;
use Deployer\Log\LogLevel as PsrLogLevel;

class Logger implements PsrLoggerInterface {

	protected $log;

	public static function file( $filename ) {
		if ( ! file_exists( $filename ) ) {
			// No log
			$logger       = new static();
			$logger->log  = 'No log';
			$logger->file = $filename;

			return $logger;
		}

		// Read log from file
		$logger       = new static();
		$logger->log  = file_get_contents( $filename );
		$logger->file = $filename;

		return $logger;
	}

	public function __toString() {
		return $this->log;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function emergency( $message, array $context = [] ) {
		$this->log( PsrLogLevel::EMERGENCY, $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function alert( $message, array $context = [] ) {
		$this->log( PsrLogLevel::ALERT, $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function critical( $message, array $context = [] ) {
		$this->log( PsrLogLevel::CRITICAL, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function error( $message, array $context = [] ) {
		$this->log( PsrLogLevel::ERROR, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function warning( $message, array $context = [] ) {
		$this->log( PsrLogLevel::WARNING, $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function notice( $message, array $context = [] ) {
		$this->log( PsrLogLevel::NOTICE, $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function info( $message, array $context = [] ) {
		$this->log( PsrLogLevel::INFO, $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function debug( $message, array $context = [] ) {
		$this->log( PsrLogLevel::DEBUG, $message, $context );
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function log( $level, $message, array $context = [] ) {
		if ( get_option( 'deployer_logging_enabled' ) != 1 ) {
			// Do nothing
			return;
		}

		$reflection = new \ReflectionClass( 'Deployer\Log\LogLevel' );
		$levels     = $reflection->getConstants();

		if ( ! in_array( $level, $levels, true ) ) {
			// Log level not allowed.

			return;
		}

		$date    = date( 'Y-m-d H:i:s' );
		$level   = strtoupper( $level );
		$message = $this->interpolate( $message, $context );
		$entry   = "[{$date}] {$level}: $message\n";

		file_put_contents( $this->file, $entry, FILE_APPEND );
	}

	public function clear() {
		file_put_contents( $this->file, '' );
		$this->log = '';
	}

	/**
	 * Interpolates context values into the message placeholders.
	 *
	 * @param $message
	 * @param array   $context
	 * @return string
	 */
	protected function interpolate( $message, array $context = [] ) {
		// build a replacement array with braces around the context keys
		$replace = [];
		foreach ( $context as $key => $val ) {
			$replace[ '{' . $key . '}' ] = $val;
		}

		// interpolate replacement values into the message and return
		return strtr( $message, $replace );
	}
}
