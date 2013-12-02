<?php

function getCurrentSession($type, $items)
{
	$now = time();
	$sessions = array();
	foreach($items->allOfType($type) as $item)
	{
		$begin = strtotime("" . $item->get("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime"));
		$end = strtotime("" . $item->get("http://purl.org/NET/c4dm/timeline.owl#endsAtDateTime"));
		if($begin < $now)
		{
			$sessions[] = "" . $item;
		}
	}
	rsort($sessions);
	return($sessions[0]);
}

include_once("src/opendata.php");
$type = "http://id.southampton.ac.uk/ns/AcademicSession";
$items = getEntities($type, $f3->get('sparql_endpoint'));

if(strcmp($params['format'], "rdf") == 0)
{
        header("Content-type: application/rdf+xml");
        print($items->serialize("RDFXML"));
        exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
        header("Content-type: text/turtle");
        print($items->serialize("Turtle"));
        exit();
}
if(strcmp($params['format'], "nt") == 0)
{
        header("Content-type: text/plain");
        print($items->serialize("NTriples"));
        exit();
}

$uri = getCurrentSession($type, $items);

if(strcmp($_SERVER['HTTP_HOST'], "marbles.ecs.soton.ac.uk") == 0)
{
	$uri = preg_replace("|^http://id\\.southampton\\.ac\\.uk/|", "http://marbles.ecs.soton.ac.uk/", $uri);
}

header("HTTP/1.1 302 Moved Temporarily");
header("Location: " . $uri);
exit();
