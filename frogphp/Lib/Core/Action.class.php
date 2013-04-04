<?php
/**
 * Frogphp 控制器基类
 * 所有控制器都必须继承此类
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp 
 */
class Action extends Mytpl{

		public function __construct() {
			//如果存在_initialize 则优先执行这个初始化接口
			if(method_exists($this,'_initialize'))
				$this->_initialize();
			parent::__construct();
	    }
		/*
		 * 用于在控制器中进行位置重定向
		 * @param	string	$path	用于设置重定向的位置
		 * @param	string	$args 	用于重定向到新位置后传递参数
		 * 
		 * $this->redirect("index")  /当前模块/index
		 * $this->redirect("user/index") /user/index
		 * $this->redirect("user/index", 'page/5') /user/index/page/5
		 */
	protected function redirect($path, $args=""){
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
		
		/*
		 * 用于成功跳转
		 * 
		 */
		
	protected function success($message,$jumpUrl=null,$waitSecond=1){
			//判断变量是否为数字
			if(is_numeric($waitSecond)){
				$this->assign('waitSecond',$waitSecond);
			}else{
				Debug::addmsg('success函数参数错误!');
				return;
			}
			$jumpUrl=$this->get_jumpUrl($jumpUrl);
			$this->assign('jumpUrl',$jumpUrl);
			$this->assign('message',$message);
			$this->display(FROG_TPL.'success.tpl');
			exit(1);
		}
		
		/*
		 * 用于失败跳转
		*
		*/
		
	protected function error($message,$jumpUrl=null,$waitSecond=3){
			//判断变量是否为数字
			if(is_numeric($waitSecond)){
				$this->assign('waitSecond',$waitSecond);
			}else{
				Debug::addmsg('success函数参数错误!');
				return;
			}
			$jumpUrl=$this->get_jumpUrl($jumpUrl);
			$this->assign('jumpUrl',$jumpUrl);
			$this->assign('message',$message);
			$this->display(FROG_TPL.'error.tpl');
			exit(1);
		}
		
		//处理跳转的url
	private function get_jumpUrl($jumpUrl){
			$this->caching=false;//关闭缓存
			if(!isset($jumpUrl)){
				$jumpUrl=$_SERVER['HTTP_REFERER'];
			}else{
				//匹配格式 ： 模块/方法
				if(strstr($jumpUrl, '/')){
					$jumpUrl=__APP__.'/'.$jumpUrl;
				}else{
				//匹配格式： 方法
					$jumpUrl=__APP__.'/'.MODULE_NAME.'/'.$jumpUrl;
				}
			}
			return $jumpUrl;
		}
}
