<?php
/**
 * File: SmartyVender.class.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description: Smarty连接类
 */
require_cache(VENDOR_PATH.'smarty/Smarty.class.php');
Smarty::muteExpectedErrors();
class SmartyVendor extends Smarty{
	/**
	 * 构造方法，用于初使化Smarty对象中的成员属性
	 *
	 */
	public function __construct(){
		parent::__construct();         //调用父类构造方法
		$this->template_dir=VIEW_PATH.C('default_theme');  //模板目录
		$this->compile_dir=RUNTIME_PATH."compile/".C('default_theme');    //里的文件是自动生成的，合成的文件
		$this->caching=C('cache_start');     //设置缓存开启
		$this->cache_dir=RUNTIME_PATH."cache/".C('default_theme');  //设置缓存的目录
		$this->cache_lifetime=C('cache_lifetime');  //设置缓存的时间
		$this->debugging=C('smarty_debugging');
		$this->left_delimiter=C('left_delimiter');   //模板文件中使用的“左”分隔符号
		$this->right_delimiter=C('right_delimiter');   //模板文件中使用的“右”分隔符号
	}
	
	//重载父类的display方法
	public function display($template = null, $cache_id = null, $compile_id = null, $parent = null){
		//将部分常量分配给模板
		$this->assign('__ROOT__',__ROOT__);
		$this->assign('__APP__',__APP__);
		$this->assign('__URL__',__URL__);
		$this->assign('__ACTION__',__ACTION__);
		$this->assign('__SELF__',__SELF__);
		$this->assign('siteName',C('siteName'));
		if(is_null($template)){
			$template=CONTROLLER_NAME.'/'.ACTION_NAME.C('view_suffix');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,'/')){
			$template=strtolower($template);
			$template=$template.C('view_suffix');
		}else{
			$template=CONTROLLER_NAME.'/'.$template.C('view_suffix');
		}
		
		Debug::addmsg("使用模板 $template ");
		parent::display($template, $cache_id, $compile_id, $parent);
	}
	
	//重载父类 is_cached 方法 判断是否已经被缓存
	public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null){
		if(is_null($template)){
			$template=CONTROLLER_NAME.'/'.ACTION_NAME.C('view_suffix');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,':')){
			$template=str_replace(':', '/', $template);
			$template=$template.C('view_suffix');
		}else{
			$template=CONTROLLER_NAME.'/'.$template.C('view_suffix');
		}
		
		return parent::isCached($template, $cache_id, $compile_id, $parent);
	}
	
	/* 
	 * 重载父类的Smarty类中的方法
	 *  @param	string	$tpl_file	模板文件
	 * @param	mixed	$cache_id	缓存的ID
	 */
	public function clear_cache($template = null, $cache_id = null, $compile_id = null, $exp_time = null){
		if(is_null($template)){
			$template=CONTROLLER_NAME.'/'.ACTION_NAME.C('view_suffix');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,':')){
			$template=str_replace(':', '/', $template);
			$template=$template.C('view_suffix');
		}else{
			$template=CONTROLLER_NAME.'/'.$template.C('view_suffix');
		}
		
		return parent::clearCache($template, $cache_id, $compile_id, $exp_time);
	}
}

?>