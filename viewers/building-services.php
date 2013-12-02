<?php

$poss = array();
$facs = array();
$clusters = array();
$mfds = array();

$output = false;

foreach( $building->all( "-sr:within" )->sort( "rdfs:label" ) as $inner_thing )
{
	if( $inner_thing->isType( "oo:Facility" ) ) { $facs[]=$inner_thing; }
	if( $inner_thing->isType( "http://www.productontology.org/id/Multifunction_printer" ) ) { $mfds[]=$inner_thing; }
	if( $inner_thing->has( "http://id.southampton.ac.uk/ns/workstationSeats" ) ) { $clusters[]=$inner_thing; }
        if( $inner_thing->isType( "gr:LocationOfSalesOrServiceProvisioning" ) ) { $poss[]=$inner_thing; }
}

if(count($poss) > 0)
{
	$html = "";
        foreach( $poss as $pos )
        {
		if($pos->has("soton:workstationSeats"))
		{
			continue;
		}

                $html .= "<tr><td>";
		if($pos->has("oo:mapIcon"))
		{
			$html .= "<img src=\"" . $pos->get("oo:mapIcon") . "\">";
		}
		$html .= "</td>";
                $html .= "<td>" . $pos->prettyLink() . "</td>";
                $html .= "</tr>";
        }
	if(strlen($html) > 0)
	{
	        print "<h2>Points of Service</h2>";
	        print "<table style=\"margin-bottom: 20px;\">";
		print($html);
	        print "</table>";
		$output = true;
	}
}

if((count($mfds) > 0) | (count($clusters) > 0))
{
	print("<h2>iSolutions Services</h2>");
	print "<table style=\"margin-bottom: 20px;\">";
	foreach($clusters as $cluster)
	{
		print("<tr>");
		print("<td>" . $cluster->prettyLink() . "</td>");
		print("<td>" . $cluster->get("soton:workstationFreeSeats")->toString() . " of " . $cluster->get("soton:workstationSeats")->toString() . " workstations available</td>");
		print("</tr>");
	}
	print("</table>");
	print("<table>");
	foreach($mfds as $mfd)
	{
		$room = "";
		foreach($mfd->all("sr:within") as $mfdloc)
		{
			if($mfdloc->isType("http://vocab.deri.ie/rooms#Room"))
			{
				$room = $mfdloc->prettyLink();
			}
		}
		print("<tr>");
		print("<td>" . $mfd->prettyLink() . "</td>");
		print("<td>" . $room . "</td>");
		print("</tr>");
	}
	print("</table>");
	$output = true;
}

if(!($output))
{
	print("<p>We're sorry, we don't have any information on services within this building. This may be because the building has no services, or because our data is incomplete.</p>");
}
