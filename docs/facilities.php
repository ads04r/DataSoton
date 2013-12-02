<?php

$sparql = "
PREFIX oo:       <http://purl.org/openorg/>
PREFIX rdfs:     <http://www.w3.org/2000/01/rdf-schema#>
PREFIX sr: 	 <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/>
PREFIX geo:      <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX rooms:    <http://vocab.deri.ie/rooms#>
PREFIX soton:    <http://id.southampton.ac.uk/ns/>

CONSTRUCT
{
   ?facility a oo:Facility .
   ?facility rdfs:label ?label .
   ?facility sr:within ?building .
   ?building a rooms:Building .
   ?building rdfs:label ?buildingname .
   ?facility geo:lat ?lat .
   ?facility geo:long ?long .
   ?facility oo:organizationPart ?org .
   ?org rdfs:label ?orgname .
   ?facility soton:facilityIsRCUKCosted ?costed .
}
WHERE
{
   ?facility a oo:Facility .
   ?facility rdfs:label ?label .
   OPTIONAL { ?facility soton:facilityIsRCUKCosted ?costed . }
   OPTIONAL {
      ?facility sr:within ?building .
      ?building a rooms:Building ; geo:lat ?lat ; geo:long ?long .
      ?building rdfs:label ?buildingname .
   }
   OPTIONAL  {
      ?facility oo:organizationPart ?org .
      ?org a <http://www.w3.org/ns/org#OrganizationalUnit> .
      ?org rdfs:label ?orgname .
   }
}
";

//======================================================================

$endpoint = $f3->get('sparql_endpoint');

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"By Division"),
	array("id"=>"building", "text"=>"By Building")
);

$graph = new Graphite();
$graph->workAround4StoreBNodeBug = true;
$graph->ns( "oo","http://purl.org/openorg/" );
$graph->ns( "sr","http://data.ordnancesurvey.co.uk/ontology/spatialrelations/" );
$n = $graph->loadSPARQL($endpoint, $sparql);

$lists = array( "building"=>array(), "org"=>array() );
foreach( $graph->allOfType( "oo:Facility" ) as $facility )
{

	if( $facility->has( "sr:within" ))
	{
		$lists["building"][(string)$facility->get( "sr:within" )->label()][(string)"building"] = $facility->get( "sr:within" );
		$lists["building"][(string)$facility->get( "sr:within" )->label()]["list"][(string)$facility->label()] = $facility;
	}
	if( $facility->has( "oo:organizationPart" ))
	{
		$lists["org"][(string)$facility->get( "oo:organizationPart" )->label()]["org"] = $facility->get( "oo:organizationPart" );
		$lists["org"][(string)$facility->get( "oo:organizationPart" )->label()]["list"][(string)$facility->label()] = $facility;
	}
	else
	{
		#print "no org: ".$facility->prettyLink();
	}
	if( !$facility->has( "oo:organizationPart", "sr:within" ))
	{
		#error?
	}
}

if(strcmp($params['format'], "rdf") == 0)
{
        header("Content-type: application/rdf+xml");
        print($graph->serialize("RDFXML"));
        exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
        header("Content-type: text/turtle");
        print($graph->serialize("Turtle"));
        exit();
}
if(strcmp($params['format'], "nt") == 0)
{
        header("Content-type: text/plain");
        print($graph->serialize("NTriples"));
        exit();
}

print("<h2>Facilities</h2>");

print("<p>To search these facilities, and those of other universities, please see <a href=\"http://equipment.data.ac.uk/\">equipment.data.ac.uk</a>.</p>");

print("<div class=\"datasotonacuk_tabs\">");
print("<ul>");
foreach($tabs as $tab)
{
	if(strcmp($tab['id'], $query) == 0)
	{
		print("<li class=\"tabcurrent\"><a href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
	}
	else
	{
		print("<li><a href=\"?" . $tab['id'] . "\">" . $tab['text'] . "</a></li>");
	}
}

print("</ul>");
print("</div>");

if(strcmp($query, "building") == 0)
{
	// View list by building.

	ksort( $lists["building"] );
	foreach( $lists["building"] as $buildingdata )
	{
		print "<h4>".$buildingdata["building"]->label()."</h4><ul class=\"facilities\">";
		ksort( $buildingdata["list"] );
		foreach( $buildingdata["list"] as $facility )
		{
			print "<li>".$facility->prettyLink();
			if( $facility->getString( "soton:facilityIsRCUKCosted" ) == "true" )
			{
				print "<small> - RCUK Costed</small>";
			}
			print "</li>";
		}
		print "</ul>";
	}

} else {
	// View list by division.

	ksort( $lists["org"] );
	foreach( $lists["org"] as $orgdata )
	{
		print "<h4>".$orgdata["org"]->label()."</h4><ul class=\"facilities\">";
		ksort( $orgdata["list"] );
		foreach( $orgdata["list"] as $facility )
		{
			print "<li>".$facility->prettyLink();
			if( $facility->getString( "soton:facilityIsRCUKCosted" ) == "true" )
			{
				print "<small> - RCUK Costed</small>";
			}
			print "</li>";
		}
		print "</ul>";
	}

}
