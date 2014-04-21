<?PHP

/**
 * 脚本初始化
 *
 * @version        1.0
 * @since           12:54 2009-02-09
 * @author          liut
 * @words           Init
 * @Revised Information
 * $Id$
 *
 */
//

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

if (isset($_SERVER['HTTP_MDOMAIN']) && preg_match("#^[a-z][a-z0-9]{1,9}$#i", $_SERVER['HTTP_MDOMAIN'])) {
	define('CONF_ROOT', __DIR__ . DS . '_' . $_SERVER['HTTP_MDOMAIN'] . DS );
	define('CUSTOM_DOMAIN', $_SERVER['HTTP_MDOMAIN']);
	define('HAS_MULTIDOMAIN', TRUE);
} else {
	define('CONF_ROOT', __DIR__ . DS );
}

if (!defined('APP_ROOT')) {
	include_once __DIR__ . DS . 'config.inc.php';
}

include_once LIB_ROOT . 'include/profiling.php';

// Global Loader
include_once LIB_ROOT . 'class'.DS.'Loader.php';

Loader::import(APP_ROOT . 'appclass', TRUE);
Loader::import(WEB_ROOT . '_class', TRUE);

if (defined('_PS_DEBUG') && TRUE === _PS_DEBUG) {
	set_error_handler(['Loader','printError']);
}

if (PHP_SAPI === 'cli') { // command line
	isset($argc) || $argc = $_SERVER['argc'];
	isset($argv) || $argv = $_SERVER['argv'];
}
elseif (isset($_SERVER['HTTP_HOST'])) { // http mod, cgi, cgi-fcgi
	if(headers_sent()) {
		exit('headers already sent');
	}

	if (defined('RESPONSE_NO_CACHE')) {
		header('Expires: Fri, 02 Oct 98 20:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
	}
}


