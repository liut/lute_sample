<?PHP

namespace Eb\Web;

/**
* Eb
*/
class Dispatcher
{
	/**
	 * @deprecated by dispatch()
	 */
	public static function act(array $config = array())
	{
		static::dispatch($config);
	}
	/**
	 * 执行一个调度
	 * 
	 * @param array $config
	 */
	public static function dispatch(array $config = array())
	{
		extract($config);
		isset($namespace) or $namespace = '';
		isset($default_controller) or $default_controller = 'home';
		isset($default_action) or $default_action = 'index';
		isset($request) or $request = \Request::current();
		isset($view) or $view = new \Eb_View;
		
		$params = array();
		
		$app_control = $default_controller;
		$app_method = $default_action;
		isset($uri) or $uri = isset($_SERVER['REQUEST_URI']) ? explode('/', $_SERVER['REQUEST_URI']) : array();
		
		if(is_array($uri) and count($uri) > 0){
			//var_dump($uri);
			array_shift($uri); // trim first '/'
			//controller
			$ctl = array_shift($uri);
			if(!empty($ctl)) {
				if (ctype_alnum($ctl)){ // verify controller name
					$app_control = $ctl;
				}
				else {
					$view->out(400);
				}
			}
			//action 对于php关键词进行特殊处理
			$method = array_shift($uri);
			if($method){
				if (ctype_alnum($method)){ // verify method name
					$app_method = $method;	
				}
				else {
					$view->out(400);
				}
			}
			//整理uri得到的参数
			foreach($uri as $k => $v){
				$params[] = $v;
			}
		}
		
		$app_control = $namespace . "\\Controller_" . ucfirst($app_control);
		//var_dump($app_control);
		try {
			
			$control = new $app_control($request, $view);
			$app_method = 'action_' . $app_method;

			if (!method_exists($control, $app_method)){
				$view->out(404, false);return;
				//$app_method = 'index';
			}
			//调用执行方法
			call_user_func_array(array($control, $app_method), $params);
			//$control->$app_method();
		}
		catch(Exception $e) {
			// TODO: 404 not found
			$view->out(404);
			//echo 'Not Found', PHP_EOL;
			if (defined('_PS_DEBUG') && TRUE === _PS_DEBUG) {
				throw $e;
			}

		}
	}
}

