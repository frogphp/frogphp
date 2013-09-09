<?php
/**
 * File: Controller.class.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description: 控制器基类，所有控制器必须继承此类
 */
	class Controller extends SmartyVender{
	/**
	 * [redirect description]
	 * @param  [type] $path [description]
	 * @param  string $args [description]
	 * @return [type]       [description]
	 */
	public function redirect($path, $args=""){
		$path=trim($path, "/");
		if($args!="")
			$args="/".trim($args, "/");
		if(strstr($path, "/")){
			$url=$path.$args;
		}else{
			$url=MODULE_NAME."/".$path.$args;
		}

		$uri=__APP__.'/'.$url;
		if(!headers_sent()){
			header('Location: ' . $uri);
			exit;
		}else{
			exit("<meta http-equiv='Refresh' content='0';URL={$uri}'>");
		}
	}
}
?>