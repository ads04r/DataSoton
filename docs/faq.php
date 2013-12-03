<?php

$faq = array();

$item = array();
$item['q'] = "What formats are you publishing in?";
$item['a'] = "Where possible we aim to provide the data as full <a href=\"/5star/\">&#9733;&#9733;&#9733;&#9733;&#9733; data</a>. However, that may be a long process, and we would rather make good data available now, than perfect data the day after tomorrow. Most datasets are available as RDF+XML and Turtle and, where possible, we also provide the raw data which is almost always one or more comma-separated value files or Excel documents.";
$faq[] = $item;

$item = array();
$item['q'] = "What does \"beta\" mean?";
$item['a'] = "By \"beta\" we mean that this service is in active development. We are learning as we go so it is subject to having bits added and changed. That's not a good way to build a suspension bridge, but it is very cost-effective and practical for web development. When we started this site we had a brief, rather than a detailed set of requirements.";
$faq[] = $item;

$item = array();
$item['q'] = "What technologies are you using?";
$item['a'] = "We use <a href='https://github.com/cgutteridge/Grinder'>Grinder</a>, <a href='http://librdf.org/raptor/rapper.html'>rapper</a>, and some simple scripts to publish the datasets. Grinder was developed for this project, but is available as a simple way to convert tabular data into RDF. We use <a href=\"http://4store.org/\">4store</a> as our data store and SPARQL endpoint, with a <a href='https://github.com/semsol/arc2'>arc2</a> front page to make it easier to use. To provice the interface we use PHP and our <a href=\"http://graphite.ecs.soton.ac.uk/\">Graphite</a> and <a href=\"http://graphite.ecs.soton.ac.uk/sparqllib/\">sparqllib</a> libraries, plus the Google Maps API.";
$faq[] = $item;

$item = array();
$item['q'] = "Are you the only university to do this?";
$item['a'] = "No, and it would be pointless if we were. We are working with our peers at <a href=\"http://data-ac-uk.ecs.soton.ac.uk/\">other universities planning and providing open data</a> to design good practice and tools. However Southampton is a pioneer in the field, leading in <a href=\"http://eprints.org/openaccess/\">Open Access to Research</a> and <a href=\"http://www.ecs.soton.ac.uk/\">Electronics and Computer Science</a> have been publishing open data about their infrastructure since 2006. This site is built by the same team, but with the experience of the lessons learned from the ECS project.";
$faq[] = $item;

$item = array();
$item['q'] = "What license will you publish under?";
$item['a'] = "Datasets will mostly be published under <a href=\"http://www.nationalarchives.gov.uk/doc/open-government-licence/\">Open Government License</a>, or other licenses conforming to the <a href=\"http://www.opendefinition.org/\">Open Definition</a>. In some cases this may not be possible.";
$faq[] = $item;

$item = array();
$item['q'] = "Aren't you worried about the legal and social risks of publishing your data?";
$item['a'] = "No, we are not worried. We will consider carefully the implications of what we are publishing and manage our risk accordingly. We have no intention of breaking the UK Data Protection Act or other laws. Much of what we publish is going to be data which was already available to the public, but just not as machine-readable data. There are risks involved, but as a university -- it's our role to try exciting new things!";
$faq[] = $item;

$item = array();
$item['q'] = "What is a URI?";
$item['a'] = "First of all, note that it looks very like \"URL\". URLs are a subset of URIs. A URI identifies a single concept, but unlike a URL that concept is not limited to a document or file on the Web. It still <i>looks</i> like a web address, but don't let that confuse you. It can identify things which are not possible to turn to a series of ones and zeroes and send over the Internet. For example, Bencraft Hall Bar (http://id.southampton.ac.uk/building/81D), the Genus Velociraptor (http://www.bbc.co.uk/nature/genus/Velociraptor) and so forth. You may wish to cut-and-paste some URIs into your data, if so, just click the <span style='color:#999;font-style:italic'>grey, italic URI</span> and it will by auto-selected it for you, for convenient cut-and-pasting. Most University of Southampton URIs will start with \"http://id.southampton.ac.uk/\".";
$faq[] = $item;

$item = array();
$item['q'] = "What file formats are used on this site?";
$item['a'] = "Many of the HTML pages are constructed from RDF data. If the page was constructed from triples, the \"get the data\" box will contain links to other alternate formats. The formats RDF+XML (.rdf), Turtle (.ttl) and N-Triples (.nt) all express exactly the same data. For a programer new to RDF, \"N-Triples\" is the easiest to get to grips with as it's just the raw data. In time we hope to add some additional formats to some pages, specifically KML, which is a format used by Google to describe shapes and points on maps, and ICS which is a standard format for expressing calendar events. For now you can try converting the RDF to KML using our <a href=\"http://graphite.ecs.soton.ac.uk/geo2kml/\">GEo 2 KML</a> tool, which is how we currently generate the maps on some pages.";
$faq[] = $item;

print("<h1>Frequently Asked Questions</h1>");
print("<p>If you have a question not in the list, please <a href='mailto:ads04r@ecs.soton.ac.uk'>get in touch</a>.</p>");
if(count($faq) == 0)
{
	exit();
}

print("<dl>");
foreach($faq as $item)
{
	if(!((array_key_exists("q", $item)) & (array_key_exists("q", $item))))
	{
		continue;
	}
	print("<dt>" . $item['q'] . "</dt>");
	print("<dd>" . $item['a'] . "</dd>");
}
print("</dl>");
