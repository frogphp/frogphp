<?php
/**
 * Frogphp
 * Mytpl.class.php   
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
class Mytpl extends Smarty{
	/**
	 * 构造方法，用于初使化Smarty对象中的成员属性
	 *
	 */
	function __construct(){
		parent::__construct();         //调用父类构造方法
		$this->template_dir=TPL_PATH.C('DEFAULT_THEME');  //模板目录
		$this->compile_dir=RUNTIME_PATH."Comps/".C('DEFAULT_THEME');    //里的文件是自动生成的，合成的文件
		$this->caching=C('CACHE_START');     //设置缓存开启
		$this->cache_dir=RUNTIME_PATH."Cache/".C('DEFAULT_THEME');  //设置缓存的目录
		$this->cache_lifetime=C('cache_lifetime');  //设置缓存的时间
		$this->debugging=C('SMARTY_DEBUGGING');
		$this->left_delimiter=C('left_delimiter');   //模板文件中使用的“左”分隔符号
		$this->right_delimiter=C('right_delimiter');   //模板文件中使用的“右”分隔符号
	}
	
	//重载父类的display方法
	function display($template = null, $cache_id = null, $compile_id = null, $parent = null){
		//将部分常量分配给模板
		$this->assign('__ROOT__',__ROOT__);
		$this->assign('__BASE__',__BASE__);
		$this->assign('__APP__',__APP__);
		$this->assign('__URL__',__URL__);
		$this->assign('__ACTION__',__ACTION__);
		$this->assign('__SELF__',__SELF__);
		$this->assign('ACTION_NAME',ACTION_NAME);
		$this->assign('MODULE_NAME',MODULE_NAME);
		if(is_null($template)){
			$template=MODULE_NAME.'/'.ACTION_NAME.C('TMPL_SUFFIX');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,':')){
			$template=str_replace(':', '/', $template);
			$template=$template.C('TMPL_SUFFIX');
		}else{
			$template=MODULE_NAME.'/'.$template.C('TMPL_SUFFIX');
		}
		
		Debug::addmsg("使用模板 <b> $template </b>");
		parent::display($template, $cache_id, $compile_id, $parent);
	}
	
	//重载父类 is_cached 方法 判断是否已经被缓存
	public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null){
		if(is_null($template)){
			$template=MODULE_NAME.'/'.ACTION_NAME.C('TMPL_SUFFIX');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,':')){
			$template=str_replace(':', '/', $template);
			$template=$template.C('TMPL_SUFFIX');
		}else{
			$template=MODULE_NAME.'/'.$template.C('TMPL_SUFFIX');
		}
		
		return parent::isCached($template, $cache_id, $compile_id, $parent);
	}
	
	/* 
	 * 重载父类的Smarty类中的方法
	 *  @param	string	$tpl_file	模板文件
	 * @param	mixed	$cache_id	缓存的ID
	 */
	public function clearCache($template=null, $cache_id = null, $compile_id = null, $exp_time = null, $type = null){
		if(is_null($template)){
			$template=MODULE_NAME.'/'.ACTION_NAME.C('TMPL_SUFFIX');
		}elseif(strstr($template,'.')){
			//直接使用模板路径
		}elseif(strstr($template,':')){
			$template=str_replace(':', '/', $template);
			$template=$template.C('TMPL_SUFFIX');
		}else{
			$template=MODULE_NAME.'/'.$template.C('TMPL_SUFFIX');
		}
		
		return parent::clearCache($template, $cache_id, $compile_id, $exp_time, $type);
	}
}

?>