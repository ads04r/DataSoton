<?php

$graph->workAround4StoreBNodeBug = true;
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$graph->ns( "soton","http://id.southampton.ac.uk/ns/" );
$graph->ns( "rooms","http://vocab.deri.ie/rooms#" );
$graph->ns( "geo","http://www.w3.org/2003/01/geo/wgs84_pos#" );
$graph->ns( "dct","http://purl.org/dc/terms/" );
$graph->ns( "dcat", "http://www.w3.org/ns/dcat#" );
$graph->ns( "oo", "http://purl.org/openorg/" );
$graph->ns( "prov", "http://www.w3.org/ns/prov#" );
$graph->ns( "void", "http://rdfs.org/ns/void#" );
$building = $graph->resource( $uri );
$rdesc = $building->prepareDescription();

$rdesc->addRoute( "*" );
$rdesc->addRoute( "*/rdf:type" );
$rdesc->addRoute( "*/rdfs:label" );
$rdesc->addRoute( "*/void:triples" );
$rdesc->addRoute( "*/dct:format" );
$rdesc->addRoute( "-*/dct:requires/*" );
$rdesc->addRoute( "-*/rdf:type/*" );
$rdesc->addRoute( "-*/rdfs:label" );
$rdesc->addRoute( "dcat:accessURL/*/*" );
$rdesc->addRoute( "-*/-prov:generated" );
$rdesc->addRoute( "-*/-prov:generated/*" );
$rdesc->addRoute( "-*/-prov:generated/*/-prov:generated" );
$rdesc->addRoute( "-*/-prov:generated/*/-prov:generated/*" );

$n = $rdesc->loadSPARQL( $endpoint );

if( $format != "html" )
{
        # Unless this is HTML just let Graphite serve the document.
        if( !$rdesc->handleFormat( $format ) ) { print "404!\n"; }
        exit;
}

print(renderUri($uri));

$rdf = $rdesc->toGraph();
$item = $rdf->resource($uri);
if($item->has("dct:description"))
{
	print("<p>" . $item->get("dct:description") . "</p>");
}

// ## ACCESS URLS ##

if($item->has("http://www.w3.org/ns/dcat#accessURL"))
{
	print("<h3>Available Distributions</h3>");
	print("<table>");
	foreach($item->all("http://www.w3.org/ns/dcat#accessURL") as $dist)
	{
		$url = (string) $dist;
		$urlformat = $dist->get("dct:format")->label();
		if(strcmp($urlformat, "[UNKNOWN]") != 0)
		{
			print("<tr><td>");
			print($urlformat . "</td><td><a href=\"" . $url . "\">" . $url . "</a>");
			print("</td></tr>");
		}
	}
	foreach($item->all("http://www.w3.org/ns/dcat#accessURL") as $dist)
	{
		$url = (string) $dist;
		$urlformat = $dist->get("dct:format")->label();
		if(strcmp($urlformat, "[UNKNOWN]") == 0)
		{
			print("<tr><td></td><td><a href=\"" . $url . "\">" . $url . "</a>");
			print("</td></tr>");
		}
	}
	print("</table>");
}

// ## APPS ##

$apps = array();
foreach($item->all("-dct:requires") as $app)
{
	if(!($app->isType("http://id.southampton.ac.uk/ns/App")))
	{
		continue;
	}
	$apps[] = $app->prettyLink();
}
if(count($apps) > 0)
{
	print("<h3>Applications Using This Dataset</h3>");
	print("<table>");
	foreach($apps as $app)
	{
		print("<tr><td>" . $app . "</td></tr>");
	}
	print("</table>");
}

// ## DATASET INFORMATION ##

