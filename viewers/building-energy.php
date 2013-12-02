<?php


if( $building->has( "soton:electricityTimeSeries" ) )
{
	$weekago = (time()-24*60*60*7)."000.0"; # this week
	$monthago = (time()-24*60*60*28)."000.0"; # this month
	#$hourago = (time()-1*60*60)."000.0"; # ignore that icky dip on half hours
	print "<h2>Electricity usage in kW</h2>";
	print "<iframe style='border:solid 1px black; width:98%; height:300px' src='http://data.southampton.ac.uk/time-series?action=fetch&series=".$building->getString( "soton:electricityTimeSeries" )."&type=average&format=graph&resolution=3600&startTime=$weekago'></iframe>";
	print "<div style='text-align:left'><a href='http://data.southampton.ac.uk/time-series?action=fetch&series=".$building->getString( "soton:electricityTimeSeries" )."&type=average&format=html&resolution=3600&startTime=$weekago'>Get this data</a> ";
	print " | <a href='http://data.southampton.ac.uk/time-series?action=fetch&series=".$building->getString( "soton:electricityTimeSeries" )."&type=average&format=graph&resolution=3600&startTime=$monthago'>Graph for past month</a>";
	print " | <a href='http://data.southampton.ac.uk/time-series?action=fetch&series=".$building->getString( "soton:electricityTimeSeries" )."&type=average&format=graph&resolution=3600&startTime=0'>Graph for all time</a> (note, there are gaps in the readings)";
	print "</div>";
	print "<p>Click and drag graph to zoom. Double click to zoom back out.</p>";
}

else
{
	print("<p>We're sorry, but we don't have any energy usage data for this building at the present time.</p>");
}
