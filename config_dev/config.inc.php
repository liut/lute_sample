<?php
/** $Id$ */

if(defined('LIB_ROOT')) return;	// 防止重复定义

if('WINNT' == PHP_OS || 'Darwin' == PHP_OS) // 为 windows & macosx 下调试用，仅 beta 和 开发 环境
{
	if ('WINNT' == PHP_OS ) {
		define('LIB_ROOT', 'D:/eben/canal/library/' );
		define('WEB_ROOT',	'D:/eben/canal/web/');
		define('SKIN_ROOT',	'D:/eben/canal/skins/');
		defined('CONF_ROOT') || define('CONF_ROOT', 'D:/eben/canal/config_dev/' );
		define('LOG_ROOT', 'D:/eben/logs/' );
	}
	else { // mac osx
		define('LIB_ROOT', '/opt/sproot/library/' );
		define('WEB_ROOT',	'/opt/sproot/web/');
		define('SKIN_ROOT',	'/opt/sproot/skins/');
		defined('CONF_ROOT') || define('CONF_ROOT', '/opt/sproot/config_dev/' );
		define('LOG_ROOT', '/opt/sproot/logs/' );
	}

	defined('LOG_LEVEL') || define('LOG_LEVEL', 6 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN_SUFFIX', 'eben.cc' );  		 // for development only
	define('L_HOST_STATIC', 'm.eben.cc' );

	defined('_PS_DEBUG') || define('_PS_DEBUG', TRUE );	// DEBUG , beta only
	defined('_DB_DEBUG') || define('_DB_DEBUG', TRUE );	// DEBUG , beta only

	define('CACHE_MEMCACHE_SERVERS', 'mc.eben.net' );
	defined('CACHE_MEMCACHE_DEBUG') || define('CACHE_MEMCACHE_DEBUG', _PS_DEBUG );

	#define('ENABLE_BENCHMARK', TRUE );

}
else
{
	define('LIB_ROOT',	'/sproot/library/');  //  PHP库位置
	define('WEB_ROOT',	'/sproot/web/');	// 这里的不是全部都可以访问的
	define('SKIN_ROOT',	'/sproot/skins/');	// 模板（主题方案）主目录
	defined('CONF_ROOT') || define('CONF_ROOT', '/sproot/config/' );
	define('LOG_ROOT', '/sproot/logs/' );
	defined('LOG_LEVEL') || define('LOG_LEVEL', 4 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN_SUFFIX', 'eben.cn' );	// 域名后辍
	define('L_HOST_STATIC', 'm.eben.cn' );	// 静态资源服务器域名

	define('CACHE_MEMCACHE_SERVERS', 'mc.eben.net' );	// Memcached Servers, format: 'HOST:PORT,HOST1:PORT,HOST2'
    
}

// base
// 输出字符集
defined('RESPONSE_CHARSET') || define('RESPONSE_CHARSET', 'utf-8' );

// cookie supported domains
define('COOKIE_DOMAIN_SUPPORT', '.eben.cn .eben.cc' );


// writable paths
define('CACHE_ROOT', WEB_ROOT.'cache/' );	//
define('DATA_ROOT', WEB_ROOT.'data/' );	//
define('TEMP_ROOT', WEB_ROOT.'temp/' );	//

// urls
define('L_URL_STO', 	'http://'.L_HOST_STATIC.'/' );
define('L_URL_CSS', 	'http://'.L_HOST_STATIC.'/c/' );
define('L_URL_IMG', 	'http://'.L_HOST_STATIC.'/i/' );
define('L_URL_JS', 	'http://'.L_HOST_STATIC.'/j/' );
define('L_URL_HOME', 'http://www.'.L_DOMAIN_SUFFIX.'/' );
define('L_URL_API', 'http://api.'.L_DOMAIN_SUFFIX.'/' ); 		// API 接口相关
define('L_URL_STAT', 'http://stat.'.L_DOMAIN_SUFFIX.'/' ); 	// 统计相关，建议放在子域名
define('L_URL_USER', 'http://pp.'.L_DOMAIN_SUFFIX.'/' ); 		// 用户相关，建议放在子域名

// view / smarty
define('SMARTY_DIR',	LIB_ROOT .'third/Smarty-3.1.8/libs/');
//defined('VIEW_TEMPLATE_DIR') || define('VIEW_TEMPLATE_DIR', SKIN_ROOT . 'default/' );
define('VIEW_COMPILE_DIR', CACHE_ROOT . 'view/tpl_c' );
define('VIEW_CACHE_DIR', CACHE_ROOT . 'view/cache' );

define('VIEW_CONFIG_DIR', CONF_ROOT .'view/' );
define('VIEW_SKINS_ROOT', SKIN_ROOT );
define('VIEW_SKINS_AVAILABLE', 'default' ); // default skin1 skin2
define('VIEW_SKIN_DEFAULT', 'default' );
define('VIEW_SKIN_CURRENT', 'default' );

// PHPExcel
define('PHPEXCEL_ROOT', LIB_ROOT . 'third/PHPExcel-lite/');

