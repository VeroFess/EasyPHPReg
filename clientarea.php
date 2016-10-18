<?php
session_start();

if(!isset($_SESSION['LastView'])){
	$_SESSION['LastView'] = time();
}
//if($_SESSION['LastView'] + 3 > time()){
//	die("Access Deny");
//}
if($_SESSION['LastView'] + 5 * 60 < time()){
	if(isset($_SESSION["User"])){
		unset($_SESSION["User"]);
	}
}
$_SESSION['LastView'] = time();

include_once("class/class.sql.user.php");
$usermanager = new UserManager();

if(isset($_GET['method'])){
	if($_GET['method'] == 'active'){
		return $usermanager->Active($_GET["token"]);
	}
	if($_GET['method'] == 'reset'){
		file_get_contents("reset.html.php")
		exit();
	}
}

if(isset($_POST['login'])){
	if($_SESSION['VerifyCode'] != strtolower($_POST['vericode'])){
		die("err_vericode_not_same");
	}
	$ret = $usermanager->Login($_POST['username'],$_POST['password']);
	if($ret == "login_success"){
		$_SESSION["User"] = $_POST['username'];
	}
	die($ret);
}else if(isset($_POST['reset'])){
	if($_SESSION['VerifyCode'] != strtolower($_POST['vericode'])){
		die("err_vericode_not_same");
	}
	die($usermanager->Reset_First($_POST['email']));
}else if(isset($_POST['register'])){
	if($_SESSION['VerifyCode'] != strtolower($_POST['vericode'])){
		die("err_vericode_not_same");
	}
	if(!isset($_POST['tnc'])){
		die("err_not_allow_tnc");
	}
	die($usermanager->Register($_POST['username'],$_POST['password'],$_POST['rpassword']));
}else if(isset($_POST['reset'])){
	if($_SESSION['VerifyCode'] != strtolower($_POST['vericode'])){
		die("err_vericode_not_same");
	}
	die($usermanager->Reset_Second($_GET["token"],$_POST["password"],$_POST["rpassword"]));
}

if(!isset($_SESSION["UserID"])){
	echo file_get_contents("login.html.php");
}else{
	echo "登录成功了";
}


?>