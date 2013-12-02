<?php

if(strcmp($params['format'], "rdf") == 0)
{
	$f3->error(404);
	exit();
}

function render_org_list( $id, $tree, $maxdepth=1000, $depth=0, $showcodes=false )
{
	if( $depth == $maxdepth ) { return; }
	if( !isset( $tree[$id] ) || sizeof($tree[$id]) == 0 ) { return; }
	print "<ul class='org-tree-$depth' style='margin-bottom:0.5em'>";
	foreach( $tree[$id] as $item )
	{
		print "<li>";
		print "<a href='".$item['org']."'>".$item['label']."</a>";
		if( $showcodes && isset( $item["finance_code"] ) ) 
		{ 
			print " - <span title='2 Character Code'>".$item['finance_code']."</span> "; 
		}
		if( $showcodes && isset( $item["hr_code"] ) ) 
		{ 
			print " - <span title='10 Character Code'>".$item['hr_code']."</span> "; 
		}
		render_org_list( $item['org'], $tree, $maxdepth, $depth+1, $showcodes );        
	}
	print "</ul>";
}

$query = $_SERVER['QUERY_STRING'];
$tabs = array(
	array("id"=>"", "text"=>"Top Levels"),
	array("id"=>"full", "text"=>"All Levels"),
	array("id"=>"codes", "text"=>"Codes")
);

$maxdepth = 3;
$showcodes = false;
if( isset( $query ) && $query=='full' ) { $maxdepth = 1000; }
if( isset( $query ) && $query=='codes' ) { $maxdepth = 1000; $showcodes = true; }

$db = sparql_connect( "http://edward.ecs.soton.ac.uk:8002/sparql/" );
if( !$db ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$db->ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#" );
$db->ns( "soton","http://id.southampton.ac.uk/ns/" );
$db->ns( "org","http://www.w3.org/ns/org#" );

$sparql = "
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX soton: <http://id.southampton.ac.uk/ns/>
PREFIX org: <http://www.w3.org/ns/org#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT * WHERE {
GRAPH <http://id.southampton.ac.uk/dataset/org/latest> 
{
	?org rdfs:label ?label .
	?porg org:hasSubOrganization ?org .
	OPTIONAL { 
	?org skos:notation ?hr_code .
	FILTER ( datatype(?hr_code) = soton:10CharHRCode ) 
	}
	OPTIONAL { 
	?org skos:notation ?finance_code .
	FILTER ( datatype(?finance_code) = soton:alphaCode ) 
	}
	OPTIONAL { ?org foaf:homepage ?homepage }
}
}
ORDER BY ?porg ?org
";
$result = $db->query( $sparql, "All Organisational Components" );
if( !$result ) { print $db->errno() . ": " . $db->error(). "\n"; exit; }
$data = $result->fetch_all();

$tree = array();
foreach( $data as $row )
{
	$tree[$row['porg']][] = $row;   
}

print("<h2>Organisation</h2>");

print("<p>This page describes the organisation of the University of Southampton, and lists the codes that HR and Finance use to identify them. Our organisation is also available in ");
print("machine-readable RDF, in the form of the <a href=\"http://id.southampton.ac.uk/dataset/org\">Organisation Dataset</a>.</p>");

print("<p>There are two code schemes used to identify the parts of the organisation. ");
print("Two-character codes which identify the higher levels of the organisation (defined by the finance department) and ");
print("ten-character codes which identify the whole formal structure of the organisation (defined by the HR department).</p>");

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

print("<div class=\"datasotonacuk_orgchart\">");
render_org_list( "http://id.southampton.ac.uk/", $tree, $maxdepth, 1, $showcodes );
print("</div>");
