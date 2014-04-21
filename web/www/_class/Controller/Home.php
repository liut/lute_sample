<?PHP



/**
 * Class Controller_Home
 *
 * @package Sp_Web
 **/
class Controller_Home extends Controller
{
	/**
	 * default index
	 *
	 * @return void
	 **/
	function action_index()
	{
		return [
			'template' => 'home',
			'context' => ['title' => 'Home']
		];
	}

} // END class
