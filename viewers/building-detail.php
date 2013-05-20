<?php

if( $building->has( "geo:lat", "-foaf:depicts" ))
{
	print "<div class='datasotonacuk_rightsidebar'>";
	foreach( $building->all( "-foaf:depicts" ) as $pic )
	{
		if( !$pic->has( "http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#width" )) { continue;}
		if( $pic->getString( "http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#width" )!=300 ) { continue; }
		print "<img src='".$pic->toString()."' />";
		print "<div style='font-size:80%;text-align:right'>&copy;";
		if( $pic->has( "dct:created" ) ) { print $pic->getString( "dct:created" ); }
		print " ";
		if( $pic->has( "dct:creator" ) ) { 
			$creator = $pic->get( "dct:creator" );
			if( $creator->has( "foaf:homepage" ) ) { print "<a href='".$creator->toString( "foaf:homepage" )."'>"; }
			print $creator->label();
			if( $creator->has( "foaf:homepage" ) ) { print "</a>"; }
		}
		if( $pic->has( "dct:license" ) ) { print " (".$pic->get( "dct:license" )->prettyLink().")"; }
		print "</div>";
	}
	if( $building->has( 'geo:lat' ) )
	{
		$ll = $building->getString("geo:lat").",".$building->getString("geo:long");
		print "<div style='margin-top:20px'><img src='/graphics/staticmaplite/staticmap.php?center=" . $building->getString("geo:lat") . "," . $building->getString("geo:long") . "&zoom=17&size=300x300&markers=" . $building->getString("geo:lat") . "," . $building->getString("geo:long") . ",ol-marker-green' /></div>";

		$mapurl = "http://www.openstreetmap.org/?mlat=" . $building->getString("geo:lat") . "&mlon=" . $building->getString("geo:long") . "&zoom=16";
		//print "<div style='text-align:right'><a href='$mapurl'>View Larger Map</a> (<a href='$mapurl&output=embed'>Full Screen</a>)</div>";
		print "<div style='text-align:right'><a href='$mapurl'>View Larger Map</a></div>";
	}

	print "</div>";
}


if( $building->isType( "soton:RetiredID" ) )
{
	print "
<div style='padding-right: 330px'>
<div class='rounded' style='background-color:#fee; border: 3px solid #c88;padding:0.5em;font-weight:bold;margin-bottom:0.5em'>
This Building Identifier has been retired, and should not be used.
";
	if( $building->has( "owl:sameAs" ) )
	{
		print "Alternate identifier: ".$building->all( "owl:sameAs" )->link()->join( ", " );
	}
	print "
</div>
</div>
";
}

if( $building->isType( "soton:ExUoSBuilding" ) )
{
	print "
<div style='padding-right: 330px'>
<div class='rounded' style='background-color:#fee; border: 3px solid #c88;padding:0.5em;font-weight:bold;margin-bottom:0.5em'>
This Building is no longer included as part of the Estate of the University of Southampton. There may be a number of reasons for this; including sale or demolition.
</div>
</div>
";
}


print("<table>");


if( $building->has( "sr:within" ) )
{
	foreach( $building->all( "sr:within" ) as $bigger )
	{
		if( $bigger->isType( "http://www.w3.org/ns/org#Site" ) )
		{
			print "<tr><td>Site:</td><td>".$bigger->prettyLink()."</td></tr>";
		}
	}
}


if( $building->has("soton:buildingDate" ) )
{
	print "<tr><td>Construction:</td><td>".$building->getString( "soton:buildingDate" )."</td></tr>";
}
if( $building->has("soton:buildingArchitect" ) )
{
	print "<tr><td>Architect:</td><td>".$building->get("soton:buildingArchitect" )->label()."</td></tr>";
}

if( $building->has( "oo:hasFeature","oo:lacksFeature" ) )
{
	print "<tr><td>Features:</td><td>";
	print $building->all( "oo:hasFeature","oo:lacksFeature" )->get( "rdfs:label" )->join( "<br>" );
	print "</td></tr>";
}

print("</table>");


if( $building->has( "soton:disabledGoPage" ) )
{
	print "<p><a href='".$building->getString("soton:disabledGoPage")."'>View Disability Report for this Building</a></p>";
}

if( $building->has( "rooms:occupant" ) )
{
	$occupants = "";
	foreach($building->all("rooms:occupant") as $occupant)
	{
		if(!($occupant->isType("http://www.w3.org/ns/org#Organization")))
		{
			continue;
		}
		if($occupant->has("-org:hasSubOrganization"))
		{
			continue;
		}
		$occupants .= "<tr><td>" . $occupant->prettylink() . "</td></tr>";
	}
	if(strlen($occupants) > 0)
	{
	print("<h3>Occupants</h3>");
	print("<table>" . $occupants . "</table>");
	}
}

$intranet_info = "";
foreach( $building->all( "rooms:occupant" ) as $occupant )
{
	if( $occupant->isType("soton:BuildingOccupantsGroup" ) && $occupant->has( "foaf:mbox" ))
	{
		$intranet_info .= "<p><strong>Email the occupants: </strong>";
	 	$intranet_info .= "Bulk email lists may only be emailed by members of the university <i>and in addition</i> are moderated. ";
		$intranet_info .= $occupant->get("foaf:mbox" )->prettyLink();
		$intranet_info .= "</p>";
	
		# this bit is a bit hacky
		$list = preg_replace( "/@.*/","", $occupant->get("foaf:mbox" ) );
		$list = preg_replace( "/mailto:/","",$list );
		
		$intranet_info .= "<p><strong>View the occupants: </strong>";
	 	$intranet_info .= "The list of the staff in a building is taken from the 'subscribe' system and is only visible to members of the university: <a href='http://all.soton.ac.uk/search/index.php?list=$list&amp;view=map#b".$building->get("skos:notation")."'>staff in ".$building->label()."</a>.</p>";
	}
}
if( $intranet_info != "" )
{
	print "<h3>Restricted Services</h3>$intranet_info";
	#print "<div style='margin-top: 2em; border: 2px solid rgb(204, 204, 255); padding: 10px; -moz-border-radius: 10px 10px 10px 10px; background-color: rgb(249, 249, 255);'><h2>Restricted Services</h2>$intranet_info</div>";
}
