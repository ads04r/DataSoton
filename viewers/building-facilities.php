<?php

$facs = array();
$cls = array();

$output = false;

foreach( $building->all( "-sr:within" )->sort( "rdfs:label" ) as $inner_thing )
{
	if( $inner_thing->isType( "oo:Facility" ) ) { $facs[]=$inner_thing; }
	if( $inner_thing->isType( "http://id.southampton.ac.uk/ns/SyllabusLocation" ) ) { $cls[]=$inner_thing; }
}

if(count($facs) > 0)
{
	print "<h2>Research Facilities</h2>";
	print "<table style=\"margin-bottom: 20px;\">";
	foreach( $facs as $facility )
	{
		print "<tr><td>".$facility->prettyLink()."</td>";
		if(!($facility->has("oo:primaryContact")))
		{
			print("</tr>");
			continue;
		}
		print("<td>" . $facility->get("oo:primaryContact")->prettyLink() . "</td>");
		print("</tr>");
	}
	print "</table>";
	$output = true;
}

if(count($cls) > 0)
{
	print("<h2>Teaching Rooms</h2>");
	foreach( $cls as $room )
	{

		$disabled = "";
		$features = "";
		foreach($room->all("oo:hasFeature") as $feature)
		{
			if(strcmp(substr("" . $feature->type()->label(), 0, 4), "RSC-") == 0)
			{
				$disabled .= "<tr><td>" . $feature->type()->label() . "</td></tr>";
				continue;
			}
			$features .= "<tr><td>" . $feature->type()->prettyLink() . "</td></tr>";
		}
		if(strlen($features) + strlen($disabled) > 0)
		{
			print "<h3>".$room->prettyLink()."</h3>";

			print("<div class=\"section-box\">");

			if($room->has("-foaf:depicts"))
			{
				$image = "";
				$width = 0;
				foreach($room->all("-foaf:depicts") as $img)
				{
					$imgwidth = (int) ("" . $img->get("http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#width"));
					if(($imgwidth > $width) & ($imgwidth <= 250))
					{
						$image = "" . $img;
						$width = $imgwidth;
					}
				}
				if(strlen($img) > 0)
				{
					print("<img src=\"" . $image . "\" align=\"right\" width=\"" . $width . "\">");
				}
			}


			if($room->has("oo:capacity"))
			{
				print("<p>Capacity: " . $room->get("oo:capacity") . "</p>");
			}
			print("<table style=\"min-height: 250px;\">" . $features . $disabled . "</table>");

			print("</div>");
		}
	}
	$output = true;
}

if(!($output))
{
	print("<p>We're sorry, we don't have any information on facilities within this building. This may be because the building has no facilities, or because our data is incomplete.</p>");
}
