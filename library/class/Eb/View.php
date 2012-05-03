<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Eb_View
 *
 * View 模板引擎
 *
 * @package    Sp
 * @author     liut
 * @version    $Id$
 */

if(!class_exists('Smarty', FALSE))
{
	include_once SMARTY_DIR . 'Smarty.class.php';
	
	if(!class_exists('Smarty', FALSE))
	{
		die('class Smarty not found!');
	}
}

/**
 * Eb_View
 *
 */
class Eb_View extends Smarty
{
	//protected $_head_title = '';
	//protected $_head_keywords = '';
	//protected $_head_description = '';
	protected $_head_links = array();
	protected $_head_scripts = array();
	protected $_head_styles = array();
	protected $_charset = 'UTF-8';
	protected $_parent_dir = null;
	protected $_dft_skin = 'default';
	protected $_cur_skin = '';
	protected $_foot_scripts = array();
	protected $_request = null;
	
	/**
	 * 继承Smarty并设置项目的属性
	 */
	function __construct($charset = 'UTF-8') {
		//Smarty 2 or 3 ?
		method_exists('Smarty','Smarty') && $this->Smarty() || parent::__construct();
		
		$tpl_dir = isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] . '/templates/' : '';
		if(!empty($tpl_dir) && is_dir($tpl_dir)) {
			$this->template_dir = $tpl_dir;
		}
		elseif (defined('VIEW_SKINS_ROOT') && defined('VIEW_SKIN_DEFAULT'))
		{
			$this->_parent_dir = VIEW_SKINS_ROOT;
			$this->_dft_skin = VIEW_SKIN_DEFAULT;
			//$this->template_dir = VIEW_SKINS_ROOT . $this->_dft_skin . '/';
			$this->addTemplateDir(VIEW_SKINS_ROOT . $this->_dft_skin, $this->_dft_skin);
		}
		
		is_null($this->_parent_dir) && $this->_parent_dir = CONF_ROOT . 'templates/';
		
		if (defined('VIEW_SKIN_CURRENT')) {
			$this->_cur_skin = VIEW_SKIN_CURRENT;
		} else {
			// TODO: support custom skin
			$this->_cur_skin = $this->_dft_skin;
		}
		//$this->template_dir = $this->_parent_dir . $this->_cur_skin . '/';
		$this->addTemplateDir($this->_parent_dir . $this->_cur_skin, $this->_cur_skin);
		if (defined('VIEW_TEMPLATE_DIR')) {
			//$this->template_dir = VIEW_TEMPLATE_DIR;
			$this->addTemplateDir(VIEW_TEMPLATE_DIR);
		}
		
		//$this->use_sub_dirs = true;
		$this->compile_id = (isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'local') . '_' . $this->_cur_skin;
		//$this->compile_dir = VIEW_COMPILE_DIR;
		$this->setCompileDir(VIEW_COMPILE_DIR);
		//$this->config_dir = VIEW_CONFIG_DIR;
		$this->setConfigDir(VIEW_CONFIG_DIR);
		//$this->plugins_dir = array(LIB_ROOT.'function/smarty/plugins', SMARTY_DIR.'/plugins');
		$this->addPluginsDir(LIB_ROOT.'function/smarty/plugins');

		$this->left_delimiter  =  '{';
		$this->right_delimiter =  '}';
		

		$this->_charset = $charset;
        new Eb_View_Helper($this);
		
		//$this->assign_by_ref('head_title', $this->_head_title);
		//$this->assign_by_ref('head_keywords', $this->_head_keywords);
		//$this->assign_by_ref('head_description', $this->_head_description);
		$this->assignByRef('head_links', $this->_head_links);
		$this->assignByRef('head_styles', $this->_head_styles);
		$this->assignByRef('head_scripts', $this->_head_scripts);
		$this->assignByRef('foot_scripts', $this->_foot_scripts);
		$this->assignByRef('charset', $this->_charset);
		
