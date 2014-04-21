<?PHP

if(defined('APP_ROOT')) return;	// 防止重复定义

define('APP_ROOT', dirname(__DIR__) . DS);
define('LIB_ROOT', APP_ROOT . 'library' . DS);
define('WEB_ROOT', APP_ROOT . 'web' . DS);
define('SKIN_ROOT', APP_ROOT . 'skins' . DS);

// writable paths
define('CACHE_ROOT', WEB_ROOT.'cache/' );	//
define('DATA_ROOT', WEB_ROOT.'data/' );	//

if ('WINNT' == PHP_OS) {
	define('LOG_ROOT', DS . 'logs' . DS);
	define('TEMP_ROOT', DS . 'temp' . DS);
}
else {
	define('LOG_ROOT', '/var/log/wproot/');
	define('TEMP_ROOT', '/var/tmp/wproot/' );
}

define('LOG_NAME', 'SP'); // 日志文件前缀, SP 是默认值

if('WINNT' == PHP_OS || 'Darwin' == PHP_OS || isset($_SERVER['LOCAL_DEV'])) // 为 windows & macosx 下调试用，仅 beta 和 开发 环境
{
	defined('LOG_LEVEL') || define('LOG_LEVEL', 6 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN', 'lute-demo.cc' );
	define('L_RES_SUFFIX', '.lute-static.cc' );
	define('L_STO_SUFFIX', 'WINNT' == PHP_OS ? '.lute-demo.info' : '.lute-static.cc');
	define('L_MAN_SUFFIX', '.lute-demo.cc');


	defined('_PS_DEBUG') || define('_PS_DEBUG', TRUE );	// DEBUG , beta only
	defined('_DB_DEBUG') || define('_DB_DEBUG', TRUE );	// DEBUG , beta only

}
else
{
	defined('LOG_LEVEL') || define('LOG_LEVEL', 6 ); // 3=err,4=warn,5=notice,6=info,7=debug
	define('L_DOMAIN', 'lute-demo.info' );	// 域名
	define('L_RES_SUFFIX', '.lute-demo.info' );	// 静态资源后辍
	define('L_STO_SUFFIX', '.lute-demo.info'); 	// 存储的(图片)资源后辍
	define('L_MAN_SUFFIX', '.lute-demo.info'); // 管理所用域名
}

// 输出字符集
defined('RESPONSE_CHARSET') || define('RESPONSE_CHARSET', 'utf-8' );

// cookie supported domains
define('COOKIE_DOMAIN_SUPPORT', '.lute-demo.info .lute-demo.cc' );

// urls
define('L_URL_STO', 	'http://'.L_RES_SUFFIX.'/' );
define('L_URL_CSS', 	'http://'.L_RES_SUFFIX.'/c/' );
define('L_URL_IMG', 	'http://'.L_RES_SUFFIX.'/i/' );
define('L_URL_JS', 	'http://'.L_RES_SUFFIX.'/j/' );
define('L_URL_HOME', 'http://www.'.L_DOMAIN.'/' );
define('L_URL_API', 'http://api.'.L_DOMAIN.'/' ); 		// API 接口相关
define('L_URL_STAT', 'http://stat.'.L_DOMAIN.'/' ); 	// 统计相关，建议放在子域名
define('L_URL_USER', 'http://pp.'.L_DOMAIN.'/' ); 		// 用户相关，建议放在子域名

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

