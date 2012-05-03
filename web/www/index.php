<?PHP

include_once 'init.php';

defined('DOC_ROOT') || define('DOC_ROOT', __DIR__.DS);


\Eb\Web\Dispatcher::dispatch(array(
	'namespace' => '\Eb\Web\Passport',
	'default_controller' => 'account',
	'default_action' => 'index'
));


?>
