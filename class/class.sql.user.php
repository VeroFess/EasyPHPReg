<?php

include_once("class.sql.base.php");
include_once("class.web.helper.php");


class UserManager{
	private $sql;
	
	public function __construct() {
		$this->sql = new SqlBase("localhost","login","root","root");
	}
	
	public function Register($email,$password,$rpassword) {
		if($password != $rpassword){
			return "err_different_password";
		}
		if(checkmail($email) == false){
			return "err_invalid_email";
		}
		if($this->sql->SafeSelect("Users",array(array('email',$email))) != false){
			if($this->sql->SafeSelect("Users",array(array('email',$email)),"exp_time")[0]["exp_time"] < time()){
				$this->sql->SafeDelete("Users",array(array('token',$token)));
			}else{
				return "err_already_use";
			}
		}
		$token = md5($email.$password.time().GetRandChar(10));
		if($this->sql->SafeInsert("Users",array(
												array("email",strtolower($email)),
												array("password",strtolower(md5($password))),
												array("token",$token),
												array("exp_time",time()+60*60*12),
												array("reg_time",time()),
												array("active",0)
												)) == false){
			return "err_sql_insert";
		}
		$emailbody = "感谢您在BinKlac注册了新帐号。<br/>请点击链接激活您的帐号。<br/><a href='https://www.binklac.com/clientarea.php?method=active&token=" . $token . "' target='_blank'>https://www.binklac.com/clientarea.php?method=active&token=" . $token . "</a><br/>如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接12小时内有效。<br/>如果此次激活请求非你本人所发，请忽略本邮件。<br/><p style='text-align:right'>BinKlac</p>";
		if(postmail($email,"用户帐号激活",$emailbody) == false){
			$this->sql->SafeDelete("Users",array(array('email',$email)));
			return "err_post_mail";
		}
		return "reg_success";
	}
	
	public function Active($token){
		if($this->sql->SafeSelect("Users",array(array('token',$token)),"exp_time")[0]["exp_time"] < time()){
			$this->sql->SafeDelete("Users",array(array('token',$token)));
			return false;
		}
		if($this->sql->SafeUpdate("Users",array(array("active",true)),array(array('token',$token)))){
			if($this->sql->SafeUpdate("Users",array(array("token",""),array("exp_time",0)),array(array('token',$token)))){
				return true;
			}else{
				return $this->sql->SafeUpdate("Users",array(array("active",false)),array(array('token',$token)));
			}
		}
		return false;
	}
	
	public function Reset_First($email){
		if(checkmail($email) == false){
			return "err_invalid_email";
		}
		if($this->sql->SafeSelect("Users",array(array('email',$email))) == false){
			return "err_no_such_email";
		}
		if($this->sql->SafeSelect("Users",array(array('email',$email)),"active")[0]["active"] == false){
			return "err_not_active";
		}
		$token = md5($email.time().GetRandChar(10));
		if($this->sql->SafeUpdate("Users",array(array("token",$token),array("exp_time",time()+60*60*12)),array(array('email',$email))) == false){
			return "err_sql_update";
		}
		$emailbody = "您尝试重置您在BinKlac的密码。<br/>请点击链接重置您的帐号。<br/><a href='https://www.binklac.com/clientarea.php?method=reset&token=" . $token . "' target='_blank'>https://www.binklac.com/clientarea.php?method=reset&token=" . $token . "</a><br/>如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接12小时内有效。<br/>如果此次激活请求非你本人所发，请忽略本邮件。<br/><p style='text-align:right'>BinKlac</p>";
		if(postmail($email,"找回密码",$emailbody) == false){
			$this->sql->SafeUpdate("Users",array(array("token",""),array("exp_time",0)),array(array('email',$email)));
			return "err_post_mail";
		}
		return "reset_success";
	}
	
	public function Reset_Second($token,$password,$rpassword){
		if($this->sql->SafeSelect("Users",array(array('token',$token))) == false){
			return "err_no_such_token";
		}
		if($password != $rpassword){
			return "err_passwd_not_same";
		}
		if($this->sql->SafeSelect("Users",array(array('token',$token)),"exp_time")[0]["exp_time"] < time()){
			$this->sql->SafeDelete("Users",array(array('token',$token)));
			return "err_timeout";
		}
		if($this->sql->SafeUpdate("Users",array(array("password",strtolower(md5($password))),array("token",""),array("exp_time",0)),array(array('token',$token)))){
			return "reset_success";
		}else{
			return "err_sql_update";
		}
	}
	
	public function Login($email,$password){
		if($this->sql->SafeSelect("Users",array(array('email',$email),array("password",strtolower(md5($password))))) == false){
			return "login_error";
		}else{
			if($this->sql->SafeSelect("Users",array(array('email',$email)),"active")[0]["active"] == false){
				return "err_not_active";
			}
			return "login_success";
		}
	}
	
	function __destruct(){
        $this->sql = null;
    }
}
?>