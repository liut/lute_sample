<?php
/**
 * 日志处理
 *
 * @author liut
 * @version $Id$
 * @created 13:30 2009-04-21
 */


/**
 * 日志
 * 
 */
class Log
{
	const LEVEL_EMERG = 	0;     /* System is unusable */
	const LEVEL_ALERT = 	1;     /* Immediate action required */
	const LEVEL_CRIT = 	 	2;     /* Critical conditions */
	const LEVEL_ERROR =  	3;     /* Error conditions */
	const LEVEL_WARNING = 	4;     /* Warning conditions */
	const LEVEL_NOTICE = 	5;     /* Normal but significant */
	const LEVEL_INFO = 	 	6;     /* Informational */
	const LEVEL_DEBUG = 	7;     /* Debug-level messages */
	
	static $_levels = array('emerg', 'alert', 'crit', 'err', 'warning',
                              'notice', 'info', 'debug');
	
	/* Log types for PHP's native error_log() function. */
	const TYPE_SYSTEM = 0; /* Use PHP's system logger */
	const TYPE_MAIL = 	1; /* Use PHP's mail() function */
	const TYPE_DEBUG = 	2; /* Use PHP's debugging connection */
	const TYPE_FILE = 	3; /* Append to a file */
	
	static $_logs = array();
	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function _log($message, $priority)
	{
		$log_level = defined('LOG_LEVEL') ? LOG_LEVEL : self::LEVEL_ERR;
		$log_name = defined('LOG_NAME') ? LOG_NAME : 'SP';
		
		if($priority > $log_level) return false;
		
		$now = time();
		
		static $conf = null;
		if($conf === null) {
			$log_root = defined('LOG_ROOT') ? LOG_ROOT : '/tmp/';
			$conf = array(
				'lineFormat' => '%1$s %2$s: [%3$s] %4$s',
				'destination' => $log_root .$log_name.'_'.PHP_SAPI.'_'.date('oW', $now).'.log'
			);
		}
        /* Extract the string representation of the message. */
        $message = self::extractMessage($message);
		
		if(defined('_PS_DEBUG') && TRUE === _PS_DEBUG && PHP_SAPI !== 'cli') {
			self::$_logs[] = $priority . ' ' . $message;
		}

        /* Build the string containing the complete log line. */
        $line = self::_format($conf['lineFormat'],
                               strftime($this->_timeFormat),
                               $priority, $message);

        /* Pass the log line and parameters to the error_log() function. */
        $success = error_log($line, self::TYPE_FILE, $conf['destination']);

		return false;
	}
	
	private static function _format($format, $timestamp, $priority, $message, $ident = '')
	{
		return sprintf($format,
                       $timestamp,
                       $ident,
                       self::$_levels[$priority],
                       $message,
                       isset($file) ? $file : '',
                       isset($line) ? $line : '',
                       isset($func) ? $func : '',
                       isset($class) ? $class : '');
	}


	public static function extractMessage($message)
    {
        /*
         * If we've been given an object, attempt to extract the message using
         * a known method.  If we can't find such a method, default to the
         * "human-readable" version of the object.
         *
         * We also use the human-readable format for arrays.
         */
        if (is_object($message)) {
            if (method_exists($message, 'getmessage')) {
                $message = $message->getMessage();
            } else if (method_exists($message, 'tostring')) {
                $message = $message->toString();
            } else if (method_exists($message, '__tostring')) {
                if (version_compare(PHP_VERSION, '5.0.0', 'ge')) {
                    $message = (string)$message;
                } else {
                    $message = $message->__toString();
                }
            } else {
                $message = var_export($message, true);
            }
        } else if (is_array($message)) {
            if (isset($message['message'])) {
                if (is_scalar($message['message'])) {
                    $message = $message['message'];
                } else {
                    $message = var_export($message['message'], true);
                }
            } else {
                $message = var_export($message, true);
            }
        } else if (is_bool($message) || $message === NULL) {
            $message = var_export($message, true);
        }

        /* Otherwise, we assume the message is a string. */
        return $message;
    }

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function err($message)
	{
		return self::_log($message, self::LEVEL_ERR);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function warning($message)
	{
		return self::_log($message, self::LEVEL_WARNING);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function notice($message)
	{
		return self::_log($message, self::LEVEL_NOTICE);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function info($message)
	{
		return self::_log($message, self::LEVEL_INFO);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function debug($message)
	{
		return self::_log($message, self::LEVEL_DEBUG);
	}

	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public static function getLogs()
	{
		return self::$_logs;
	}
	
	
}