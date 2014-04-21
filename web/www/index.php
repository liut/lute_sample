<?PHP

include_once dirname(dirname(__DIR__)) . '/config/init.php';

Loader::import(__DIR__ . DS . '_class');

defined('DOC_ROOT') || define('DOC_ROOT', __DIR__.DS);


Dispatcher::start(array(
	'default_controller' => 'home',
	'default_action' => 'index'
));

