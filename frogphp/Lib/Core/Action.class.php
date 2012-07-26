<?php
//控制器基类
class Action extends Mytpl{
		/*
		 * 用于在控制器中进行位置重定向
		 * @param	string	$path	用于设置重定向的位置
		 * @param	string	$args 	用于重定向到新位置后传递参数
		 * 
		 * $this->redirect("index")  /当前模块/index
		 * $this->redirect("user/index") /user/index
		 * $this->redirect("user/index", 'page/5') /user/index/page/5
		 */
	function redirect($path, $args=""){
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