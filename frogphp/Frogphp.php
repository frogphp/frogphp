<?php
/**
 * File: Frogphp.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description: 框架入口文件
 */

//设置编码及时区
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
//开启session
session_start();

//路径常量
defined('APP_DEBUG') or define('APP_DEBUG', false);

defined('FROG_PATH') or define('FROG_PATH',str_replace('\\', '/', dirname(__FILE__).'/')); 

defined('FROG_BASE') or define('FROG_BASE',FROG_PATH.'base/');
defined('FROG_DB') or define('FROG_DB',FROG_PATH.'db/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('CONTROLLER_PATH') or define('CONTROLLER_PATH', APP_PATH.'controllers/');
defined('MODEL_PATH') or define('MODEL_PATH', APP_PATH.'models/');
defined('VENDOR_PATH') or define('VENDOR_PATH', FROG_PATH.'vendors/');
defined('COMMON_PATH') or define('COMMON_PATH', APP_PATH.'common/');
defined('VIEW_PATH') or define('VIEW_PATH', APP_PATH.'views/');
defined('PLUGIN_PATH') or define('PLUGIN_PATH', APP_PATH.'plugin/');
defined('RUNTIME_PATH') or define('RUNTIME_PATH', APP_PATH.'Runtime/');

//包含debug类
include FROG_BASE."Debug.class.php";  
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
include FROG_PATH.'common/functions.php';

//加载框架默认配置
if(file_exists(FROG_PATH.'common/config.php')) C(include FROG_PATH.'common/config.php');

//包含项目配置文件  会覆盖框架默认的配置
if(file_exists(APP_PATH.'common/config.php')) C(include COMMON_PATH.'config.php');

//包含项目函数库    用户可以再这个文件中自定义函数
$comm_fun=COMMON_PATH.'function.php';
if(file_exists($comm_fun)) require_cache($comm_fun);

require_cache(FROG_BASE.'App.class.php');	

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
