<?php
	/**
	 * File: Controller.class.php
	 * User: silenceper@gmail.com
	 * Site: https://github.com/frogphp/frogphp
	 * Date: 13-9-9
	 * Time: 上午10:25
	 * Description: 控制器基类，所有控制器必须继承此类
	 */
	class Controller extends SmartyVendor{
		/**
		 * 跳转
		 * @param $path
		 * @param string $args
		 */
		public function redirect($path, $args=""){
			$path=trim($path, "/");
			if($args!="")
				$args="/".trim($args, "/");

			if(strstr($path, "http://") || strstr($path, "https://")){
				$uri=$path;
			}elseif(strstr($path, "/")){
				$uri=__APP__.'/'.$path.$args;
			}else{
				$uri=__APP__.'/'.CONTROLLER_NAME."/".$path.$args;
			}

			if(!headers_sent()){
				header('Location: ' . $uri);
				exit;
			}else{
				exit("<meta http-equiv='Refresh' content='0';URL={$uri}'>");
			}
		}

		/**
		 * @param string $message 成功消息
		 * @param int $time 等待时间
		 */
		public function success($message='操作成功',$location='',$time=3){
			$this->assign('message',$message);
			$this->assign('time',$time);

			$uri=$this->parseUrl($location);

			$this->assign('location',$uri);
			$this->display(FROG_PATH.'common/success.htm');
			exit;
		}

		/**
		 * @param string $message 失败消息
		 * @param int $time 等待时间
		 */
		public function error($message='操作失败',$location='',$time=3){
			$this->assign('message',$message);
			$this->assign('time',$time);

			$uri=$this->parseUrl($location);

			$this->assign('location',$uri);
			$this->display(FROG_PATH.'common/error.htm');
			exit;
		}

		private function parseUrl($location){
			if(strstr($location, "http://") || strstr($location, "https://")){
				$uri=$location;
			}elseif(strstr($location, "/")){
				$uri=__APP__.'/'.$location;
			}elseif($location!=''){
				$uri=__APP__.'/'.CONTROLLER_NAME."/".$location;
			}else{
				$uri=__APP__.'/'.CONTROLLER_NAME;
			}
			return $uri;
		}
}
?>