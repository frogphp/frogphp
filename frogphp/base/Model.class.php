<?php
/**
 * File: Model.class.php
 * User: silenceper@gmail.com
 * Site: https://github.com/frogphp/frogphp
 * Date: 13-9-9
 * Time: 上午10:25
 * Description: 模型基类，所有模型都必须继承此类
 */
class Model{
	//主数据库
	protected $_link="db";
	//handle
	private $_db=NULL;
	//sql
	public $sql=NULL;
	//最后插入id
	public $lastInsertId=NULL;
	//表前缀
	public $tablePrefix=NULL;
	//事物执行次数
	public $transTimes=0;

	public function __construct(){
		//使用默认数据库
		$this->db($this->_link);
	}

	/**
	 * 加载数据库驱动
	 * @param $link
	 */
	public function db($link){
		//读取相应数据库配置
		$dbConfig=C($link);

		$this->tablePrefix=$dbConfig['tablePrefix'];
		$dbType=$dbConfig['dbType'];
		$className='Db'.$dbType;

		static $_instance = array();
		if(isset($_instance[$link])){
			$this->_db=$_instance[$link];
		}else{
			$this->_db=new $className($dbConfig);
		}
	}

	/**
	 * 执行具有返回值的sql
	 * @param $sql
	 * @param array $params
	 * @return mixed
	 */
	public function query($sql,$params=array()){
		$this->_db->connect();
		$this->sql=$sql;
		$sql=$this->parseTablePrefix($sql);
		Debug::addmsg($sql,2);
		try{
			$result=$this->_db->query($sql,$params);
		}catch(PDOException $e){
			Debug::addmsg($this->error());
			return false;
		}
		return $result;
	}

	/**
	 * 执行无结果集的sql语句
	 * @param $sql
	 * @param array $params
	 * @return mixed
	 */
	public function execute($sql,$params=array()){
		$this->_db->connect();
		$this->sql=$sql;
		$sql=$this->parseTablePrefix($sql);
		Debug::addmsg($sql,2);
		try{
			$s=$this->_db->execute($sql,$params);
			if($s && preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql)) {
				$this->getLastInsertId();
			}
		}catch(PDOException $e){
			Debug::addmsg($this->error());
			return false;
		}

		return $s;
	}

	/**
	 * 获取最后插入的id
	 * @return mixed
	 */
	public function getLastInsertId(){
		$this->lastInsertId=$this->_db->getLastInsertId();
	}

	/**
	 * 解析表名
	 * @param $sql
	 * @return mixed
	 */
	public function parseTablePrefix($sql){
		return preg_replace('/{{(.*?)}}/',$this->tablePrefix.'\1',$sql);
	}

	/**
	 * 启动事务
	 * @access public
	 * @return void
	 */
	public function beginTransaction() {
		$this->_db->connect();
		if ($this->transTimes == 0) {
			$this->_db->beginTransaction();
		}
		$this->transTimes++;
		return ;
	}

	/**
	 * 用于非自动提交状态下面的查询提交
	 * @access public
	 * @return boolen
	 */
	public function commit() {
		if ($this->transTimes > 0) {
			$result = $this->_db->commit();
			$this->transTimes = 0;
			if(!$result){
				Debug::addmsg($this->error());
			}
		}
		return true;
	}

	/**
	 * 事务回滚
	 * @access public
	 * @return boolen
	 */
	public function rollback() {
		if ($this->transTimes > 0) {
			$result = $this->_db->rollback();
			$this->transTimes = 0;
			if(!$result){
				Debug::addmsg($this->error());
			}
		}
		return true;
	}

	/**
	 * 获取执行错误
	 * @return mixed
	 */
	public function error(){
		return $this->_db->error();
	}

	/**
	 *关闭数据库连接
	 */
	public function __destruct(){
		$this->_db->close();
	}
}
?>
