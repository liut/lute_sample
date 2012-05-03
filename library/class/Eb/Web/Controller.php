<?PHP

namespace Eb\Web;

/**
* Controller Base
*/
abstract class Controller
{
	/**
	 * @var  Request  The current Request object
	 */
	protected $_request;

	/**
	 * @var  View  The current View object
	 */
	protected $_view;
	
	public function __construct($request = null, $view = null)
	{
		$this->_request = (is_null($request) ? \Request::current() : $request);
		$this->_view = (is_null($view) ? new \Eb_View : $view);
	}
	
	public function request()
	{
		return $this->_request;
	}
	
	public function view()
	{
		return $this->_view;
	}
}



