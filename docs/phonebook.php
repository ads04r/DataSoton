<?php

if(strcmp($params['format'], "rdf") == 0)
{
	$f3->error(404);
	exit();
}

print("<h2>Public Phonebook</h2>");

print("<p>This phonebook data is taken periodically from the list of staff who have opted to appear in our public phone directory. ");
print("Staff may update their information, and change their preference via the <a href=\"https://subscribe.iss.soton.ac.uk/subscribe.html\">iSolutions Subscribe</a> site. ");
print("Changes may take days to appear here as updates to this site do not yet happen automatically.</p>");

print("<iframe style='border:0px; width:98%; height:300px' src=\"/phonebook_search/phonebook_search.html\"></iframe>");

