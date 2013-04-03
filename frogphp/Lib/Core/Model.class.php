<?php
/**
 * Frogphp
 * Model.class.php   模型基类
 *
 *
 * @author silenceper <silenceper@gmail.com>
 * @link http://www.ttzxnet.com/frogphp/
 * @copyright Copyright &copy; 2012-2013 Frogphp
 */
class Model{
	// 操作状态
	const MODEL_INSERT      =   1;      //  插入模型数据
	const MODEL_UPDATE    =   2;      //  更新模型数据
	const MODEL_BOTH      =   3;      //  包含上面两种方式
	const MUST_VALIDATE         =   1;// 必须验证
	const EXISTS_VAILIDATE      =   0;// 表单存在字段则验证
	const VALUE_VAILIDATE       =   2;// 表单值不为空则验证
	// 当前数据库操作对象
	protected $db = null;
	// 主键名称
	protected $pk  = 'id';
	// 数据表前缀
	protected $tablePrefix  =   '';
	// 模型名称
	protected $name = '';
	// 数据库名称
	protected $dbName  = '';
	// 数据表名（不包含表前缀）
	protected $tableName = '';
	// 实际数据表名（包含表前缀）
	protected $trueTableName ='';
	// 字段信息
	protected $fields = array();
	// 数据信息
	protected $data =   array();
	// 查询表达式参数
	protected $options  =   array();
	protected $_validate       = array();  // 自动验证定义
	protected $_auto           = array();  // 自动完成定义
	protected $_map           = array();  // 字段映射定义
	// 是否自动检测数据表字段信息
	protected $autoCheckFields   =   true;

	
	public function __construct($name=''){
		if(!empty($name)){
			$this->name=$name;
		}elseif(empty($this->name)){
			$this->name =   $this->getModelName();
		}
		
		//获取表前缀
		$this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
		
		// 数据库初始化操作
		// 获取数据库操作对象
		$this->db();
	}
	
	
	function db(){
		//可扩展
		$this->db = Db::getInstance();
		//dump($this->db);
		// 字段检测
        if(!empty($this->name) && $this->autoCheckFields)    $this->_checkTableInfo();	
	}
	
