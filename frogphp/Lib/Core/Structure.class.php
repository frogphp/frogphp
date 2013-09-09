<?php
/**
 * Frogphp
 * 创建项目结构
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
class Structure{
    public static $mess=array();
	//创建项目结构
	static function build_app(){
		$dirs=array(
		        APP_PATH,//项目主目录
		        LIB_PATH,
		        ACTION_PATH,
		        MODEL_PATH,
		        EXTEND_PATH,
		        COMMON_PATH,
		        CONF_PATH,
		        TPL_PATH,
				TPL_PATH.C('DEFAULT_THEME'),
				TPL_PATH.C('DEFAULT_THEME').'/Index',
		        RUNTIME_PATH,
		        RUNTIME_PATH.'Cache/',
				RUNTIME_PATH.'Comps/',
				RUNTIME_PATH.'Cache/'.C('DEFAULT_THEME'),
				RUNTIME_PATH.'Comps/'.C('DEFAULT_THEME'),
		        );
		self::mkdir($dirs);
		
		//创建公共函数库文件 function.php
	    self::touch(COMMON_PATH.'function.php',"<?php\n\t//项目全局函数可以在这里定义");
		
		//创建项目配置文件 config.php
		self::write_config();
		//创建一个 index.htm 
		self::write_index();
		//创建第一个控制器
        self::write_action();
	
	}
	
	//写入初始化配置文件
	private static function write_config(){
		$content=<<<FROG
<?php 
    //项目配置文件
    return array(
        
    );
FROG;
		self::touch(CONF_PATH.'config.php', $content);
	}
	
	//写入第一个index.htm文件
	private static function write_index(){
		$content=<<<FROG
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><{\$title}></title>
</head>
<body>
<{\$content}>
</body>
</html>
FROG;
		self::touch(TPL_PATH.C('DEFAULT_THEME').'/Index/index.htm',$content);
	}
	
	//写入第一个Action
	private static function write_action(){
		$content=<<<FROG
<?php
    class IndexAction extends Action {
        function index(){
                \$this->assign('title','hello world _ Powered by FrogPHP');
              	\$this->assign('content','hello world _ Powered by FrogPHP');
				\$this->display();
            }
    }
FROG;
		self::touch(ACTION_PATH.'IndexAction.class.php',$content);
	}
	
	/* 
	 * 优化之后的touch方法
	 * @param  String $fielname   创建的文件
	 * @param  String $str        写入的字符串
	 */
	private static function touch($filename,$str){
	    if(!file_exists($filename)){
	            if(file_put_contents($filename, $str)){
	                self::$mess[]="创建文件{$filename}成功";
	            }
	    }
	}
	
	/*
	 * 优化之后的mkdir方法 
	 * @param  array $dirs 需要创建的目录数组
	 * 
	 */
	private static function mkdir($dirs){
	    foreach ($dirs as $dir){
	        if(!file_exists($dir)){
	            if(mkdir($dir)){
	                self::$mess[]="创建目录{$dirs}成功";
	            }
	        }
	    }
	}
}