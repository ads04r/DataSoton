<?php

include_once("src/opendata.php");
$type = "http://www.w3.org/2004/02/skos/core#Concept";
$jargon = getEntities($type, $f3->get('sparql_endpoint'));

if(strcmp($params['format'], "rdf") == 0)
{
        header("Content-type: application/rdf+xml");
        print($jargon->serialize("RDFXML"));
        exit();
}
if(strcmp($params['format'], "ttl") == 0)
{
        header("Content-type: text/turtle");
        print($jargon->serialize("Turtle"));
        exit();
}
if(strcmp($params['format'], "nt") == 0)
{
        header("Content-type: text/plain");
        print($jargon->serialize("NTriples"));
        exit();
}

print("<h2>Southampton Jargon</h2>\n");
print("<p>We use lots of acronyms in emails and meetings and they can get confusing. Use the jargon dictionary to help decode your colleagues!</p>\n");

print("<h3>Bookmarklette</h3>\n");
print("<p>Drag the following link to your bookmark bar and then use it to decode jargon in any webpage.</p>\n");
print("<a style='border: 1px solid black;padding:5px;background-color:#eee ' class='rounded' href=\"javascript:var%20i,s,ss=['http://data.southampton.ac.uk/jargon/inpage.js','http://data.southampton.ac.uk/jargon/data.js'];for(i=0;i!=ss.length;i++){s=document.createElement('script');s.src=ss[i];document.body.appendChild(s);}void(0);\">Jargon</a>\n");
print("<p>You can then try it out on the <a href='http://www.southampton.ac.uk/isolutions/'>iSolutions Homepage</a>. Select the bookmark while on a page with jargon in, and it should be expanded and highlighted in yellow. Hover over it with the mouse to see a full description.</p>\n");

print("<h3>Become an Editor</h3>\n");
print("<p>Any member of university staff is welcome to become a contributing editor. If you would like to help, you will need a valid <a href='http://docs.google.com/'>Google Docs</a> account. Send a message <i>from your university email</i> to <a href=\"mailto:cjg@ecs.soton.ac.uk\">cjg@ecs.soton.ac.uk</a> saying you would like to become an editor of our jargon file, and include your google email address in the body of the message.</p>\n");

print("<h3>Linking to this page</h3>\n");
print("<p>If you want to, you can link to a definition using a # and the term, for example:\n");
print("<a href='http://data.southampton.ac.uk/jargon.html#SOWN'>http://data.southampton.ac.uk/jargon.html#SOWN</a>\n");

print("<h3>The Dictionary</h3>\n");
print("<p>The terms are not always the official or correct terms, but rather those that are actually used.</p>\n");

