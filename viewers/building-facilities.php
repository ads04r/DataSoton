<?php

########## Facilities

$facs = array();
foreach( $building->all( "-sr:within" )->sort( "rdfs:label" ) as $inner_thing )
{
	if( $inner_thing->isType( "oo:Facility" ) ) { $facs[]=$inner_thing; }
}
if( sizeof( $facs ) )
{
	print "<h2>Research Facilities</h2>";
	print "<table>";
	foreach( $facs as $facility )
	{
		print "<tr><td>".$facility->prettyLink()."</td></tr>";
	}
	print "</table>";
}


