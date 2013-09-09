<?php
/**
 * Frogphp
 * url调度类
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
class Dispatcher{
    static function dispatch(){
        if(empty($_SERVER['PATH_INFO'])){
            //获取control
            $m=!empty($_GET['m'])?$_GET['m']:'index';//默认为index模块
            //获取action
            $a=!empty($_GET['a'])?$_GET['a']:'index';//默认为index操作
        }else{
            //pathinfo模式
            $pathinfo=explode('/', trim($_SERVER['PATH_INFO'],'/'));
            //获取control
        	$m=(!empty($pathinfo[0])) ? $pathinfo[0]:'index';//默认为index模块
        	//将数组开头的单元移出数组
        	array_shift($pathinfo);
        	//获取action
        	$a=(!empty($pathinfo[0])) ? $pathinfo[0]:'index';//默认为index模块
        	//将数组开头的单元移出数组
        	array_shift($pathinfo);
        	//确保可以通过$_GET获取其他参数
        	for($i=0; $i < count($pathinfo); $i+=2){
        		@$_GET[$pathinfo[$i]]=$pathinfo[$i+1];
        	}
        	
        }
        
        //将Model  Action分别赋予常量   并全部转为小写 MODULE_NAME首字母大写
        defined('MODULE_NAME') or define('MODULE_NAME',ucfirst(strtolower($m)));
        defined('ACTION_NAME') or define('ACTION_NAME',strtolower($a));
        //ROOT目录
        defined('__ROOT__') or define('__ROOT__',str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']));
        defined('__BASE__') or define('__BASE__',str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])));
        if(C('URL_MODEL') == 'REWRITE'){
        	//当前项目地址  URL_REWRITE 重写模式
        	$url    =   dirname($_SERVER['SCRIPT_NAME']);
        	if($url == '/' || $url == '\\')
        		$url    =   '';
        	defined('__APP__') or define('__APP__',$url);
        }else{
        	//当前项目（入口文件）地址
        	defined('__APP__') or define('__APP__',$_SERVER['SCRIPT_NAME']);
        }
        
        //当前模块的URL 使用小写
        defined('__URL__') or define('__URL__', strtolower(__APP__.'/'.MODULE_NAME));
        //当前操作的URL地址
        defined('__ACTION__') or define('__ACTION__', strtolower(__APP__.'/'.MODULE_NAME.'/'.ACTION_NAME));
        //当前URL地址
        defined('__SELF__') or define('__SELF__', strtolower($_SERVER['REQUEST_URI']));
    }
}