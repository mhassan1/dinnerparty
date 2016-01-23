<?php

try {

include('../mysql_include.php');

$resp='';
$emails = array('m','a');

if($_POST['type']=='requestauth'){

if(isset($_POST['email'])&&isset($_POST['name'])){

session_unset();
session_destroy();

setcookie('shabbatemail',$_POST['email'],0,'/shabbat/');$_COOKIE['shabbatemail']=$_POST['email'];
$resp.='email cookie created.';
}

$result = $db->query("select * from shabbat_users where user='".$_COOKIE['shabbatemail']."'");

if($result->num_rows==1) {

$row=$result->fetch_assoc();

if($row["active"]=='Y'){
################ AUTHENTICATED
session_start();
//setcookie('shabbatauth','auth',0,'/shabbat/');
//setcookie('shabbatid',$row["user_id"],0,'/shabbat/');
$_SESSION['shabbatid'] = $row["user_id"];
$_SESSION['shabbatname'] = $row["user_name"];
$_SESSION['shabbatemail'] = $row["user_email"];
$resp.=' session started. auth cookie created.';
}

} else {

$stmt = $db->query("INSERT INTO shabbat_users (user,user_name,active) values ('".$_POST['email']."','".$_POST['name']."','Y')");
$db->commit();

}

} elseif(isset($_GET['logoff'])){

setcookie('shabbatemail','', time()-3600,'/shabbat/');
setcookie('shabbatauth','', time()-3600,'/shabbat/');
session_unset();
session_destroy();
$resp.=' logged off';


} elseif($_POST['type']=='rsvp'){

$stmt = $db->query("INSERT INTO shabbat_rsvps (event_id,user_id,resp,addl_guests) values (".$_POST['event_id'].",".$_COOKIE['shabbatid'].",'".$_POST['resp']."',".$_POST['addl_guests'].")
on duplicate key update resp='".$_POST['resp']."',addl_guests=".$_POST['addl_guests']);
$db->commit();

} else {$resp='Error';}


//echo $resp;

} catch (Exception $e) {echo 'Caught exception: ',  $e->getMessage(), "\n";}

header('Location: index.php');

?>

<!--<a href="index.php">go back</a>-->