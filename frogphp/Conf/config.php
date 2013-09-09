<?php
/**
 * Frogphp
 * Frogphp 默认配置
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
return array(
		/* 数据库设置 */
		
		/*
		 *  数据库类型  确定了使用哪种数据库链接方式 
		 *  pdo  mssql  mysqli 
		 *  注意：如需使用pdo形式链接其他数据库需要在Dbpdo.class.php   get_dsn方法中手动配置
		 */
    	'DB_TYPE'               => 'mysqli',     
		
		'DB_HOST'               => 'localhost', // 服务器地址
		'DB_NAME'               => '',          // 数据库名
		'DB_USER'               => 'root',      // 用户名
		'DB_PWD'                => '123',          // 密码
		'DB_PORT'               => '3306',        // 端口
		'DB_PREFIX'             => 'frog_',    // 数据库表前缀
		'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
		'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
		'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
		'DB_DEPLOY_TYPE'        => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
		'DB_RW_SEPARATE'        => false,       // 数据库读写是否分离 主从式有效
		'DB_MASTER_NUM'         => 1, // 读写分离后 主服务器数量
		'DB_SQL_BUILD_CACHE'    => false, // 数据库查询的SQL创建缓存
		'DB_SQL_BUILD_QUEUE'    => 'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
		'DB_SQL_BUILD_LENGTH'   => 20, // SQL缓存的队列长度
		'DB_DSN'				=>'mysql:dbname=frogphp;host=localhost',
		
		'DEFAULT_THEME'			=>'default',//默认模板
		'CACHE_START'			=>true,
		'cache_lifetime'		=>3600,
		'SMARTY_DEBUGGING'		=>false,//是否开启Smarty debugging
		//默认模板分隔符
		'left_delimiter'		=>'<{',
		'right_delimiter'		=>'}>',
		
		'TMPL_SUFFIX'			=>'.htm',//默认模板后缀
		'URL_MODEL'				=>'PATHINFO',//REWRITE  PATHINFO
		);