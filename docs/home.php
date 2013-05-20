<?php

include_once("src/opendata.php");
$blog = getBlogEntries($f3->get('sparql_endpoint'));

?>

<p>The University of Southampton provides open access to some of our administrative data.</p>

<p>We believe that this will be of benefit to our own members and visitors, and increase the transparency of our operations.</p>

<div class="datasotonacuk_rightsidebar">
<h3>Featured App</h3>
<h4><a href='http://data.southampton.ac.uk/room-finder/'>Room Finder</a></h4><div>We recently published timetable data for every bookable room in the University. As an example of a tool which uses this data, as well as a great example of the power of open data, we produced the Room Finder, which allows anyone to search for a room based on both availability and room features.</div>
<h3>Open Data Blog</h3>

<? $i = 0; foreach($blog as $entry) { ?>

<h4><a href="<? print($entry['url']); ?>"><? print($entry['title']); ?></a></h4>

<? $i++; if($i >= 5) { break; }} ?>

</div>

<p>The executive summary: There's data we have which isn't in any way confidential which is of use to our members, visitors, and the public. If we make the data available in a structured way with a
<a href='http://www.nationalarchives.gov.uk/doc/open-government-licence/'>license which allows reuse</a> 
then our members, or anyone else, can build tools on top of it without needless bureaucracy. That's common sense. 
We call it "Open Data".</p>

<p>For more on Open Data and it's benefits see 
<a href="http://data.gov.uk/resources">these presentations</a>
 by Southampton's Nigel Shadbolt and Tim Berners-Lee. They helped establish <a href="http://data.gov.uk">data.gov.uk</a> the UK Government's Open Data site and are members of the Coalition Government's Transparency Board.
</p>

<p>We publish our data in RDF format and link our identifiers to <a href='http://richard.cyganiak.de/2007/10/lod/'>other sites in the Linked Open Data Web</a>. This makes it much easier to merge data from multiple sources and other sites can link their datasets up with ours. Like the HTML Web, the whole is much greater than the sum of its parts, that's "Linked Data".</p>



<h2>Show me the data!</h2>

<p>Browse the <a href='/datasets.html'>list of datasets</a> or view the links on the left to explore some of our data.</p>

<h2>Keep Informed</h2>

<p><a href="https://www.facebook.com/UniversityOfSouthamptonOpenData"><img width="144" style='padding: 0px 0px 5px 10px; ' height="44" src="/images/facebook_button.png" align="right" border="0"></a> You can chat about the service and ideas for the future on our facebook page.</p>

<h2 style='clear:right'>Awards</h2>

<a href='http://www.the-awards.co.uk/the2012/winners'><img src='/resources/images/ICT_Initiative_small.png' /></a>
<h2>Credits</h2>

<p>The site and data are developed and maintained by <a href='http://id.ecs.soton.ac.uk/person/6802'>Ash Smith</a>, the University of Southampton's Linked &amp; Open Data Management Specialist ('LODmaster').</p>
<p>The initial version of this site and data by <a href='http://id.ecs.soton.ac.uk/person/1248'>Christopher Gutteridge</a>, the University of Southampton Linked Open Data Architect.</p>
<p>The SPARQL component was developed by Dave Challis.</p>
<p>The University of Southampton's Open Data Champion is <a href='http://id.ecs.soton.ac.uk/person/2686'>Nigel Shadbolt</a>.</p>
<p>The various teams around the University have been massively helpful and patient. Without their data this site would be an empty box! </p>