		defined('L_URL_HOME') && $this->assign('L_URL_HOME', L_URL_HOME);
		defined('L_URL_CSS') && $this->assign('L_URL_CSS', L_URL_CSS);
		defined('L_URL_IMG') && $this->assign('L_URL_IMG', L_URL_IMG);
		defined('L_URL_JS') && $this->assign('L_URL_JS', L_URL_JS);
		defined('L_URL_STO') && $this->assign('L_URL_STO', L_URL_STO);
		defined('L_URL_ITEM') && $this->assign('L_URL_ITEM', L_URL_ITEM);
		defined('L_URL_STAT') && $this->assign('L_URL_STAT', L_URL_STAT);
		defined('L_URL_USER') && $this->assign('L_URL_USER', L_URL_USER);
		$this->assign('COOKIE_DOMAIN', Request::genCookieDomain());
		
		// current request
		$this->_request = Request::current();
		$this->assign('_request', $this->_request);
		
		if(is_dir($this->_parent_dir)) {
			$this->assign('parent_dir', $this->_parent_dir);
		} else {
			$this->assign('parent_dir', '');
		}
		$this->assign('tpl_home_header', /* $this->_parent_dir . */'home_header.html'); 
		$this->assign('tpl_home_footer', /* $this->_parent_dir . */'home_footer.html');
		$this->default_template_handler_func = array($this, 'make_template');
		
	}
	
	/**
	 * 添加头部脚本引用的名称
	 * 
	 * @param string $name
	 * @param boolean $unshift 是否放在最前面
	 * @return void
	 */
	public function addScript($name, $unshift = false)
	{
		if(!in_array($name, $this->_head_scripts))
		{
			if($unshift) return array_unshift($this->_head_scripts, $name);
			return array_push($this->_head_scripts, $name);
		}
	}
	
	/**
	 * 添加尾部脚本引用的名称
	 * 
	 * @param string $name
	 * @param boolean $unshift 是否放在最前面
	 * @return void
	 */
	public function addFootScript($name, $unshift = false)
	{
		if(!in_array($name, $this->_foot_scripts))
		{
			if($unshift) return array_unshift($this->_foot_scripts, $name);
			return array_push($this->_foot_scripts, $name);
		}
	}
	
	/**
	 * function description
	 * 
	 * @param string $name
	 * @param boolean $unshift 是否放在最前面
	 * @return void
	 */
	public function addStyle($name, $unshift = false)
	{
		if(!in_array($name, $this->_head_styles))
		{
			if($unshift) return array_unshift($this->_head_styles, $name);
			return array_push($this->_head_styles, $name);
		}
	}
	
	/**
	 * add head link
	 * 
	 * @param array $entry
	 * @return void
	 */
	public function addHeadLink($entry)
	{
		if (is_array($entry) && count($entry) > 1) {
			$this->_head_links[] = $entry;
		}
		
	}
	
	/**
	 * function description
	 * 
	 * @return string
	 */
	public function getCharset()
	{
		return $this->_charset;
	}
	
	/**
	 * function description
	 * 
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	/**
	 * 设置页面标题
	 * 
	 * @param $title
	 * @return void
	 */
	public function setTitle($title)
	{
		//$this->_head_title = $title;
		$this->assign_by_ref('head_title', $title);
	}
	
	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public function setKeywords($kw)
	{
		$this->assign_by_ref('head_keywords', $kw);
	}
	
	
	
	/**
	 * function description
	 * 
	 * @param
	 * @return void
	 */
	public function out_404($end = true)
	{
		$this->out(404, $end);
	}

	/**
	 * output error status page and exit
	 * 
	 * @param $code int status code
	 * @param $end boolean exit
	 * @return void
	 */
	public function out($code = 404, $end = true)
	{
		static $status = array(
			400 => '400 Bad Request',
			401 => '401 Unauthorized',
			402 => '402 Payment Required',
			403 => '403 Forbidden',
			404 => '404 Not Found',
			405 => '405 Method Not Allowed',
			406 => '406 Not Acceptable',
		);
		$code = (int)$code;
		if (!isset($status[$code])) {
			$code = 404;
		}
		header("HTTP/1.1 ".$status[$code]);
		$this->assign('message', $status[$code]);
		$this->display('error/'.$code.'.htm');
		$end && exit();	
	}

    /**
     * displays a Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
	{
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && preg_match('/MSIE (7|8)/', $this->_request->getAgent())) { // 检测是否为IE
			$content = $this->fetch($template, $cache_id, $compile_id, $parent);
			$content = str_replace(array('http://'.L_HOST_STATIC.'/',L_URL_STAT,L_URL_USER), '/', $content);
			//$content = str_replace('/wanlitong/', L_URL_USER.'wanlitong/', $content);
			echo $content;
		} else {
			//$g_view->assign("res_bundle", FALSE);
			parent::display($template, $cache_id, $compile_id, $parent);
		}
	}
	
	protected function make_template ($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
	{
		if( $resource_type == 'file' ) {
			if ( ! is_readable ( $resource_name )) {
				$skins = array($this->_cur_skin);
				if ($this->_cur_skin != $this->_dft_skin) {
					$skins[] = $this->_dft_skin;
				}
				foreach($skins as $skin) {
					$file = $this->_parent_dir . $skin . '/' . $resource_name;
					if (is_readable ( $file )) {
						$template_source = file_get_contents($file);
						return true;
					}
				}
			}
		}
		// not a file
		return false;
		
	}
	
	/**
	 * 返回当前选择的skin名称
	 */
	public function getSkin(){
		return $this->_cur_skin;
	}
	
}


