
/** $Id$ */

关于 Library 目录结构说明

/ 此目录建议不要暴露在web可以访问的位置，也建议不要放置任何需要web访问的资源，如果有需要web访问的资源，请放在在static或者www里。
/class: 所有PHP类的根目录，其中 /class/Loader.php 是全局加载器，此目录内容是所有工作的基础
/config: 配置文件的位置，初期为空
/function: 除了类，还有一些独立的function定义文件
/include: 即无class，也无function定义的文件
/shell: 存放用来在console shell下运行的脚本
/resource: 程序用到的资源，如字体文件、IP数据等
/third: 第三方软件，如：Smarty、JPGraph等


关于 Library 使用示例：

<?PHP

// 必须先定义 LIB_ROOT ，例如：/data/php/library
defined('LIB_ROOT') || define ('LIB_ROOT', '/data/php/library/' );
// Global Loader
include_once LIB_ROOT . 'class/Loader.php'; 


// 主过程，这之后可以使用任意已定义的类，例如：

	// 显示图形验证码
	$captcha = Util_Captcha::getInstance();
	$captcha->display();	// display image and exit
	
	
	// 使用Memcache
	define('CACHE_MEMCACHE_SERVERS', 'cache1:11211,cache2,cache3,cache4');
	$cache = Cache_Memcache::getInstance();
	$cache->init();
	$key = 'key1';
	$data = 'abc';
	$ret = $cache->set($key, $data, 60);	// set
	$c_data = $cache->get($key);		// get
	
	
	// 
	
	
	
	
	
	