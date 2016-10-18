<?php



function checkmail($string){
	if(filter_var($string,FILTER_VALIDATE_EMAIL) != false){
		if(!preg_match('/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i', $string)){
			return false;
		}else{
			return true;
		}
	}
	return false;
}

function GetRandChar($length){
	$str = null;
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($strPol)-1;
	for($i=0;$i<$length;$i++){
		$str.=$strPol[rand(0,$max)];
	}
	return $str;
}  

function postmail($to,$subject = '',$body = ''){
    require_once('class.phpmailer.php');
    include('class.smtp.php');
    $mail = new PHPMailer();
    $mail->CharSet ="utf-8";
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host       = 'YouHost!;
    $mail->Port       = 465;
    $mail->Username   = 'YouUserName!';
    $mail->Password   = 'YouPW!';
    $mail->From		  = 'account@binklac.com if you want';
	$mail->FromName   = 'BinKlac';
    $mail->Subject    = $subject;
    $mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!';
    $mail->MsgHTML($body);
    $mail->AddAddress($to, '');
    if(!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}

?>