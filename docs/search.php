<?php

include_once("src/search.php");

$searchmode = strtolower($_GET['s']);
$searchterm = $_GET['term'];
if(strcmp($searchmode, "all") == 0)
{
	$url = "https://search.sharepoint.soton.ac.uk/Pages/results.aspx?k=" . urlencode($searchterm) . "&submit=&s=All";
	header("Location: " . $url);
	exit();
}

$results = freeTextSearch($searchterm);
print("<h2>Search</h2>");

print("<table class=\"datasotonacuk_searchresults\">");
foreach($results as $result)
{
	$uri = $result['uri'];
	$label = $result['label'];
	$icon = "";
	if(array_key_exists("icon", $result))
	{
		$icon = $result['icon'];
	}

	print("<tr>");
	print("<td>");
	if(strlen($icon) > 0)
	{
		print("<img src=\"" . $icon . "\"/>");
	}
	print("</td>");
	print("<td style=\"padding-left: 10px;\">");
	print("<h4><a href=\"" . $uri . "\">" . $label . "</a></h4>");
	print("<p>" . $uri . "</p>");
	print("</td>");
	print("</tr>");
}
print("</table>");
