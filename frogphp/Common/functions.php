<?php
	/**
	 * 读取或设置配置文件
	 * @param null $name
	 * @param null $value
	 * @return array|null
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

	/**
	 * 优化的require_once
	 * @param $filename
	 * @return mixed
	 */
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

	/**
	 * 实例化模型
	 * @param string $name
	 * @return mixed
	 */
	function M($name=''){
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
?>
