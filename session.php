<?php
session_start();
print_r($_SESSION);




$the_date = strtotime("2010-01-19 00:00:00");
echo(date_default_timezone_get() . "<br />");
echo(date("Y-d-mTG:i:sz",$the_date) . "<br />");
echo(date_default_timezone_set("UTC") . "<br />");
echo(date("Y-d-mTG:i:sz", $the_date) . "<br />");





echo strip_tags('<p>test</p>');

echo $_SERVER['REQUEST_URI'];




?>