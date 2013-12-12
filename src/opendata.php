<?php

function getBlogEntries($endpoint)
{

	function date_sort($a, $b)
	{
		if($a['date'] < $b['date'])
		{
			return 1;
		}
		if($a['date'] > $b['date'])
		{
			return -1;
		}
		return 0;
	}

	$query = "SELECT ?title ?url ?date ?content WHERE {
		    ?url <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://rdfs.org/sioc/types#BlogPost> .
		    ?url <http://purl.org/dc/terms/created> ?date .
		    ?url <http://purl.org/dc/terms/title> ?title .
		    ?url <http://rdfs.org/sioc/ns#content> ?content .
		}";
	$result = sparql_get($endpoint, $query);

	$ret = array();
	foreach($result as $triple)
	{
		$item = array();
		$item['title'] = $triple['title'];
		$item['url'] = $triple['url'];
		$item['date'] = strtotime($triple['date']);
		$item['content'] = $triple['content'];
		$ret[] = $item;
	}

	usort($ret, 'date_sort');

	return($ret);
}

function getEntities($type, $endpoint)
{
	$graph = new Graphite();

	$graph->workAround4StoreBNodeBug = true;
	$graph->ns( "dct","http://purl.org/dc/terms/" );
	$type = $graph->resource($type);
	$rdesc = $type->prepareDescription();
	$rdesc->addRoute( "-rdf:type" );
	$rdesc->addRoute( "-rdf:type/*" );
	$rdesc->addRoute( "-rdf:type/*/rdf:label" );
	$rdesc->addRoute( "-rdf:type/*/foaf:name" );
	$rdesc->addRoute( "-rdf:type/*/dct:title" );
	$rdesc->addRoute( "-rdf:type/*/dc:title" );
	$rdesc->addRoute( "-rdf:type/*/rdfs:label" );
	$rdesc->addRoute( "-rdf:type/*/*/dct:title" );
	$n = $rdesc->loadSPARQL( $endpoint );
	if($n > 0)
	{
		return $rdesc->toGraph();
	}

	return($graph);
}