	/**
	 +----------------------------------------------------------
	 * 自动检测数据表信息
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	protected function _checkTableInfo() {
		if(empty($this->fields)){
			// 每次都会读取数据表信息
			$this->flush();
		}
	}
	
	/**
	 +----------------------------------------------------------
	 * 获取字段信息并缓存
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	function flush(){
		// 缓存不存在则查询数据表信息
		$this->db->setModel($this->name);
		$fields =   $this->db->getFields($this->getTableName());
		if(!$fields) { // 无法获取字段信息
			return false;
		}
		$this->fields   =   array_keys($fields);
		$this->fields['_autoinc'] = false;
		foreach ($fields as $key=>$val){
			// 记录字段类型
			$type[$key]    =   $val['type'];
			if($val['primary']) {
				$this->fields['_pk'] = $key;
				if($val['autoinc']) $this->fields['_autoinc']   =   true;
			}
		}
	}
	
	//得到完整的数据表名
	function getTableName(){
		if(empty($this->trueTableName)){
			$this->tableName=strtolower($this->name);
			$this->trueTableName=$this->tablePrefix.$this->tableName;
		}
		
		return $this->trueTableName;
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 得到当前的数据对象名称
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function getModelName() {
		if(empty($this->name))
			$this->name =   substr(get_class($this),0,-5);
		return $this->name;
	}
	
	/**
	 +----------------------------------------------------------
	 * SQL查询
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $sql  SQL指令
	 * @param boolean $parse  是否需要解析SQL
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function query($sql,$parse=false) {
		$sql  =   $this->parseSql($sql,$parse);
		return $this->db->query($sql);
	}
	/**
	 +----------------------------------------------------------
	 * 执行SQL语句
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $sql  SQL指令
	 * @param boolean $parse  是否需要解析SQL
	 +----------------------------------------------------------
	 * @return false | integer
	 +----------------------------------------------------------
	 */
	public function execute($sql,$parse=false) {
		$sql  =   $this->parseSql($sql,$parse);
		return $this->db->execute($sql);
	}
	/**
	 +----------------------------------------------------------
	 * 解析SQL语句
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $sql  SQL指令
	 * @param boolean $parse  是否需要解析SQL
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	protected function parseSql($sql,$parse) {
		// 分析表达式
		if($parse) {
			$options =  $this->_parseOptions();
			$sql  =   $this->db->parseSql($sql,$options);
		}else{
			if(strpos($sql,'__TABLE__'))
				$sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
		}
		$this->db->setModel($this->name);
		return $sql;
	}
	/**
	 +----------------------------------------------------------
	 * 设置数据对象的值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $name 名称
	 * @param mixed $value 值
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function __set($name,$value) {
		// 设置数据对象属性
		$this->data[$name]  =   $value;
	}
	
	/**
	 +----------------------------------------------------------
	 * 获取数据对象的值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $name 名称
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function __get($name) {
		return isset($this->data[$name])?$this->data[$name]:null;
	}
	
	/**
	 +----------------------------------------------------------
	 * 检测数据对象的值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $name 名称
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function __isset($name) {
		return isset($this->data[$name]);
	}
	
	/**
	 +----------------------------------------------------------
	 * 销毁数据对象的值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $name 名称
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	public function __unset($name) {
		unset($this->data[$name]);
	}
	
	/**
	 +----------------------------------------------------------
	 * 利用__call方法实现一些特殊的Model方法
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $method 方法名称
	 * @param array $args 调用参数
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function __call($method,$args) {
		if(in_array(strtolower($method),array('table','where','order','limit','page','alias','having','group','lock','distinct'),true)) {
			// 连贯操作的实现
			$this->options[strtolower($method)] =   $args[0];
			return $this;
		}elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
			// 统计查询的实现
			$field =  isset($args[0])?$args[0]:'*';
			return $this->getField(strtoupper($method).'('.$field.') AS fg_'.$method);
		}elseif(strtolower(substr($method,0,5))=='getby') {
			// 根据某个字段获取记录
			$field   =   strtolower(substr($method,5));
			$where[$field] =  $args[0];
			return $this->where($where)->find();
		}elseif(strtolower(substr($method,0,10))=='getfieldby') {
			// 根据某个字段获取记录的某个值
			$name   =   strtolower(substr($method,10));
			$where[$name] =$args[0];
			return $this->where($where)->getField($args[1]);
		}else{
			Debug::addmsg("模型中没有这个{$method}方法");
			return;
		}
	}
	
	/**
	 +----------------------------------------------------------
	 * 新增数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $data 数据
	 * @param array $options 表达式
	 * @param boolean $replace 是否replace
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function add($data='',$options=array(),$replace=false) {
		if(empty($data)) {
			// 没有传递数据，获取当前数据对象的值
			if(!empty($this->data)) {
				$data    =   $this->data;
				// 重置数据
				$this->data = array();
			}else{
				Debug::addmsg('没有数据');
				return false;
			}
		}
		// 分析表达式
		$options =  $this->_parseOptions($options);
		// 数据处理
		$data = $this->_facade($data);
		
		// 写入数据到数据库
		$result = $this->db->insert($data,$options,$replace);
		if(false !== $result ) {
			$insertId   =   $this->getLastInsID();
			if($insertId) {
				// 自增主键返回插入ID
				$data[$this->getPk()]  = $insertId;
				return $insertId;
			}
		}
		return $result;
	}
	
	/**
	 +----------------------------------------------------------
	 * 保存数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $data 数据
	 * @param array $options 表达式
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function save($data='',$options=array()) {
		if(empty($data)) {
			// 没有传递数据，获取当前数据对象的值
			if(!empty($this->data)) {
				$data    =   $this->data;
				// 重置数据
				$this->data = array();
			}else{
				Debug::addmsg('保存数据失败');
				return false;
			}
		}
		// 数据处理
		$data = $this->_facade($data);
		// 分析表达式
		$options =  $this->_parseOptions($options);
		if(false === $this->_before_update($data,$options)) {
			return false;
		}
		if(!isset($options['where']) ) {
			// 如果存在主键数据 则自动作为更新条件
			if(isset($data[$this->getPk()])) {
				$pk   =  $this->getPk();
				$where[$pk]   =  $data[$pk];
				$options['where']  =  $where;
				$pkValue = $data[$pk];
				unset($data[$pk]);
			}else{
				// 如果没有任何更新条件则不执行
				Debug::addmsg('不存在数据跟心');
				return false;
			}
		}
		$result = $this->db->update($data,$options);
		if(false !== $result) {
			if(isset($pkValue)) $data[$pk]   =  $pkValue;
			$this->_after_update($data,$options);
		}
		return $result;
	}
	// 更新数据前的回调方法
	protected function _before_update(&$data,$options) {}
	// 更新成功后的回调方法
	protected function _after_update($data,$options) {}
	
	/**
	 +----------------------------------------------------------
	 * 删除数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $options 表达式
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function delete($options=array()) {
		if(empty($options) && empty($this->options['where'])) {
			// 如果删除条件为空 则删除当前数据对象所对应的记录
			if(!empty($this->data) && isset($this->data[$this->getPk()]))
				return $this->delete($this->data[$this->getPk()]);
			else
				return false;
		}
		if(is_numeric($options)  || is_string($options)) {
			// 根据主键删除记录
			$pk   =  $this->getPk();
			if(strpos($options,',')) {
				$where[$pk]   =  array('IN', $options);
			}else{
				$where[$pk]   =  $options;
				$pkValue = $options;
			}
			$options =  array();
			$options['where'] =  $where;
		}
		// 分析表达式
		$options =  $this->_parseOptions($options);
		$result=    $this->db->delete($options);
		if(false !== $result) {
			$data = array();
			if(isset($pkValue)) $data[$pk]   =  $pkValue;
			$this->_after_delete($data,$options);
		}
		// 返回删除记录个数
		return $result;
	}
	// 删除成功后的回调方法
	protected function _after_delete($data,$options) {}
	
	/**
	 +----------------------------------------------------------
	 * 查询数据集
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 表达式参数
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function select($options=array()) {
		if(is_string($options) || is_numeric($options)) {
			// 根据主键查询
			$pk   =  $this->getPk();
			if(strpos($options,',')) {
				$where[$pk] =  array('IN',$options);
			}else{
				$where[$pk]   =  $options;
			}
			$options =  array();
			$options['where'] =  $where;
		}elseif(false === $options){ // 用于子查询 不查询只返回SQL
			$options =  array();
			// 分析表达式
			$options =  $this->_parseOptions($options);
			return  '( '.$this->db->buildSelectSql($options).' )';
		}
		// 分析表达式
		$options =  $this->_parseOptions($options);
		$resultSet = $this->db->select($options);
		if(false === $resultSet) {
			return false;
		}
		if(empty($resultSet)) { // 查询结果为空
			return null;
		}
		$this->_after_select($resultSet,$options);
		return $resultSet;
	}
	// 查询成功后的回调方法
	protected function _after_select(&$resultSet,$options) {}
	
	/**
	 +----------------------------------------------------------
	 * 创建数据对象 但不保存到数据库
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $data 创建数据
	 * @param string $type 状态
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function create($data='',$type='') {
		// 如果没有传值默认取POST数据
		if(empty($data)) {
			$data    =   $_POST;
		}elseif(is_object($data)){
			$data   =   get_object_vars($data);
		}
		// 验证数据
		if(empty($data) || !is_array($data)) {
			//空
			$this->error = Debug::addmsg('空数据');
			return false;
		}
	
		// 检查字段映射
		$data = $this->parseFieldsMap($data,0);
	
		// 状态
		$type = $type?$type:(!empty($data[$this->getPk()])?self::MODEL_UPDATE:self::MODEL_INSERT);
	
		
		// 验证完成生成数据对象
		if($this->autoCheckFields) { // 开启字段检测 则过滤非法字段数据
			$vo   =  array();
			foreach ($this->fields as $key=>$name){
				if(substr($key,0,1)=='_') continue;
				$val = isset($data[$name])?$data[$name]:null;
				//保证赋值有效
				if(!is_null($val)){
					$vo[$name] = (MAGIC_QUOTES_GPC && is_string($val))?   stripslashes($val)  :  $val;
				}
			}
		}else{
			$vo   =  $data;
		}
	
		// 创建完成对数据进行自动处理
		$this->autoOperation($vo,$type);
		// 赋值当前数据对象
		$this->data =   $vo;
		// 返回创建的数据以供其他调用
		return $vo;
	}
	
	/**
	 +----------------------------------------------------------
	 * 自动表单处理
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $data 创建数据
	 * @param string $type 创建类型
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	private function autoOperation(&$data,$type) {
		// 自动填充
		if(!empty($this->_auto)) {
			foreach ($this->_auto as $auto){
				// 填充因子定义格式
				// array('field','填充内容','填充条件','附加规则',[额外参数])
				if(empty($auto[2])) $auto[2] = self::MODEL_INSERT; // 默认为新增的时候自动填充
				if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
					switch($auto[3]) {
						case 'function':    //  使用函数进行填充 字段的值作为参数
						case 'callback': // 使用回调方法
							$args = isset($auto[4])?(array)$auto[4]:array();
							if(isset($data[$auto[0]])) {
								array_unshift($args,$data[$auto[0]]);
							}
							if('function'==$auto[3]) {
								$data[$auto[0]]  = call_user_func_array($auto[1], $args);
							}else{
								$data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
							}
							break;
						case 'field':    // 用其它字段的值进行填充
							$data[$auto[0]] = $data[$auto[1]];
							break;
						case 'string':
						default: // 默认作为字符串填充
							$data[$auto[0]] = $auto[1];
					}
					if(false === $data[$auto[0]] )   unset($data[$auto[0]]);
				}
			}
		}
		return $data;
	}
	
	/**
	 +----------------------------------------------------------
	 * 查询数据
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $options 表达式参数
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function find($options=array()) {
		if(is_numeric($options) || is_string($options)) {
			$where[$this->getPk()] =$options;
			$options = array();
			$options['where'] = $where;
		}
		// 总是查找一条记录
		$options['limit'] = 1;
		// 分析表达式
		$options =  $this->_parseOptions($options);
		$resultSet = $this->db->select($options);
		if(false === $resultSet) {
			return false;
		}
		if(empty($resultSet)) {// 查询结果为空
			return null;
		}
		$this->data = $resultSet[0];
		$this->_after_find($this->data,$options);
		return $this->data;
	}
	// 查询成功的回调方法
	protected function _after_find(&$result,$options) {}
	/**
	 +----------------------------------------------------------
	 * 生成查询SQL 可用于子查询
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $options 表达式参数
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function buildSql($options=array()) {
		// 分析表达式
		$options =  $this->_parseOptions($options);
		return  '( '.$this->db->buildSelectSql($options).' )';
	}
	/**
	 +----------------------------------------------------------
	 * 处理字段映射
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param array $data 当前数据
	 * @param integer $type 类型 0 写入 1 读取
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	 */
	public function parseFieldsMap($data,$type=1) {
		// 检查字段映射
		if(!empty($this->_map)) {
			foreach ($this->_map as $key=>$val){
				if($type==1) { // 读取
					if(isset($data[$val])) {
						$data[$key] =   $data[$val];
						unset($data[$val]);
					}
				}else{
					if(isset($data[$key])) {
						$data[$val] =   $data[$key];
						unset($data[$key]);
					}
				}
			}
		}
		return $data;
	}
	/**
	 +----------------------------------------------------------
	 * 设置记录的某个字段值
	 * 支持使用数据库字段和方法
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string|array $field  字段名
	 * @param string $value  字段值
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function setField($field,$value='') {
		if(is_array($field)) {
			$data = $field;
		}else{
			$data[$field]   =  $value;
		}
		return $this->save($data);
	}
	
	/**
	 +----------------------------------------------------------
	 * 字段值增长
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $field  字段名
	 * @param integer $step  增长值
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function setInc($field,$step=1) {
		return $this->setField($field,array('exp',$field.'+'.$step));
	}
	
	/**
	 +----------------------------------------------------------
	 * 字段值减少
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $field  字段名
	 * @param integer $step  减少值
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	public function setDec($field,$step=1) {
		return $this->setField($field,array('exp',$field.'-'.$step));
	}
	
	/**
	 +----------------------------------------------------------
	 * 获取一条记录的某个字段值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $field  字段名
	 * @param string $spea  字段数据间隔符号 NULL返回数组
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */
	public function getField($field,$sepa=null) {
		$options['field']    =  $field;
		$options =  $this->_parseOptions($options);
		if(strpos($field,',')) { // 多字段
			$resultSet = $this->db->select($options);
			if(!empty($resultSet)) {
				$_field = explode(',', $field);
				$field  = array_keys($resultSet[0]);
				$move   =  $_field[0]==$_field[1]?false:true;
				$key =  array_shift($field);
				$key2 = array_shift($field);
				$cols   =   array();
				$count  =   count($_field);
				foreach ($resultSet as $result){
					$name   =  $result[$key];
					if($move) { // 删除键值记录
						unset($result[$key]);
					}
					if(2==$count) {
						$cols[$name]   =  $result[$key2];
					}else{
						$cols[$name]   =  is_null($sepa)?$result:implode($sepa,$result);
					}
				}
				return $cols;
			}
		}else{   // 查找一条记录
			$options['limit'] = 1;
			$result = $this->db->select($options);
			if(!empty($result)) {
				return reset($result[0]);
			}
		}
		return null;
	}
	/**
	 +----------------------------------------------------------
	 * 对保存到数据库的数据进行处理
	 +----------------------------------------------------------
	 * @access protected
	 +----------------------------------------------------------
	 * @param mixed $data 要操作的数据
	 +----------------------------------------------------------
	 * @return boolean
	 +----------------------------------------------------------
	 */
	protected function _facade($data) {
		// 检查非数据字段
		if(!empty($this->fields)) {
			foreach ($data as $key=>$val){
				if(!in_array($key,$this->fields,true)){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}
	
	
	/**
	 +----------------------------------------------------------
	 * 分析表达式
	 +----------------------------------------------------------
	 * @access proteced
	 +----------------------------------------------------------
	 * @param array $options 表达式参数
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	 */
	protected function _parseOptions($options=array()) {
		if(is_array($options))
			$options =  array_merge($this->options,$options);
		// 查询过后清空sql表达式组装 避免影响下次查询
		$this->options  =   array();
		if(!isset($options['table']))
			// 自动获取表名
			$options['table'] =$this->getTableName();
		if(!empty($options['alias'])) {
			$options['table']   .= ' '.$options['alias'];
		}
		// 记录操作的模型名称
		$options['model'] =  $this->name;
		
		// 表达式过滤
		$this->_options_filter($options);
		return $options;
	}
	// 表达式过滤回调方法
	protected function _options_filter(&$options) {}
	
	/**
	 +----------------------------------------------------------
	 * 返回最后插入的ID
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function getLastInsID() {
		return $this->db->getLastInsID();
	}
	
	/**
	 +----------------------------------------------------------
	 * 获取主键名称
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	public function getPk() {
		return isset($this->fields['_pk'])?$this->fields['_pk']:$this->pk;
	}
	
	/**
	 +----------------------------------------------------------
	 * 设置数据对象值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $data 数据
	 +----------------------------------------------------------
	 * @return Model
	 +----------------------------------------------------------
	 */
	public function data($data){
		if(is_object($data)){
			$data   =   get_object_vars($data);
		}elseif(is_string($data)){
			parse_str($data,$data);
		}elseif(!is_array($data)){
			//空
			//throw_exception(L('_DATA_TYPE_INVALID_'));
		}
		$this->data = $data;
		return $this;
	}
	
	/**
	 +----------------------------------------------------------
	 * 查询SQL组装 join
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $join
	 +----------------------------------------------------------
	 * @return Model
	 +----------------------------------------------------------
	 */
	public function join($join) {
		if(is_array($join)) {
			$this->options['join'] =  $join;
		}elseif(!empty($join)) {
			$this->options['join'][]  =   $join;
		}
		return $this;
	}
	
	/**
	 +----------------------------------------------------------
	 * 查询SQL组装 union
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $union
	 * @param boolean $all
	 +----------------------------------------------------------
	 * @return Model
	 +----------------------------------------------------------
	 */
	public function union($union,$all=false) {
		if(empty($union)) return $this;
		if($all) {
			$this->options['union']['_all']  =   true;
		}
		if(is_object($union)) {
			$union   =  get_object_vars($union);
		}
		// 转换union表达式
		if(is_string($union) ) {
			$options =  $union;
		}elseif(is_array($union)){
			if(isset($union[0])) {
				$this->options['union']  =  array_merge($this->options['union'],$union);
				return $this;
			}else{
				$options =  $union;
			}
		}else{
			//空
			//throw_exception(L('_DATA_TYPE_INVALID_'));
		}
		$this->options['union'][]  =   $options;
		return $this;
	}
	
	/**
	 +----------------------------------------------------------
	 * 获取数据表字段信息
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	 */
	public function getDbFields(){
		if($this->fields) {
			$fields   =  $this->fields;
			unset($fields['_autoinc'],$fields['_pk'],$fields['_type']);
			return $fields;
		}
		return false;
	}
	
	/**
	 +----------------------------------------------------------
	 * 指定查询字段 支持字段排除
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param mixed $field
	 * @param boolean $except 是否排除
	 +----------------------------------------------------------
	 * @return Model
	 +----------------------------------------------------------
	 */
	public function field($field,$except=false){
		if(true === $field) {// 获取全部字段
			$fields   =  $this->getDbFields();
			$field =  $fields?$fields:'*';
		}elseif($except) {// 字段排除
			if(is_string($field)) {
				$field =  explode(',',$field);
			}
			$fields   =  $this->getDbFields();
			$field =  $fields?array_diff($fields,$field):$field;
		}
		$this->options['field']   =   $field;
		return $this;
	}
	
	/**
	 +----------------------------------------------------------
	 * 设置模型的属性值
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $name 名称
	 * @param mixed $value 值
	 +----------------------------------------------------------
	 * @return Model
	 +----------------------------------------------------------
	 */
	public function setProperty($name,$value) {
		if(property_exists($this,$name))
			$this->$name = $value;
		return $this;
	}
	
}