/**
 * Eb_View_Helper
 *
 */
class Eb_View_Helper
{
	private $_view = null;
	/**
	 * 构造函数
	 *
	 * @param Smarty $view
	 */
    public function __construct($view) 
	{
		if(is_object($view))
		{
			$this->_view = & $view;
			#$charset = $view->getCharset();
			#empty($charset) || mb_internal_encoding($charset);
	        $view->registerPlugin('modifier', 'truncate',    array(& $this, '_modifier_truncate'));
	        //$view->registerPlugin('modifier', 'strcut',    array(& $this, '_modifier_strcut'));
	        $view->registerPlugin('modifier', 'url_thumb',    array(& $this, '_modifier_url_thumb'));
			#$view->registerPlugin('modifier', 'a_nickname',    array(& $this, '_modifier_a_nickname'));
	        $view->registerPlugin('modifier', 'url_avatar',    array(& $this, '_modifier_url_avatar'));

	        $view->registerPlugin('modifier', 'to_gbk',    array(& $this, '_modifier_to_gbk'));
	        $view->registerPlugin('modifier', 'url_item',    array(& $this, '_modifier_url_item'));
		}
    }

	/**
	 * function description
	 * 
	 * @param string $url
	 * @return void
	 */
	public function _modifier_url_thumb($url, $size = '100x100')
	{
		if (strncasecmp($url, 'http://sto.', 11) === 0) {
			return preg_replace("#(http://sto\.[a-z\.]+/)(.{5,})#i", "$1/thumb/s$size/$2", $url);
		}
		return $url;
	}
	

	
	/**
	 * Smarty truncate modifier plugin, 根据宽度输出字符
	 *
	 * Type:     modifier<br>
	 * Name:     truncate<br>
	 * Purpose:  Smary 自带的truncate只支持英文半角， 这里是修改版本， 去除了$break_word和middle最后两个参数,
	 *           修改支持全角各类UTF或中文字符
	 *           本函数主要是依赖字符宽度控制.
	 *           如果是处理UTF-8，需要提前设置 mb_internal_encoding("UTF-8") .
	 * @author   liut < Eagle.L at gmail dot com >
	 * @param string $string
	 * @param integer $length
	 * @param string $etc
	 * @return string
	 */
	 function _modifier_truncate($string, $length = 80, $etc = '…')
	{

		if ($length == 0) {
	        return '';
		}
		return mb_strimwidth($string, 0, $length, $etc);

	}
	
	/**
	 * 根据用户Email查找用户头像地址
	 * 
	 * @param
	 * @return void
	 */
	 function _modifier_url_avatar($email,$size = 23)
	{
		return Eb_Account::makeAvatar($email,$size);
	}
	
	
	/**
	 * 将UTF-8 转码为 GBK
	 * 
	 * @param  string $text
	 * @return void
	 */
	 function _modifier_to_gbk($text)
	{
		return iconv('utf-8', 'gbk',$text);
	}
	
	/**
	 * 将商品 id 转换成 商品页 url
	 * 
	 * @param  int $id
	 * @return string
	 */
	 function _modifier_url_item($id)
	{
		return L_URL_ITEM . $id . '/';
	}
	
}


