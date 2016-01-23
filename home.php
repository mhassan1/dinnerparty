<?php $friendlyTitle=''; include('../mysql_include.php'); session_start(); ?>

<?php function selected($thisPage,$thatPage){if($thisPage==$thatPage){echo 'selected';}} ?>

<html>
<head>
<title>Fink-a-licious Shabbat<?php if($friendlyTitle){echo ' - '.$friendlyTitle;}?></title>
<link rel="stylesheet" type="text/css" href="css/style.css"/>
<!--<link rel="stylesheet" type="text/css" href="css/style_900.css" media="(max-width:900px)"/>-->
<!--<link rel="stylesheet" type="text/css" href="css/style_480.css" media="(max-width:480px)"/>-->
<!--<link rel="shortcut icon" href="favicon.ico" />-->
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>

<script>
        $(document).ready(function (){
$("#home-click").click(function (){$('html, body').animate({scrollTop: 0}, 400);});
$("#about-click").click(function (){$('html, body').animate({scrollTop: $("#about").offset().top-55}, 400);});
$("#events-click").click(function (){$('html, body').animate({scrollTop: $("#events").offset().top-55}, 400);});
$("#contact-click").click(function (){$('html, body').animate({scrollTop: $("#contact").offset().top-55}, 400);});
});
</script>

</head>

<body>

<div id="bg">

<div id="header">

<div id="menus">
<span style="font-size:24px;margin:0 20px 0 20px;float:left;">Fink-a-licious Shabbat</span>
<ul id="a">
	<li class="<?=selected($thisPage,'Home');?>" id="home-click"><a>Home</a></li>
	<li class="<?=selected($thisPage,'About');?>" id="about-click"><a>About</a></li>
	<li class="<?=selected($thisPage,'Events');?>" id="events-click"><a>Events</a></li>
	<!--<li class="<?=selected($thisPage,'Contact');?>" id="contact-click"><a>Contact</a></li>-->
</ul>
<span>

<?php echo $_SESSION['shabbatname'];
?>
</span>
<a href="index.php?logoff">Log off</a>
</div>

</div>

<div id="content">

<h1>Fink-a-licious Shabbat!</h1>
<img style="display:block;margin:0 auto;" src="http://cdn.someecards.com/someecards/usercards/1722aba79c68be6c64eacde9444abbbbbe.png" />

<hr>
<h1 id="about">About</h1>

<h2>What is it?</h2>
<p>This is what it is!</p>

<h2>How can I get involved?</h2>
<p>This is how!</p>


<hr>

<h1 id="events">Events</h1>

<?php
$result=$db->query("select * from shabbat_events a
inner join shabbat_users c
left outer join shabbat_rsvps b on a.event_id=b.event_id and c.user_id=b.user_id
where c.user_id=".$_SESSION['shabbatid']);
while ($row=$result->fetch_assoc()) {
?>


<h2><?=$row["event_name"]?></h2><?=$row["event_description"]?>

<?php if($row["resp"]==''){ ?>

<p>You have not RSVP'd. Click <strong><a href="javascript:void(0);" onclick="$('#rsvp<?=$row["event_id"]?>').show();">here</a></strong> to RSVP!</p>

<?php } elseif($row["resp"]=='Yes'){ ?>

<?php if($row["addl_guests"]!=0){$addl=' (+'.$row["addl_guests"].' guests)';} ?>

<p>Hooray! You are coming to this<?=$addl?>! Click <strong><a href="javascript:void(0);" onclick="$('#rsvp<?=$row["event_id"]?>').show();">here</a></strong> to change your response!</p>

<?php } elseif($row["resp"]=='No'){ ?>

<p>Womp womp! You are not coming to this! Click <strong><a href="javascript:void(0);" onclick="$('#rsvp<?=$row["event_id"]?>').show();">here</a></strong> to change your response!</p>

<?php } elseif($row["resp"]=='Maybe'){ ?>

<p>You replied Maybe! Click <strong><a href="javascript:void(0);" onclick="$('#rsvp<?=$row["event_id"]?>').show();">here</a></strong> to change your response!</p>


<?php } ?>

<!--&nbsp;-&nbsp;<strong><a href="javascript:void();" onclick="showWhosComing">Who's coming?</a></strong>-->

<form id="rsvp<?=$row["event_id"]?>" style="display:none;" action="index.php?api=rsvp" method="post">
<input name="type" type="hidden" value="rsvp" />

<input name="event_id" type="hidden" value="<?=$row["event_id"]?>" />

Response:<select name="resp">
<option value="Yes">Yes</option>
<option value="Maybe">Maybe</option>
<option value="No">No :( :( :(</option>
</select>
Add'l Guests:<select name="addl_guests">
<option>0</option>
<option>1</option>
<option>2</option>
</select>
<input type="submit" value="RSVP" />
</form>

<hr>

<?php } ?>