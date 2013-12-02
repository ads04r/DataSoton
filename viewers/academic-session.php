<?php

function getWeekNumber($timestamp, $dtstart) // Mercilessly stolen from whatweekisit.
{
	$dt = $dtstart;
	while(gmdate("w", $dt) != 0)
	{
		$dt = $dt - 86400;
	}
	$w = (int) (($timestamp - $dt) / (7 * 86400));

	return($w);
}

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"General", "type"=>""),
	array("id"=>"ug", "text"=>"Undergraduate", "type"=>"http://id.southampton.ac.uk/banner/level/UG"),
	array("id"=>"msct", "text"=>"PG Taught", "type"=>"http://id.southampton.ac.uk/banner/level/PC"),
	array("id"=>"mscr", "text"=>"PG Research", "type"=>"http://id.southampton.ac.uk/banner/level/PR")
);

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "event","http://purl.org/NET/c4dm/event.owl#" );
$graph->ns( "tl","http://purl.org/NET/c4dm/timeline.owl#" );
$year = $graph->resource( $uri );
$rdesc = $year->prepareDescription();
$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );

if(strlen($query) > 0)
{
	$rdesc->addRoute( "-soton:inAcademicSession" );
	$rdesc->addRoute( "-soton:inAcademicSession/rdf:type" );
	$rdesc->addRoute( "-soton:inAcademicSession/rdfs:label" );
	$rdesc->addRoute( "-soton:inAcademicSession/soton:bannerLevel" );
	$rdesc->addRoute( "-soton:inAcademicSession/soton:bannerUCASCode" );
	$rdesc->addRoute( "-soton:inAcademicSession/soton:bannerShortJACSCode" );
	$rdesc->addRoute( "-soton:inAcademicSession/soton:bannerShortJACSCode/-skos:narrower" );
	$rdesc->addRoute( "-soton:inAcademicSession/soton:bannerProgrammeHasTheme" );
} else {
	$rdesc->addRoute( "skos:related" );
	$rdesc->addRoute( "skos:related/*" );
}

$n = $rdesc->loadSPARQL( $endpoint );

if( $format == "sparql" )
{
	header("Content-type: text/plain");
	exit();
}

if( $format != "html" )
{
        # Unless this is HTML just let Graphite serve the document.
        if( !$rdesc->handleFormat( $format ) ) { print "404!\n"; }
        exit();
}

print("<p>" . $uri . "</p>");

$type = "";
print("<div class=\"datasotonacuk_tabs\">");
print("<ul>");
foreach($tabs as $tab)
{
	if(strcmp($tab['id'], $query) == 0)
	{
		print("<li class=\"tabcurrent\"><a style=\"width: 120px;\" href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
		$type = $tab['type'];
	}
	else
	{
		print("<li><a style=\"width: 120px;\" href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
	}
}

print("</ul>");
print("</div>");

if(strlen($type) == 0)
{
	$dtstart = strtotime($year->get("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime"));
	$dtend = strtotime($year->get("http://purl.org/NET/c4dm/timeline.owl#endsAtDateTime"));
	$now = time();

	if(($dtstart < $now) & ($now < $dtend))
	{
		print("<p>This is the current academic year.</p>");
	}

	print("<table>");
	print("<tr><td>Year Start:</td><td>" . date("l jS F Y", $dtstart) . "</td></tr>");
	print("<tr><td>Year End:</td><td>" . date("l jS F Y", $dtend) . "</td></tr>");

	if(($dtstart < $now) & ($now < $dtend))
	{
		$week = getWeekNumber($now, $dtstart);
		print("<tr><td>Current week:</td><td>" . $week . "</td></tr>");
	}

	print("</table>");

	print("<h3>Terms and Semesters</h3>");

	print("<table>");

	foreach($year->all("skos:related")->sort("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime") as $rel)
	{
		if(!($rel->isType("http://id.southampton.ac.uk/ns/AcademicSessionSemester")))
		{
			continue;
		}
		$dt = strtotime("" . $rel->get("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime"));
		print("<tr>");
		print("<td>" . preg_replace("/([^,]+),(.*)/", "$1", "" . $rel->label()) . ":</td>");
		print("<td>Begins " . date("l jS F Y", $dt) . "</td>");
		print("</tr>");
	}
	print("<tr><td>&nbsp;</td></tr>");
	foreach($year->all("skos:related")->sort("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime") as $rel)
	{
		if(!($rel->isType("http://id.southampton.ac.uk/ns/AcademicSessionTerm")))
		{
			continue;
		}
		$dt = strtotime("" . $rel->get("http://purl.org/NET/c4dm/timeline.owl#beginsAtDateTime"));
		print("<tr>");
		print("<td>" . preg_replace("/([^,]+),(.*)/", "$1", "" . $rel->label()) . ":</td>");
		print("<td>Begins " . date("l jS F Y", $dt) . "</td>");
		print("</tr>");
	}

	print("</table>");

} else {

	$progs = $year->all("-soton:inAcademicSession")->sort("rdfs:label");
	if(count($progs) == 0)
	{
		print("<p>We currently have no module information for this academic session.</p>");
	} else {
		print("<table class=\"datasotonacuk_modulelist\"><tr><th>Programme</th><th>Theme</th><th>UCAS&nbsp;Code</th></tr>");
	}
	foreach($progs as $prog)
	{
		if(!($prog->isType("soton:Programme")))
		{
			continue;
		}
		$level = "" . $prog->get("soton:bannerProgrammeHasTheme")->get("soton:bannerLevel");
		if(strcmp($level, $type) != 0)
		{
			continue;
		}
		$themes = $prog->all("soton:bannerProgrammeHasTheme")->sort("rdfs:label");
		$ct = count($themes);
		print("<tr>");
		print("<td");
		if($ct > 1)
		{
			print(" rowspan=\"" . $ct . "\"");
		}
		print(">" . $prog->label() . "</td>");
		$rows = array();
		foreach($themes as $theme)
		{
			$ucas = "";
			if($theme->has("soton:bannerUCASCode"))
			{
				$ucas .= $theme->get("soton:bannerUCASCode");
				$ucas = preg_replace("|(.*)/([^/]*)|", "$2", $ucas);
			}
			$row = "";
			$row .= "<td>" . $theme->prettyLink() . "</td>";
			$row .= "<td>" . $ucas . "</td>";
			$rows[] = $row;
		}
		print(implode("</tr><tr>", $rows));
		print("</tr>");
	}
	if(count($progs) > 0)
	{
		print("</table>");
	}


}
