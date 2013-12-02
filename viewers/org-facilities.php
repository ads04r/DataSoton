<?php

print("<h2>Research Facilities</h2>");

$i = 0;
print("<table>");
foreach($res->all("-oo:organizationPart") as $fac)
{
	if(!($fac->isType("oo:Facility")))
	{
		continue;
	}
	print("<tr>");
	print("<td>" . $fac->prettyLink() . "</td>");
	if($fac->has("oo:contact"))
	{
		print("<td>" . $fac->get("oo:contact")->prettyLink() . "</td>");
	}
	print("</tr>");
	$i++;
}
print("</table>");

if($i == 0)
{
	print("<p>We're sorry, we don't have any information on facilities managed by this organisational unit. This may be because the unit has no facilities, or because our data is incomplete.</p>");
}
