<?php
/**
 *  Frogphp
 * App.class.php
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
class App{
	static function init(){
		// 注册AUTOLOAD方法
		spl_autoload_register('self::autoload');
		//包含核心文件
		$list=array(
				FROG_CORE.'Mytpl.class.php',
				FROG_CORE.'Db.class.php',
				FROG_CORE.'Structure.class.php',
				FROG_CORE.'Dispatcher.class.php',
				FROG_CORE.'Action.class.php',
				FROG_CORE.'Model.class.php',
		);
		foreach ($list as $filename){
			require_cache($filename);
		}
		//创建项目目录
		Structure::build_app();
		//设置URL调度   设置URL常量
		Dispatcher::dispatch();
	}
	
	static function run(){
		//安全性检测
		if(!preg_match('/^[A-Za-z_0-9]+$/',MODULE_NAME)){
			$module=false;
		}else{
			$module=MODULE_NAME.'Action';
			//判断控制器是否存在
			$srccontrolerfile=ACTION_PATH.$module.'.class.php';
			if(!file_exists($srccontrolerfile) && APP_DEBUG){
				Debug::addmsg('该控制器不存在，你应该创建一个<font color=red>'.$srccontrolerfile.'</font>的控制器！');
				return;
			}
		}
		
		if(!preg_match('/^[A-Za-z_0-9]+$/', ACTION_NAME)){
			$action=false;
		}else{
			$action=ACTION_NAME;
		}
		
		if(!$module||!$action&&APP_DEBUG){
			Debug::addmsg("<font color=red>非法操作！</font>");
			return;
		}
		
		//执行当前操作
		if(method_exists($module, $action)){
			call_user_func(array(new $module,$action));
			Debug::addmsg("当前访问的控制器是<b>".ACTION_PATH.$module.'.class.php</b>');
		}elseif(APP_DEBUG){
			Debug::addmsg("非法操作{$action}");
		}
	}
	
	
	
	public static function autoload($class){
		//加载控制器
		if(substr($class,-6)=='Action'){
			if(require_cache(ACTION_PATH.$class.'.class.php')) return ;
		//加载模块
		}elseif(substr($class,-5)=='Model'){
			if(require_cache(MODEL_PATH.$class.'.class.php')) return ;
		//加载Driver
		}elseif(substr($class,0,2)=='Db'){
			if(require_cache(FROG_DRIVER.$class.'.class.php')) return ;
		//直接加载Smarty
		}elseif($class=='Smarty'){
			if(require_cache(FROG_SMARTY.$class.'.class.php')) return ;
		}
	}
	
}