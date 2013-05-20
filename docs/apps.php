<?php

include_once("src/opendata.php");
$type = "http://id.southampton.ac.uk/ns/App";
$apps = getEntities($type, $f3->get('sparql_endpoint'));

if(strcmp($params['format'], "rdf") == 0)
{
	header("Content-type: application/rdf+xml");
	print($apps->serialize("RDFXML"));
	exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
	header("Content-type: text/turtle");
	print($apps->serialize("Turtle"));
	exit();
}
if(strcmp($params['format'], "nt") == 0)
{
	header("Content-type: text/plain");
	print($apps->serialize("NTriples"));
	exit();
}

print("<h2>Apps Using Our Data</h2>\n");
print("<p>These are Apps using data from the University. If you would like your App here, or have an App you'd like to see, please <a href=\"mailto:opendata@ecs.soton.ac.uk\">let us know</a>.</p>\n");

foreach($apps->allOfType($type) as $app)
{
	print("<h3><a href=\"" . $app . "\">" . $app->label() . "</a></h3>");
	print("<p><a style=\"font-family: sans-serif; font-size: small;\" href=\"" . $app . "\">" . $app . "</a></p>");
	if($app->has("dct:description"))
	{
		print("<p>" . $app->get("dct:description") . "</p>");
	}
	print("<table style=\"margin-bottom: 20px;\">");
	if($app->has("http://id.southampton.ac.uk/ns/appType"))
	{
		print("<tr><td style=\"width: 8em;\">App type:</td><td>");
		print($app->all("http://id.southampton.ac.uk/ns/appType")->prettyLink()->join("<br>"));
		print("</td></tr>");
	}
	if($app->has("dct:created"))
	{
		$dt = strtotime($app->get("dct:created"));
		print("<tr><td>Created:</td><td>" . gmdate("jS F Y", $dt) . "</td></tr>");
	}
	if($app->has("dct:creator"))
	{
		print("<tr><td>Created by:</td><td>");
		print($app->all("dct:creator")->prettyLink()->join("<br>"));
		print("</td></tr>");
	}
	if($app->has("dct:requires"))
	{
		print("<tr><td>Uses:</td><td>");
		print($app->all("dct:requires")->prettyLink()->join("<br>"));
		print("</td></tr>");
	}
	print("<table>");
}
