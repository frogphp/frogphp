<?php
/**
 * File: config.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description:全局默认配置
 */
return array(
	//默认使用的模板名
	'default_theme'			=>'default',
	//是否启用缓存
	'cache_start'			=>true,
	//缓存时间
	'cache_lifetime'		=>3600,
	//开启Smarty debugging
	'smarty_debugging'		=>false,

	//默认模板分隔符
	'left_delimiter'		=>'<{',
	'right_delimiter'		=>'}>',
	//默认模板后缀
	'view_suffix'			=>'.htm',
	
	//数据库配置  主数据库
	'db'=>array(
		'connectionString'=>'mysql:host=localhost;dbname=test',
		'dbType'=>'pdo',
		'username'=>'root',
		'password'=>'123',
		'tablePrefix'=>'frog_',
		'charset'=>'utf8'
	),
/*
	//从数据库
	'db1'=>array(
		'connectionString'=>'mysql:host=localhost;dbname=test',
		'dbType'=>'pdo',
		'username'=>'root',
		'password'=>'123',
		'tablePrefix'=>'frog_',
		'charset'=>'utf8'
	),
*/
);