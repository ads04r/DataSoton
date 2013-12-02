<?php

$format = $params['format'];

if(strcmp($format, "kml") == 0)
{
	header("Content-type: application/xml");
	exit();
}

print("<h2>University of Southampton Places</h2>");

print("<h3>Sites</h3>");

print("<h3>Teaching Buildings</h3>");
