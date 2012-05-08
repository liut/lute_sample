<?PHP


/**
* Eb
*/
class Dispatcher
{
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
		
		$control_name = $default_controller;
		$method_name = $default_action;
		isset($uri) or $uri = isset($_SERVER['REQUEST_URI']) ? explode('/', $_SERVER['REQUEST_URI']) : array();
		
		if(is_array($uri) and count($uri) > 0){
			//var_dump($uri);
			array_shift($uri); // trim first '/'
			//controller
			$ctl = array_shift($uri);
			if(!empty($ctl)) {
				if (ctype_alnum($ctl)){ // verify controller name
					$control_name = $ctl;
				}
				else {
					$view->out(400);
				}
			}
			//action 对于php关键词进行特殊处理
			$method = array_shift($uri);
			if($method){
				if (ctype_alnum($method)){ // verify method name
					$method_name = $method;	
				}
				else {
					$view->out(400);return;
				}
			}
			//整理uri得到的参数
			// TODO: 待优化
			foreach($uri as $k => $v){
				$params[] = $v;
			}
		}
		$class_control = "Controller_" . ucfirst($control_name);
		if (!empty($namespace)) {
			$class_control = $namespace . "\\" . $class_control;
		}
		
		//var_dump($control_name);
		try {
			
			$obj_ctl = new $class_control($request, $view);
			$action_name = 'action_' . $method_name;
			
			//调用执行方法
			if (method_exists($obj_ctl, $action_name)) {
				$response = call_user_func_array(array($obj_ctl, $action_name), $params);
			} elseif (method_exists($obj_ctl, '__call')) {
				$response = $obj_ctl->__call($method_name, $params);
			} else {
				$view->out(404, false);return;
				//$method_name = 'index';
			}
			static::send($response, $view, $method_name); // TODO: 
			//$control->$method_name();
		}
		catch(Exception $e) {
			// TODO: 404 not found
			$view->out(404, false);
			//echo 'Not Found', PHP_EOL;
			if (defined('_PS_DEBUG') && TRUE === _PS_DEBUG) {
				throw $e;
			}

		}
	}
	
	/**
	 * send response
	 * @param mixed $response
	 * @param View $view
	 * @param string $method
	 * @return void
	 */
	public static function send($response, $view, $method)
	{
		// TODO: 以下代码实现较粗糙，待完善和重构
		if (is_int($response)) {
			$view->out($response, false);
			return;
		}
		if (!is_array($response)) {
			return;
		}
		
		if (isset($response[0]) && is_string($response[0])) {
			if ($response[0] == 302 && is_string($response[1])) { //302 && '302'
				Loader::redirect($response[1]);
				return;
			}
			
			if (isset($response[1]) && is_array($response[1])) {
				$view->assign($response[1]);
			} elseif (isset($response['context']) && is_array($response['context'])) {
				$view->assign($response['context']);
			}
			$template = $response[0] . '.tpl';
			$view->display($template);
			return;
		}
		
		extract($response, EXTR_SKIP);
		if (isset($cookies)) {
			// TODO: setcookie
		}
		
		if (isset($location)) { // redirect
			Loader::redirect($location);
			return;
		}
		
		if (isset($content_type)) { // custom content type
			//header("Content-Type: $content_type");
			isset($charset) or $charset = 'UTF-8';
			header('Content-Type: '.$content_type.'; charset='.$charset);
		}
		if (isset($no_cached)) Loader::nocache();
		
		if (isset($headers)) {
			foreach($headers as $h) {
				if (is_string($h))
					header($h);
			}
		}
		
		if (isset($template)) {
			if (isset($context)) $view->assign($context);
			$template = $template . '.tpl';
			$view->display($template);
		} elseif (isset($content)) {
			echo $content;
		}
	}
}

