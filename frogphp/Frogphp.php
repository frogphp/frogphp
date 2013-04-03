<?php 
/**
 * Frogphp 1.1 beta
 * Frogphp 入口文件
 * 最后修改日期：2013-04-01 
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */

	//设置编码及时区
	header("Content-Type:text/html;charset=utf-8");
	date_default_timezone_set("PRC");
	session_start();//开启session
	
	//路径常量
	defined('FROG_PATH') or define('FROG_PATH',str_replace('\\', '/', dirname(__FILE__).'/')); //框架路径
	defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_NAME']).'/');//项目路径
	defined('APP_DEBUG') or define('APP_DEBUG', false);//调试模式默认关闭
	
	//框架相关路径
	defined('FROG_LIB') or define('FROG_LIB',FROG_PATH.'Lib/');
	defined('FROG_CORE') or define('FROG_CORE',FROG_LIB.'Core/');//框架核心所在目录
	defined('FROG_SMARTY') or define('FROG_SMARTY', FROG_LIB.'Smarty/');//Smarty 所在路径
	defined('FROG_EXTEND') or define('FROG_EXTEND', FROG_LIB.'Extend/'); //扩展类
	defined('FROG_DRIVER') or define('FROG_DRIVER', FROG_LIB.'Driver/'); //数据库驱动类
	defined('FROG_TPL') or define('FROG_TPL', FROG_PATH.'Tpl/');
	
	//项目路径常量
	defined('LIB_PATH') or define('LIB_PATH', APP_PATH.'Lib/');
	defined('ACTION_PATH') or define('ACTION_PATH', LIB_PATH.'Action/');//控制器所在路径
	defined('MODEL_PATH') or define('MODEL_PATH', LIB_PATH.'Model/');//模型所在路径
	defined('EXTEND_PATH') or define('EXTEND_PATH', LIB_PATH.'Extend/');//项目所用到得工具类
	defined('COMMON_PATH') or define('COMMON_PATH', APP_PATH.'Common/');//公共函数库路径
	defined('CONF_PATH') or define('CONF_PATH', APP_PATH.'Conf/');//项目配置文件路径
	defined('TPL_PATH') or define('TPL_PATH', APP_PATH.'Tpl/');//项目视图文件路径
	defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH.'Runtime/');//Runtime 路径

	include FROG_CORE."Debug.class.php";  //包含debug类
	//设置DEBUG模式
	if(APP_DEBUG){
		error_reporting(E_ALL ^ E_NOTICE);   //输出除了注意的所有错误报告
		Debug::start();                               //开启脚本计算时间
		set_error_handler(array("Debug", 'Catcher')); //设置捕获系统异常
	}else{
		ini_set('display_errors', 'Off'); 		//屏蔽错误输出
		ini_set('log_errors', 'On');             	//开启错误日志，将错误报告写入到日志中
		ini_set('error_log', RUNTIME_PATH.'error_log.txt'); //指定错误日志文件
	}
	
	//包含框架公共函数库
	require FROG_PATH.'Common/function.php';
	
	//加载框架默认配置
	if(file_exists(FROG_PATH.'Conf/config.php')) C(include FROG_PATH.'Conf/config.php');
	//包含项目配置文件  会覆盖框架默认的配置
	if(file_exists(CONF_PATH.'config.php')) C(include CONF_PATH.'config.php');
	
	//包含项目函数库    用户可以再这个文件中自定义函数
	$comm_fun=COMMON_PATH.'function.php';
	if(file_exists($comm_fun)) require $comm_fun;
	
	require_cache(FROG_CORE.'App.class.php');	
	//项目初始化  实现 创建目录 写入初始化配置，url调度
	App::init();
	//项目执行
	App::run();
	//项目结束  错误输出
	if(APP_DEBUG){
		Debug::addmsg("会话ID:".session_id());
		Debug::stop();
		Debug::message();
	}
?>
