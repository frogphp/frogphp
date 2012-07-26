<?php
//url调度类
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
        	$m=(!empty($pathinfo[0]))?$pathinfo[0]:'index';//默认为index模块
        	//将数组开头的单元移出数组
        	array_shift($pathinfo);
        	//获取action
        	$a=(!empty($pathinfo[0]))?$pathinfo[0]:'index';//默认为index模块
        	//将数组开头的单元移出数组
        	array_shift($pathinfo);
        	//确保可以通过$_GET获取其他参数
        	for($i=0; $i<count($pathinfo); $i+=2){
        		@$_GET[$pathinfo[$i]]=$pathinfo[$i+1];
        	}
        	
        }
        
        //将Model  Action分别赋予常量   并全部转为小写
        defined('MODULE_NAME') or define('MODULE_NAME',ucfirst(strtolower($m)));
        defined('ACTION_NAME') or define('ACTION_NAME',strtolower($a));
    }
}