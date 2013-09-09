<?php
/**
 * Frogphp
 * 框架公共函数库
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */

/*
 * C 方法读取项目配置文件 
 * 获取配置值
 */
function C($name=null, $value=null) {
	//静态变量全局使用
    static $_config = array();
    // 无参数时获取所有
    if (empty($name))   return $_config;
    // 取值  赋值
    if (is_string($name)) {
        $name = strtolower($name);
        if (is_null($value)){
        	return isset($_config[$name]) ? $_config[$name] : null;
        }else{
        	$_config[$name] = $value;
        }
    }
    
    // 批量设置
    if (is_array($name)){
        return $_config = array_merge($_config, array_change_key_case($name));
    }
    return null; // 避免非法参数
}

/*
 * 	 D 方法快捷的实例化Model  或者自定义的Model 
 * 	 D('user')   可以实例化自定义模型 UserModel.class.php
 */
function D($name=''){
	//防止重复实例化   加上static
	static $_model=array();
	$name=ucfirst(strtolower($name));
	if(isset($_model[$name]))
		return $_model[$name];
	
	//实例化自定类
	$class=$name.'Model';
	if(file_exists(MODEL_PATH.$class.'.class.php') && class_exists($class))
		$_model[$name]=new $class($name);
	else 
		$_model[$name] = new  Model($name);
	
	return $_model[$name];
}

// 浏览器友好的变量输出
function dump($var, $echo=true, $label=null, $strict=true) {
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if (!$strict) {
		if (ini_get('html_errors')) {
			$output = print_r($var, true);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		} else {
			$output = $label . print_r($var, true);
		}
	} else {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
	}
	if ($echo) {
		echo($output);
		return null;
	}else
		return $output;
}

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name, $method='', $args=array()) {
	static $_instance = array();
	$identify = empty($args) ? $name . $method : $name . $method . MD5($args);
	if (!isset($_instance[$identify])) {
		if (class_exists($name)) {
			$o = new $name();
			if (method_exists($o, $method)) {
				if (!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				} else {
					$_instance[$identify] = $o->$method();
				}
			}
			else
				$_instance[$identify] = $o;
		}else{
			Debug::addmsg('不存在该类');
		}
		
		
	}
	return $_instance[$identify];
}

// 记录和统计时间（微秒）
function G($start,$end='',$dec=4) {
	static $_info = array();
	if(is_float($end)) { // 记录时间
		$_info[$start]  =  $end;
	}elseif(!empty($end)){ // 统计时间
		if(!isset($_info[$end])) $_info[$end]   =  microtime(TRUE);
		return number_format(($_info[$end]-$_info[$start]),$dec);
	}else{ // 记录时间
		$_info[$start]  =  microtime(TRUE);
	}
}

// 优化的require_once
function require_cache($filename) {
	static $_importFiles = array();
	if (!isset($_importFiles[$filename])) {
		if (is_file($filename)) {
			require $filename;
			Debug::addmsg("<b> $filename </b>", 1);  //在debug中显示自动包含的类
			$_importFiles[$filename] = true;
		} else {
			$_importFiles[$filename] = false;
			Debug::addmsg("文件{$filename}不存在",1);
		}
	}
	return $_importFiles[$filename];
}















