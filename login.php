<?php $friendlyTitle=''; include('../mysql_include.php'); ?>

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
//$("#contact-click").click(function (){$('html, body').animate({scrollTop: $("#contact").offset().top-55}, 400);});
});


function validateForm() {

var em = document.forms["requestauth"]["email"].value;
if(em==''&&IsEmail(em)){$('#msg').text="Please enter a valid email!";return false;}

if(document.forms["requestauth"]["name"].style.display=="none"){

var formData = ; 
$.ajax({
    url : "api.php",
    type: "POST",
    data : {type:"requestauth",email:em},
    success: function(data, textStatus, jqXHR)
    {
        $('#msg').text=data
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
 
    }
});
return true;
}


}

function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

</script>

</head>

<body>

