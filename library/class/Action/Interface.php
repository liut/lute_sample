<?PHP

/**
 * Action_Interface
 * 行为执行 接口 （一般用于一次请求执行一种行为）
 *
 * @author liut
 * @version $Id$
 * @created 14:26 2009-06-02
 */


interface Action_Interface
{

	/**
	 * 执行一次行为请求
	 * 
	 * @return mixed
	 */
	public function execute($request);

	

}

