<?php

$date=date('Y-m-d H:i:s',$timestamp=time()-5*60*60);



$to="Marc Hassan <marc.j.hassan@gmail.com>";

$header="Reply-To: Marc Hassan <marc.j.hassan@gmail.com>\r\n";
$header.="Return-Path: Marc Hassan <marc.j.hassan@gmail.com>\r\n";
$header.="From: speechwithjulie@speechwithjulie.com <speechwithjulie@speechwithjulie.com>\r\n";
$header.="Organization: Speech with Julie\r\n";
$header.="Content-Type: text/html\r\n";

$subject = 'Reminder: Our first shabbat is coming up!';
$msg = '<span style="color:red">Dear Marc,

Reminder: Our first shabbat is coming up!

Sincerely, Us</span>';


mail($to, $subject, $msg, $header);

?>