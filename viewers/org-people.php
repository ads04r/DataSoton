<?php

$i = 0;
print("<h2>People</h2>");
print("<table>");
foreach($res->all("foaf:member")->sort("foaf:givenName")->sort("foaf:familyName") as $member)
{
	if(!($member->isType("foaf:Person")))
	{
		continue;
	}
	print("<tr>");
	print("<td>" . $member->prettyLink() . "</td>");
	if($member->has("foaf:mbox"))
	{
		print("<td>" . $member->get("foaf:mbox")->prettyLink() . "</td>");
	}
	print("</tr>");
	$i++;
}
print("</table>");
