<?php
/**
 * File: Dbinter.class.php
 * User: silenceper
 * Site: https://github.com/frogphp/frogphp
 * Date: 13-9-8
 * Time: 下午7:54
 * Description: 数据库驱动接口
 */
interface  Dbinter {
	public static function connect();
	public function query($sql,$params=array());
	public function execute($sql,$params=array());
	public function error();
	public function getLastInsertId();
	public function beginTransaction();
	public function commit();
	public function rollback();
	public function close();
}
