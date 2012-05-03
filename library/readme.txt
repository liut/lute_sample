
/** $Id$ */

关于 Library 目录结构说明

/ 此目录建议不要暴露在web可以访问的位置，也建议不要放置任何需要web访问的资源，如果有需要web访问的资源，请放在在static或者sites下的相应网站目录里。
/class: 所有PHP类的根目录，其中 /class/Loader.php 是全局加载器，此目录内容是所有工作的基础
/function: 一些独立的function定义文件
/include: 即无class，也无function定义的文件
/shell: 存放用来在console shell下运行的脚本
/resource: 程序用到的资源，如字体文件、IP数据等
/third: 第三方软件，如：Smarty、JPGraph 等


关于 代码 示例：

<?PHP

// $Id$

include_once 'init.php'; // 初始化

// 主过程

// 结束


