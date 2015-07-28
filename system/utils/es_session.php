<?php 
class es_session
{
	static $sess_id = "";
	static function id()
	{
		if(self::$sess_id)
			return self::$sess_id;
		else
			return session_id();
	}
	static function set_sessid($sess_id)
	{
		self::$sess_id = $sess_id;
	}
	static function start()
	{
		if($_GET['FANWE_SESSION_ID']){
			self::$sess_id = $_GET['FANWE_SESSION_ID'];
		}
		es_session_start(self::$sess_id);
	}

	// 判断session是否存在
	static function is_set($name) {
		self::start();
		$tag = isset($_SESSION[app_conf("AUTH_KEY").$name]);
		self::close();
		return $tag;
	}

	// 获取某个session值
	static function get($name) {
		self::start();
		$value   = $_SESSION[app_conf("AUTH_KEY").$name];
		self::close();
		return $value;
	}

	// 设置某个session值
	static function set($name,$value) {
		self::start();
		$_SESSION[app_conf("AUTH_KEY").$name]  =   $value;
		self::close();
	}

	// 删除某个session值
	static function delete($name) {
		self::start();
		unset($_SESSION[app_conf("AUTH_KEY").$name]);
		self::close();
	}

	// 清空session
	static function clear() {
		@session_destroy();
	}

	//关闭session的读写
	static function close()
	{
		@session_write_close();
	}
	
	static function  is_expired()
    {
    	self::start();
        if (isset($_SESSION[app_conf("AUTH_KEY")."expire"]) && $_SESSION[app_conf("AUTH_KEY")."expire"] < TIME_UTC) {
            $tag =  true;
        } else {        	
        	$_SESSION[app_conf("AUTH_KEY")."expire"] = TIME_UTC+(intval(app_conf("EXPIRED_TIME"))*60);
            $tag = false;
        }
        return $tag;
        self::close();
    }

}
//end session
?>
