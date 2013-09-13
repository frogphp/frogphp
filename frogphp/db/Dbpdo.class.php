<?php
/**
 * File: Dbpdo.class.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description: pdo连接类
 */
	class Dbpdo implements Dbinter{
		public static $_pdo=NULL;
		public static $PDOStatement=NULL;
		public static $config=NULL;
		public $lastInsertId=NULL;

		/**
		 * 获取连接信息
		 * @param $config
		 */
		public function __construct($config){
			self::$config=$config;
		}

		/**
		 * 准备好一条语句
		 * @param $sql
		 */
		public function prepare($sql){
			self::$PDOStatement=self::$_pdo->prepare($sql);
		}

		/**
		 * 连接数据库
		 * @return null|PDO
		 */
		public static function connect(){
			if(is_null(self::$_pdo) || !self::$_pdo){
				$config=self::$config;
				$pdo=new PDO($config['connectionString'], $config['username'], $config['password'], array(PDO::ATTR_PERSISTENT=>true));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$charset=$config['charset'];
				$pdo->query("SET NAMES $charset");
				self::$_pdo=$pdo;
			}

			return self::$_pdo;
		}


		/**
		 * 执行一条具有结果集的sql语句 返回一条记录
		 * @param $sql
		 * @param array $params
		 * @return mixed
		 */
		public function queryRow($sql,$params=array()){
			if(!empty(self::$PDOStatement))$this->free();

			$this->prepare($sql);

			if(empty($params)){
				self::$PDOStatement->execute();
			}else{
				self::$PDOStatement->execute($params);
			}

			return self::$PDOStatement->fetch(PDO::FETCH_ASSOC);
		}

		/**
		 * 执行一条具有结果集的sql语句 并返回执行所有的信息
		 * @param $sql
		 * @param array $params
		 * @return mixed
		 */
		public function query($sql,$params=array()){
			if(!empty(self::$PDOStatement))$this->free();

			$this->prepare($sql);

			if(empty($params)){
				self::$PDOStatement->execute();
			}else{
				self::$PDOStatement->execute($params);
			}

			return self::$PDOStatement->fetchAll(PDO::FETCH_ASSOC);
		}

		/**
		 * 执行一条没有结果集的sql语句
		 * @param $sql
		 * @param array $params
		 * @return mixed
		 */
		public function execute($sql,$params=array()){
			if(!empty(self::$PDOStatement))$this->free();
			$this->prepare($sql);
			if(empty($params)){
				return self::$PDOStatement->execute();
			}else{
				return self::$PDOStatement->execute($params);
			}
		}

		/**
		 * 获取最后插入的id
		 * @return mixed
		 */
		public function getLastInsertId(){
			return self::$_pdo->lastInsertId();
		}

		/**
		 * 开启事物处理
		 */
		public function beginTransaction(){
			self::$_pdo->beginTransaction();
		}

		/**
		 * 提交事物
		 */
		public function commit(){
			self::$_pdo->commit();
		}

		/**
		 * 事物回滚
		 */
		public function rollback(){
			self::$_pdo->rollback();
		}

		/**
		 *  捕获sql错误
		 * @return string
		 */
		public function error(){
			if(self::$PDOStatement) {
				$error = self::$PDOStatement->errorInfo();
				$this->error = $error[2];
			}else{
				$this->error = '';
			}
			return $this->error;
		}

		/**
		 * 释放结果集
		 */
		public function free() {
			self::$PDOStatement = null;
		}

		/**
		 * 关闭数据库连接
		 */
		public function close(){
			self::$_pdo=NULL;
		}

	}
 ?>