print("<h3>Dataset Information</h3>");
print("<table>");
if($item->has("rdf:type"))
{
	print("<tr><td>Type:</td><td>");
	print($item->all("rdf:type")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
if($item->has("http://rdfs.org/ns/void#triples"))
{
	print("<tr><td>Triples:</td><td>");
	print($item->get("http://rdfs.org/ns/void#triples") . "");
	print("</td></tr>");
}
if($item->has("dct:license"))
{
	print("<tr><td>License:</td><td>");
	print($item->all("dct:license")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
if($item->has("dct:conformsTo"))
{
	print("<tr><td>Conforms to:</td><td>");
	print($item->all("dct:conformsTo")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
if($item->has("dct:publisher"))
{
	print("<tr><td>Publisher:</td><td>");
	print($item->all("dct:publisher")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
if($item->has("oo:contact"))
{
	print("<tr><td>Contact:</td><td>");
	print($item->all("oo:contact")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
if($item->has("oo:corrections"))
{
	print("<tr><td>Corrections:</td><td>");
	print($item->all("oo:corrections")->prettyLink()->join("<br>"));
	print("</td></tr>");
}
else
{
	print("<tr><td>Corrections:</td><td>");
	print("None specified. Please contact <a href=\"mailto:ads04r@ecs.soton.ac.uk\">Ash Smith</a> with any issues.");
	print("</td></tr>");
}
if($item->has("-dct:isPartOf"))
{
	$date = 0;
	@$date = strtotime($item->get("-dct:isPartOf")->get("-http://www.w3.org/ns/prov#generated")->get("http://www.w3.org/ns/prov#endedAtTime"));
	if($date > 0)
	{
		print("<tr><td>Published:</td><td>" . date("jS F Y", $date) . "</td></tr>");
	}
}
print("</table>");

// ## PROV ##

$prov = $rdf->allOfType("http://www.w3.org/ns/prov#Activity");
if(count($prov) > 0)
{
	print("<h3>Provenance</h3>");

	$files = array();
	$urls = array();
	foreach($prov as $event)
	{
		foreach($event->all("http://www.w3.org/ns/prov#used") as $used)
		{
			$url = (string) $used;
			if(preg_match("|^http://([^\\./]+)\\.southampton\\.ac\\.uk/|", $url) > 0)
			{
				$files[$url] = true;
			}
			else
			{
				$urls[$url] = true;
			}
		}
	}

	if(count($files) > 0)
	{
		print("<p>While generating this dataset the following files were used.</p>");
		print("<table>");
		foreach(array_keys($files) as $url)
		{
			$file = preg_replace("|^.*/|", "", $url);
			if(strcmp($file, "rapper") == 0) // I'm not personally happy with redistributing rapper as we didn't write it! - Ash
			{
				continue;
			}
			if(strcmp($file, "publish.json") == 0)
			{
				print("<tr>");
				print("<td><a href=\"" . $url . "\">" . $file . "</a></td>");
				print("<td>'<a href=\"https://github.com/cgutteridge/Hedgehog\">Hedgehog</a>' configuration file</td>");
				print("</tr>");
				continue;
			}
			if(strcmp($file, "grinder") == 0)
			{
				print("<tr>");
				print("<td><a href=\"" . $url . "\">" . $file . "</a></td>");
				print("<td>'<a href=\"https://github.com/cgutteridge/Grinder\">Grinder</a>' script</td>");
				print("</tr>");
				continue;
			}

			$ext = preg_replace("|^.*\\.|", "", $file);
			print("<tr>");
			print("<td><a href=\"" . $url . "\">" . $file . "</a></td>");
			switch($ext)
			{
				case 'csv':
					print("<td>Comma-seperated text file (can be loaded in Excel)</td>");
					break;
				case 'tsv':
					print("<td>Tab-seperated text file (can be loaded in Excel)</td>");
					break;
				case 'psv':
					print("<td>Pipe-seperated text file (can be loaded in Excel)</td>");
					break;
				case 'nt':
					print("<td>RDF/N-Triples file</td>");
					break;
				case 'n3':
					print("<td>RDF/N-Triples file</td>");
					break;
				case 'rdf':
					print("<td>RDF/XML file</td>");
					break;
				case 'ttl':
					print("<td>RDF/Turtle file</td>");
					break;
				case 'xls':
					print("<td>Excel spreadsheet</td>");
					break;
				case 'xml':
					print("<td>Extensible Markup Language file</td>");
					break;
				case 'xsl':
					print("<td>XSLT stylesheet</td>");
					break;
				case 'html':
					print("<td>Hypertext Markup Language</td>");
					break;
				case 'txt':
					print("<td>Text file</td>");
					break;
				case 'json':
					print("<td>Javascript Object Notation</td>");
					break;
			}
			print("</tr>");
		}
		print("</table>");
	}
/*
	if(count($urls) > 0)
	{
		print("<p>This dataset incorporates information from the following web resources.</p><ul>");
		foreach(array_keys($urls) as $url)
		{
			print("<li> " . $url . "</li>");
		}
		print("</ul>");
	}
*/
}

// ## MISC ##

//print("<hr>");
//$rdesc->handleFormat("rdf.html");
