<?php $friendlyTitle=''; include('../mysql_include.php'); session_start(); ?>

<?php function selected($thisPage,$thatPage){if($thisPage==$thatPage){echo 'selected';}} ?>

<?php include('header.php'); ?>

<?php
if(isset($_POST['name'])){$_POST['name']=htmlentities(mysqli_real_escape_string($db,$_POST['name']));}
if(isset($_POST['friend'])){$_POST['friend']=htmlentities(mysqli_real_escape_string($db,$_POST['friend']));}
if(isset($_POST['note'])){$_POST['note']=htmlentities(mysqli_real_escape_string($db,$_POST['note']));}
if(isset($_POST['post_content'])){$_POST['post_content']=htmlentities(mysqli_real_escape_string($db,$_POST['post_content']));}

if(isset($_GET['logoff'])){
setcookie('shabbatemail','', time()-3600,'/shabbat/');$_COOKIE['shabbatemail']='';
session_unset();setcookie('PHPSESSID','', time()-3600,'/shabbat/');$_COOKIE['PHPSESSID']='';
header('Location: index.php');exit;

}

if($_COOKIE['shabbatemail']!=''){

	// if session doesn't exist, attempt to create session and continue
	if(!isset($_SESSION['shabbatid'])){validateEmail($db,$_COOKIE['shabbatemail']);}
	
	// if session exists now...
	if(isset($_SESSION['shabbatid'])){

////////////////////////// AUTHENTICATED BEG //////////////////////////


if(isset($_GET['api'])){
////////////////////////// API BEG //////////////////////////

if($_GET['api']=='rsvp'){

$stmt = $db->query("INSERT INTO shabbat_rsvps (event_id,user_id,resp,addl_guests,note,rsvp_ts) values (".$_POST['event_id'].",".$_SESSION['shabbatid'].",'".$_POST['resp']."',".$_POST['addl_guests'].",'".$_POST['note']."',now())
on duplicate key update resp='".$_POST['resp']."',addl_guests=".$_POST['addl_guests'].",note='".$_POST['note']."',rsvp_ts=now()");
$db->commit();
header('Location: index.php?e='.$_POST['event_id']);exit;

} elseif($_GET['api']=='post'){
if($_POST['post_content']!=''){
$stmt = $db->query("INSERT INTO shabbat_posts (event_id,user_id,post_content,post_ts) values (".$_POST['event_id'].",".$_SESSION['shabbatid'].",'".$_POST['post_content']."',now())");
$db->commit();
}
header('Location: index.php?e='.$_POST['event_id']);exit;


} else {echo 'Invalid API call: '.$_SERVER['REQUEST_URI'];exit;}

////////////////////////// API END //////////////////////////
}

if(!isset($_GET['admin'])){
////////////////////////// HOME BEG //////////////////////////
?>

<div id="bleed">
<div id="title-block">
<div id="title">
<img style="height:40px;" src="img/star_logo.png"/></br>
Fink-a-licious Shabbat
</div>
Welcome, <?php echo $_SESSION['shabbatname'];?>!</br>
Scroll down to join upcoming events!
</div>
</div>

<div id="bg">
<div id="content">

<h1>Welcome!</h1>
<p>This is what it is!</p>
</br>

<h1>Upcoming Events</h1>

<?php
$result=$db->query("select a.*,b.resp,b.addl_guests,b.note from shabbat_events a
inner join shabbat_users c
left outer join shabbat_rsvps b on a.event_id=b.event_id and c.user_id=b.user_id
where c.user_id=".$_SESSION['shabbatid']." and a.active='Y'");
while ($row=$result->fetch_assoc()) {
?>

<div id="e<?=$row["event_id"]?>" class="d">
<table class="headah"><tr><td style="width:50%;">

<span><?=$row["event_name"]?></br>

<?php
$t = date('F j, Y g:ia',strtotime($row["event_start"]));
if(date('Ymd',strtotime($row["event_start"]))==date('Ymd',strtotime($row["event_end"]))){$t.=' - '.date('g:ia',strtotime($row["event_end"]));}else{$t.=' - '.date('F j, Y g:ia',strtotime($row["event_end"]));}
?>

<?=$t?></br>

<?php
$u = 'https://www.google.com/calendar/render?action=TEMPLATE&text=Fink-a-licious+Shabbat - '.urlencode(strip_tags($row["event_name"])).
'&dates='.date('Ymd',strtotime($row["event_start"])).'T'.date('Hi',strtotime($row["event_start"])).'00/'.
date('Ymd',strtotime($row["event_end"])).'T'.date('Hi',strtotime($row["event_end"])).'00'.
'&ctz=America/New_York&details='.urlencode(strip_tags($row["event_description"])).'&location='.urlencode($row["event_where"]).'&sf=true&output=xml';
?>

<a href="<?=$u?>" target="_blank">Add to Calendar</a>

<!--
<a style="margin:10px 0 15px 15px;float:left;" href="javascript:void(0);" onclick="$('#share<?=$row["event_id"]?>').css('visibility', 'visible');$('#share<?=$row["event_id"]?>').show(200);$('#share<?=$row["event_id"]?>').select();this.style.float='left';"><img style="width:12px;" src="img/link.png"/></a>
<input style="width:350px;margin:5px 0 5px 10px;display:none;" type="text" id="share<?=$row["event_id"]?>" value="http://<?=$_SERVER['HTTP_HOST'].'/shabbat/index.php?e='.$row["event_id"]?>"/>
-->

</td>
<td style="border-left:1px solid white;width:50%;">

<span><?=$row["event_where"]?></span>

</td></tr></table>

<p><?=repLinks($row["event_description"])?></p>

<?php if($row["resp"]!=''){ ?>
<span style="color:green;">
<?php
if($row["resp"]=='Yes'){
if($row["addl_guests"]==1){$addl=' +1 guest';}
elseif($row["addl_guests"]==2){$addl=' +2 guests';}
?>
Sweet! You're coming!<?=$addl?></br>
<?php } elseif($row["resp"]=='No'){ ?>
Womp womp! You are not coming to this!
<?php } elseif($row["resp"]=='Maybe'){ ?>
You replied Maybe!
<?php } ?>
</span>
<a href="javascript:void(0);" onclick="$('#rsvp<?=$row["event_id"]?>').show(200);">Edit your response</a>
<?php } ?>

<form id="rsvp<?=$row["event_id"]?>" action="index.php?api=rsvp" method="post" <?php if($row["resp"]!=''){ echo 'style="display:none;"';}?>>
<h2>Can you make it?</h2>
<input name="event_id" type="hidden" value="<?=$row["event_id"]?>" />

<table class="minitable">
<tr><td><input value="Yes" type="radio" id="rY<?=$row["event_id"]?>" name="resp" style="display:none;" <?php if($row["resp"]=='Yes'){echo 'checked';}?>/>
<label for="rY<?=$row["event_id"]?>">YES</label>
</td><td><input value="Maybe" type="radio" id="rM<?=$row["event_id"]?>" name="resp" style="display:none;" <?php if($row["resp"]=='Maybe'){echo 'checked';}?>/>
<label for="rM<?=$row["event_id"]?>">MAYBE</label>
</td><td><input value="No" type="radio" id="rN<?=$row["event_id"]?>" name="resp" style="display:none;" <?php if($row["resp"]=='No'){echo 'checked';}?>/>
<label for="rN<?=$row["event_id"]?>">NO</label>
</td></tr>
</table>

Will you be bringing additional guests?
<table class="minitable">
<tr><td><input value="0" type="radio" id="g0<?=$row["event_id"]?>" name="addl_guests" style="display:none;" <?php if($row["addl_guests"]=='0'){echo 'checked';}?>/>
<label for="g0<?=$row["event_id"]?>">0</label>
</td><td><input value="1" type="radio" id="g1<?=$row["event_id"]?>" name="addl_guests" style="display:none;" <?php if($row["addl_guests"]=='1'){echo 'checked';}?>/>
<label for="g1<?=$row["event_id"]?>">+1</label>
</td><td><input value="2" type="radio" id="g2<?=$row["event_id"]?>" name="addl_guests" style="display:none;" <?php if($row["addl_guests"]=='2'){echo 'checked';}?>/>
<label for="g2<?=$row["event_id"]?>">+2</label>
</td></tr>
</table>

<textarea name="note" class="note" placeholder="Leave a note" value="<?=$row['note']?>"></textarea>
</br>
<div class="submit" onclick="$('#rsvp<?=$row["event_id"]?>').submit();">RSVP</div>
</form>

<!--&nbsp;-&nbsp;<strong><a href="javascript:void();" onclick="showWhosComing">Who's coming?</a></strong>-->

<?php if($row["resp"]!=''){ ?>

<div id="posts<?=$row["event_id"]?>">
</br>
<form id="postform<?=$row["event_id"]?>" action="index.php?api=post" method="post" onsubmit="return this.elements.namedItem('post_content').value!='';">
<input name="event_id" type="hidden" value="<?=$row["event_id"]?>" />
<textarea class="note" name="post_content" placeholder="Post something"></textarea>
<div class="submit" onclick="$('#postform<?=$row["event_id"]?>').submit();">POST</div>
</form>

<?php
$result2=$db->query("select a.post_content,a.post_ts,b.user_name from shabbat_posts a, shabbat_users b 
where a.user_id=b.user_id and a.event_id=".$row["event_id"]." order by a.post_ts desc");
echo '<table class="posts">';
$i=0;
$ilast = $result2->num_rows;
while ($row2=$result2->fetch_assoc()) {
$i++;
?>

<tr class="<?php if($i>5){echo 'hidrow';} if($i%2==0){echo ' alt';} if($i==$ilast){echo ' last';}?>">
<td class="postts"><?=$row2["user_name"]?></br>
<?=date('m/d/Y',strtotime($row2["post_ts"]))?></br>
<?=date('H:i:s',strtotime($row2["post_ts"]))?></td>
<td style="width:100%;"><?=repLinks($row2["post_content"])?></td>
</tr>

<?php }
echo '</table>';
?>

<?php if($i>5){ ?>
<a style="font-size:12px;" href="javascript:void(0);" onclick="$('#posts<?=$row["event_id"]?> .hidrow').show(500)/*removeClass('hidrow')*/;this.style.display='none';">Show More Posts</a>
<?php } ?>
</br>
</div>

</div>

<?php } ?>

<?php if(isset($_GET['e'])){ ?>
<script type="text/javascript">
$(window).load(function (){
$('html, body').animate({scrollTop: $("#e<?=$_GET['e']?>").offset().top-5}, 400);
});
</script>
<?php } ?>



<?php } ?>

<span id="text" style="float:right;">
<?php echo $_SESSION['shabbatname'];
?></br>
<a href="index.php?logoff">Log off</a>
<?php if($_SESSION['shabbatadmin']=='Y') {echo '<a href="index.php?admin">Admin</a>';} ?>
</span>

<?php }
////////////////////////// HOME END //////////////////////////

if(isset($_GET['admin'])){
if($_SESSION['shabbatadmin']!='Y'){
echo '<h1>Admin</h1><p>I\'m sorry, it looks like you are not an admin!</p>';
} else {
////////////////////////// ADMIN BEG //////////////////////////


if(isset($_GET['adminapi'])){
////////////////////////// ADMIN API BEG //////////////////////////


/// USERS

if($_GET['adminapi']=='toggleUserAdmin'&&isset($_GET['id'])){

$stmt = $db->query("UPDATE shabbat_users set admin = case when admin='Y' then 'N' when admin='N' then 'Y' when admin is null then 'N' end
where user_id='".$_GET['id']."'");
$db->commit();
header('Location: index.php?admin');exit;

} elseif($_GET['adminapi']=='toggleUserActive'&&isset($_GET['id'])){

$stmt = $db->query("UPDATE shabbat_users set active = case when active='Y' then 'N' when active='N' then 'Y' when active is null then 'N' end
where user_id='".$_GET['id']."'");
$db->commit();
header('Location: index.php?admin');exit;

} elseif($_GET['adminapi']=='addEditUser'){

if($_POST['id']!=''){

$stmt = $db->query("UPDATE shabbat_users set user = '".$_POST['email']."', user_name = '".$_POST['name']."'
where user_id='".$_POST['id']."'");
$db->commit();
} else {insertEmail($db,$_POST['email'],$_POST['name']);}

header('Location: index.php?admin');exit;


/// EVENTS

} elseif($_GET['adminapi']=='toggleEventActive'&&isset($_GET['id'])){

$stmt = $db->query("UPDATE shabbat_events set active = case when active='Y' then 'N' when active='N' then 'Y' when active is null then 'N' end
where event_id='".$_GET['id']."'");
$db->commit();
header('Location: index.php?admin');exit;

} elseif($_GET['adminapi']=='addEditEvent'){

//if($_POST['id']!=''){

//$stmt = $db->query("UPDATE shabbat_events set event_name = '".addslashes($_POST['name'])."', event_where = '".addslashes($_POST['where'])."', event_where_full = '".addslashes($_POST['wherefull'])."'
//, event_description = '".addslashes($_POST['desc'])."', event_start = '".$_POST['start']."', event_end = '".$_POST['end']."'
//where event_id='".$_POST['id']."'");
//$db->commit();
//} else {
$stmt = $db->query("INSERT into shabbat_events (event_name,event_where,event_where_full,event_description,event_start,event_end)
values ('".addslashes($_POST['name'])."','".addslashes($_POST['where'])."','".addslashes($_POST['wherefull'])."','".addslashes($_POST['desc'])."','".$_POST['start']."','".$_POST['end']."')
on duplicate key update event_name = '".addslashes($_POST['name'])."', event_where = '".addslashes($_POST['where'])."', event_where_full = '".addslashes($_POST['wherefull'])."'
, event_description = '".addslashes($_POST['desc'])."', event_start = '".$_POST['start']."', event_end = '".$_POST['end']."'");
$db->commit();
//}

header('Location: index.php?admin');exit;

/// RSVPS

} elseif($_GET['adminapi']=='addEditRSVP'){

$stmt = $db->query("INSERT INTO shabbat_rsvps (event_id,user_id,resp,addl_guests) values (".$_POST['event_id'].",".$_POST['user_id'].",'".$_POST['resp']."','".$_POST['addl_guests']."')
on duplicate key update resp='".$_POST['resp']."',addl_guests='".$_POST['addl_guests']."'");
$db->commit();

header('Location: index.php?admin');exit;

/// EMAILS

} elseif($_GET['adminapi']=='sendEmail'){

//$date=date('Y-m-d H:i:s',$timestamp=time()-5*60*60);

$cc = $_POST['emailTo'];
$subject = $_POST['emailSub'];
$msg = $_POST['emailMsg'];

//echo $msg; exit;

$header="Cc: ".$cc."\r\n";
$header.="Reply-To: Asher Fink <fink.asher@gmail.com>\r\n";
$header.="Return-Path: Asher Fink <fink.asher@gmail.com>\r\n";
$header.="From: finkshabbat@speechwithjulie.com <finkshabbat@speechwithjulie.com>\r\n";
$header.="Organization: Fink-a-licious Shabbat\r\n";
$header.="MIME-Version: 1.0\r\n";
$header.="Content-type: text/html; charset=iso-8859-1\r\n";

if($cc!=''&&$subject!=''&&$msg!=''){
$to='Marc Hassan <marc.j.hassan@gmail.com>';

if(mail($to, $subject, $msg, $header)){
echo 'Email sent. Check the '.$to.' inbox to confirm that it went through.';
} else {
echo 'PHP Email error!!!</br>To: '.htmlentities($to).'</br>Subject: '.htmlentities($subject).'</br>Message: '.htmlentities($msg).'</br>Header: '.htmlentities($header).'</br>';
print_r(error_get_last());
exit;
}
} else {echo 'One of to, subject, or msg is empty!</br>';
echo 'To: '.htmlentities($to).'</br>Subject: '.htmlentities($subject).'</br>Message: '.htmlentities($msg).'</br>Header: '.htmlentities($header);exit;}

header('Location: index.php?admin');exit;

///

} else {echo 'Invalid Admin API call: '.$_SERVER['REQUEST_URI'];exit;}

////////////////////////// ADMIN API END //////////////////////////
}






////////////////////////// ADMIN HOME BEG //////////////////////////
include('header.php');
?>

<div id="bg">
<div id="content">

<h1>Admin</h1>

<p>Here, an admin will be able to:</p>
<ul>
<li>Add users</li>
<li>Authenticate users</li>
<li>Add events</li>
<li>Edit events</li>
<li>Remove events</li>
<li>See all event RSVPs</li>
<li>Send emails</li>
</ul>

<!--/// Users-->

<h2>User Administration</h2>
<div class="d">
<?php
$result=$db->query("select * from shabbat_users");
echo '<table class="adminTable"><tr><th>ID</th><th>Email</th><th>Name</th><th>Referrer</th><th>Admin</th><th>Active</th></tr>';
while ($row=$result->fetch_assoc()) {
?>

<tr><td><?=$row["user_id"]?></td><td><?=$row["user"]?></td><td><?=$row["user_name"]?></td><td><?=$row["friend"]?></td>
<td><?=$row["admin"]?></td><td><?=$row["active"]?></td>
<td><a href="index.php?admin&adminapi=toggleUserAdmin&id=<?=$row["user_id"]?>"><?php if($row["admin"]=='Y'){echo 'Revoke Admin';}else{echo 'Give Admin';} ?></a></td>
<td><a href="index.php?admin&adminapi=toggleUserActive&id=<?=$row["user_id"]?>"><?php if($row["active"]=='Y'){echo 'Inactivate';}else{echo 'Activate';} ?></a></td>
<td><a href="javascript:void(0);"
onclick="$('#addEditUserId').val('<?=$row["user_id"]?>');$('#addEditUserEmail').val('<?=$row["user"]?>');$('#addEditUserName').val('<?=$row["user_name"]?>');$('#addEditUser').show(200);">Edit User</a></td>
</tr>

<?php }
echo '</table>';
?>

<a href="javascript:void(0);"
onclick="$('#addEditUserId').val('');$('#addEditUserEmail').val('');$('#addEditUserName').val('');$('#addEditUser').show(200);">Add User</a>
<form id="addEditUser" action="index.php?admin&adminapi=addEditUser" method="post" style="display:none;">
<table class="adminTable">
<input id="addEditUserId" type="hidden" name="id" />
<tr><td>Email:</td><td><input id="addEditUserEmail" type="text" name="email" /></td></tr>
<tr><td>Name:</td><td><input id="addEditUserName" type="text" name="name" /></td></tr>
</table>
<input type="submit" value="Submit" />
</form>
</div>
<hr>
<!--/// Events-->

<h2>Event Administration</h2>
<div class="d">
<?php
$result=$db->query("SELECT
sum(case when b.resp = 'Yes' then 1+b.addl_guests else 0 end) as 'yes',
sum(case when b.resp = 'Maybe' then 1+b.addl_guests else 0 end) as 'maybe',
sum(case when b.resp = 'No' then 1+b.addl_guests else 0 end) as 'no',
a.*
FROM shabbat_events a
left outer join shabbat_rsvps b on a.event_id=b.event_id
group by a.event_id");
echo '<table class="adminTable" style="width:100%"><tr><th>ID</th><th>Name</th><th>Where</th><th>Where (Full)</th><th>Description</th><th>Start</th><th>End</th><th>Active</th><th>Y</th><th>M</th><th>N</th></tr>';
while ($row=$result->fetch_assoc()) {
?>

<tr><td><?=$row["event_id"]?></td><td><?=$row["event_name"]?></td><td><?=$row["event_where"]?></td><td><?=$row["event_where_full"]?></td>
<td><?php echo substr($row["event_description"],0,35).'...'; ?></td><td><?=$row["event_start"]?></td><td><?=$row["event_end"]?></td><td><?=$row["active"]?></td>
<td><?=$row["yes"]?></td><td><?=$row["maybe"]?></td><td><?=$row["no"]?></td>
<td><a href="index.php?admin&adminapi=toggleEventActive&id=<?=$row["event_id"]?>"><?php if($row["active"]=='Y'){echo 'Inactivate';}else{echo 'Activate';} ?></a></td>
<td><a href="javascript:void(0);"
onclick="$('#addEditEventId').val('<?=$row["event_id"]?>');$('#addEditEventName').val('<?=addslashes($row["event_name"])?>');
$('#addEditEventWhere').val('<?=addslashes($row["event_where"])?>');$('#addEditEventWhereFull').val('<?=addslashes($row["event_where_full"])?>');
$('#addEditEventDesc').val('<?=addslashes($row["event_description"])?>');$('#addEditEventStart').val('<?=$row["event_start"]?>');$('#addEditEventEnd').val('<?=$row["event_end"]?>');
$('#addEditEvent').show(200);">Edit Event</a></td>
</tr>

<?php }
echo '</table>';
?>

<a href="javascript:void(0);"
onclick="$('#addEditEventId').val('');$('#addEditEventName').val('');
$('#addEditEventWhere').val('');$('#addEditEventWhereFull').val('');
$('#addEditEventDesc').val('');$('#addEditEventStart').val('');$('#addEditEventEnd').val('');
$('#addEditEvent').show(200);">Add Event</a>
<form id="addEditEvent" action="index.php?admin&adminapi=addEditEvent" method="post" style="display:none;">
<table class="adminTable">
<input id="addEditEventId" type="hidden" name="id" />
<tr><td style="width:0;">Name:</td><td><input id="addEditEventName" type="text" name="name" style="width:100%;"/></td></tr>
<tr><td style="width:0;">Where:</td><td><input id="addEditEventWhere" type="text" name="where" style="width:100%;"/></td></tr>
<tr><td style="width:0;">Where (Full):</td><td><input id="addEditEventWhereFull" type="text" name="wherefull" style="width:100%;"/></td></tr>
<tr><td style="width:0;">Start:</td><td><input id="addEditEventStart" type="text" name="start" /></td></tr>
<tr><td style="width:0;">End:</td><td><input id="addEditEventEnd" type="text" name="end" /></td></tr>
</table>
Description:<textarea id="addEditEventDesc" name="desc" rows="3" cols="100" style="width:100%;"></textarea>
<input type="submit" value="Submit" />
</form>
</div>
<hr>
<!--/// RSVPs-->

<h2>RSVP Administration</h2>
<div class="d">
<?php
$result=$db->query("SELECT a.event_id,a.event_name,b.resp,b.addl_guests,b.note,c.user_id,c.user,c.user_name
FROM shabbat_events a
left outer join shabbat_rsvps b on a.event_id=b.event_id
left outer join shabbat_users c on b.user_id=c.user_id
group by a.event_id,c.user_id
order by a.event_id,c.user_id");
$eid=-1;
$sel = 'Event:<select id="rsvpEventSel" onchange="$(\'[id^=rsvps]\').hide();$(\'#rsvps\'+this.value).show(200);">';
while ($row=$result->fetch_assoc()) {
if($eid!=$row["event_id"]){
if($eid!=-1){$tbl .= '</table>';}
$sel .= '<option value="'.$row["event_id"].'">'.$row["event_name"].'</option>';
$tbl .= '<table class="adminTable" id="rsvps'.$row["event_id"].'" style="display:none;"><tr><th>User Email</th><th>User Name</th><th>Response</th><th>Add\'l Guests</th><th>Note</th></tr>';
}
if($row["user"]==''){$tbl .= '<tr><td>No RSVPs</td></tr>';}else{
$tbl .= '<tr><td>'.$row["user"].'</td><td>'.$row["user_name"].'</td><td>'.$row["resp"].'</td><td>'.$row["addl_guests"].'</td><td>'.$row["note"].'</td>
<td><a href="javascript:void(0);"
onclick="$(\'#addEditRSVPEventId\').val(\''.$row["event_id"].'\');$(\'#addEditRSVPUserId\').val(\''.$row["user_id"].'\');
$(\'#addEditRSVPResp\').val(\''.$row["resp"].'\');$(\'#addEditRSVPAddlGuests\').val(\''.$row["addl_guests"].'\');
$(\'#addEditRSVPEventId\')[0].disabled=true;$(\'#addEditRSVPUserId\')[0].disabled=true;
$(\'#addEditRSVP\').show(200);">Edit RSVP</a></td>
</tr>';
}

$eid=$row["event_id"];
}
$tbl .= '</table>';
$sel .= '</select>';
echo $sel.$tbl;
?>

<a href="javascript:void(0);"
onclick="$('#addEditRSVPEventId').val($('#rsvpEventSel').val());$('#addEditRSVPUserId').prop('selectedIndex',0);
$('#addEditRSVPResp').prop('selectedIndex',0);$('#addEditRSVPAddlGuests').prop('selectedIndex',0);
$('#addEditRSVPEventId')[0].disabled=false;$('#addEditRSVPUserId')[0].disabled=false;
$('#addEditRSVP').show(200);">Add RSVP</a>

<form id="addEditRSVP" action="index.php?admin&adminapi=addEditRSVP" method="post" style="display:none;" onsubmit="$('#addEditRSVPEventId')[0].disabled=false;$('#addEditRSVPUserId')[0].disabled=false;">
<table class="adminTable">
<tr><td>Event:</td><td><select id="addEditRSVPEventId" name="event_id">
<?php
$result=$db->query("SELECT event_id,event_name from shabbat_events");
while ($row=$result->fetch_assoc()) {
echo '<option value="'.$row['event_id'].'">'.$row['event_name'].'</option>';
}?>
</select></td></tr>
<tr><td>User:</td><td><select id="addEditRSVPUserId" name="user_id">
<?php
$result=$db->query("SELECT user_id,user,user_name from shabbat_users");
while ($row=$result->fetch_assoc()) {
echo '<option value="'.$row['user_id'].'">'.$row['user_name'].'</option>';
}?>
</select></td></tr>
<tr><td>Response:</td><td><select name="resp" id="addEditRSVPResp">
<option value="Yes">Yes</option>
<option value="Maybe">Maybe</option>
<option value="No">No :( :( :(</option>
</select></td></tr>
<tr><td>Add'l Guests:</td><td><select name="addl_guests" id="addEditRSVPAddlGuests">
<option>0</option>
<option>1</option>
<option>2</option>
</select></td></tr>
</table>
<input type="submit" value="Submit" />
</form>
<script type="text/javascript">$('[id^=rsvps]:first').show(200);</script>
</div>

<!--/// Emails-->

<h2>Email Administration</h2>
<div class="d">
<?php
$result=$db->query("SELECT a.*,c.user_id,GROUP_CONCAT(concat(c.user_name,' <',c.user,'>') SEPARATOR ', ') email_list
FROM shabbat_events a,shabbat_rsvps b,shabbat_users c 
where a.event_id=b.event_id and b.user_id=c.user_id and b.resp='Yes'
group by a.event_id");
$sel = '<table class="adminTable" style="width:100%"><tr><td>Event:</td><td><select id="emailEventSel" onchange="$(\'#emailTo\').val($(\'#to\'+this.value).val());$(\'#emailSub\').val($(\'#sub\'+this.value).val());$(\'#emailMsg\').val($(\'#msg\'+this.value).val());">
<option value="-1"></option>';
$tbl = '';
while ($row=$result->fetch_assoc()) {
$sel .= '<option value="'.$row["event_id"].'">'.$row["event_name"].'</option>';
$tbl .= '<input id="to'.$row["event_id"].'" type="hidden" value="'.$row["email_list"].'">';
$tbl .= '<input id="sub'.$row["event_id"].'" type="hidden" value="Fink-a-licious Shabbat! - '.$row["event_name"].'"/>';
$tbl .= '<input id="msg'.$row["event_id"].'" type="hidden" value="
<hr>
<a href=&quot;http://'.$_SERVER['HTTP_HOST'].'/shabbat/index.php?e='.$row["event_id"].'&quot; target=&quot;_blank&quot;><h2>'.$row["event_name"].'</h2></a>';
$t = date('F j, Y g:ia',strtotime($row["event_start"]));
if(date('Ymd',strtotime($row["event_start"]))==date('Ymd',strtotime($row["event_end"]))){$t.=' - '.date('g:ia',strtotime($row["event_end"]));}else{$t.=' - '.date('F j, Y g:ia',strtotime($row["event_end"]));}

$tbl.='<p><strong>When:&nbsp;</strong>'.$t.'</p>
<p><strong>Where:&nbsp;</strong>'.$row["event_where_full"].' (<a href=&quot;https://www.google.com/maps/place/'.urlencode($row["event_where_full"]).'&quot; target=&quot;_blank&quot;>map</a>)</p>
<p>'.repLinks($row["event_description"]).'</p>"/>';
}
$sel .= '</select></td></tr>';
echo $sel.$tbl;
?>

<form id="sendEmail" action="index.php?admin&adminapi=sendEmail" method="post" style="display:;" onsubmit="return $('#emailEventSel').val()>0;">
<tr><td style="width:0;">To:</td><td><input id="emailTo" name="emailTo" type="text" style="width:100%;"/></td></tr>
<tr><td style="width:0;">Subject:</td><td><input id="emailSub" name="emailSub" type="text" style="width:100%;"/></td></tr>
</table>
Message (HTML):<textarea id="emailMsg" name="emailMsg" rows="10" cols="100" style="width:100%;"></textarea>
<input type="submit" value="Submit" />
</form>
</div>


<?php }
////////////////////////// ADMIN HOME END //////////////////////////
}
////////////////////////// ADMIN END //////////////////////////

////////////////////////// AUTHENTICATED END //////////////////////////

	} else {
		header('Location: index.php?logoff');exit;
	}
} else {


	if(!isset($_POST['email'])){
		echo buildLogin('emailonly');exit;
	} elseif(!isset($_POST['name'])){
		//if($_POST['robot']!='5'){echo buildLogin('emailonly','You are such a robot!!');exit;}
		$r=validateEmail($db,$_POST['email']);
		if($r=='new'){echo buildLogin('emailandname');exit;}
		elseif($r=='exists'){header('Location: '.$_SERVER['REQUEST_URI']);exit;}
		else {echo buildLogin('emailonly',$r);exit;}
	} else {

		if($_POST['name']=='') {echo buildLogin('emailandname','Please enter your name so we know who you are!');exit;}
		if($_POST['friend']=='') {echo buildLogin('emailandname','Please tell us who referred you here!');exit;}
		$r=validateEmail($db,$_POST['email']);
		if($r=='new'){insertEmail($db,$_POST['email'],$_POST['name'],$_POST['friend']);header('Location: '.$_SERVER['REQUEST_URI']);exit;}
		elseif($r=='exists'){header('Location: '.$_SERVER['REQUEST_URI']);exit;}
		else {echo buildLogin('emailonly',$r);exit;}
	}
}

function buildLogin($type,$err=''){

$s='<div id="bleed">
<div id="title-block">
<div id="title">
<img style="height:40px;" src="img/star_logo.png"/></br>
Fink-a-licious Shabbat</div>
<div>';

if($type=='emailonly'){$s.='<div class="submit" onclick="$(\'#loginform\').show();this.style.display=\'none\';">JOIN US</div>';}
//if($type=='emailandname'){$s.='It looks like you are new here! Tell us more!';}

$s.='<form id="loginform" ';if($type=='emailonly'){$s.='style="display:none;"';}$s.=' action="'.$_SERVER['REQUEST_URI'].'" method="post">
<input name="email" placeholder="What\'s your email?" type="text" value="'.$_POST['email'].'"';if($type=='emailandname'){$s.=' readonly style="width:200px;background-color:#EBEBE4;color:black;" /></br>';}else{$s.=' autofocus style="width:200px;" /></br>';};
//if($type=='emailonly'){$s.='<tr><td>What is 2 plus 3?</td><td><input style="width:200px;" name="robot" type="text" /></td></tr>';}
if($type=='emailandname'){$s.='<input style="width:200px;" name="name" placeholder="What\'s your name?" type="text" value="'.$_POST['name'].'" autofocus/></br>
<input style="width:200px;" placeholder="Who told you about us?" name="friend" type="text" value="'.$_POST['friend'].'"/>';}
$s.='<!--<input type="submit" value="Submit" />--><div class="submit" onclick="$(\'#loginform\').submit();">ENTER</div>
</form>';
if($err){$s.='<span style="color:red">'.$err.'</span>';}

$s.='</div></div></div>';

return $s;
}

function validateEmail($db,$em){

if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {return 'That ain\'t no valid email!';}

$result = $db->query("select * from shabbat_users where user='".$em."'");
if($result->num_rows==1) {
	$row=$result->fetch_assoc();
	if($row["active"]=='Y'){
		setcookie('shabbatemail',$em,0,'/shabbat/');$_COOKIE['shabbatemail']=$em;
		$_SESSION['shabbatid'] = $row["user_id"];
		$_SESSION['shabbatname'] = $row["user_name"];
		$_SESSION['shabbatemail'] = $em;
		$_SESSION['shabbatadmin'] = $row["admin"];
		$db->query("update shabbat_users set last_ts=now() where user='".$em."'");
		$db->commit();
		return 'exists';
	} else{return 'This email address has been banned!';}
} else {return 'new';}

}

function insertEmail($db,$em,$nm,$fr=''){
$db->query("INSERT INTO shabbat_users (user,user_name,friend,join_ts) values ('".$em."','".$nm."','".$fr."',now())");
$db->commit();

validateEmail($db,$em);
}

function repLinks($str){
	return preg_replace('/((http|https)\:\/\/\S*?)((\!|\.|\,|\;)*(\s|$))/i','<a href="${1}" target="_blank">${1}</a>${3}',$str);
}
?>