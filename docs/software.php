<h2>Our Software</h2>
<p>
	Our service is cutting edge. There are many challenges in running it, not least that no
	third party software exists to do the sort of things we want! For this reason we have
	written our own, and, wherever possible, we make it available as open source in the
	hope that we can benefit the greater Open Data community and assist others in setting
	up their own Open Data services without having to re-live all the problems we've already
	solved.
</p>

<h3>Publishing Pipeline</h3>
<p>
	Our data is republished at various intervals throughout the week using automatic scripts.
	The software that runs and manages these scripts is known as <em>Hedgehog</em>. Each dataset contains
	a 'hopper' directory in which the collector script runs, and Hedgehog manages the downloading
	of remote files, the converting of these files into linked data, as well as publishing them
	to the triplestore and the website and generating metadata and provenance data. It uses
	several supplimentary tools, the most prominent of these is <em>Grinder</em>, a tool for generating
	XML from other data formats and applying stylesheets to it (for converting to RDF/XML, for
	example). We also have custom tools for performing repetitive tasks such as converting
	spreadsheet file formats and connecting to databases.
</p>
<p>
	Another essential requirement in the management of this website is the ability to convert
	between different RDF formats, and reason on linked data. For this we use <em>Graphite</em>, a
	PHP library that simplifies the management of linked data, and potentially allows a
	developer to call RDF from a triplestore without having to write a line of SPARQL. It's
	designed to be similar to JQuery, and is based on ARC2. For when we need to delve in and
	read the data directly, we have <em>PHP-SPARQL-Lib</em>.
</p>

<h3>External Links</h3>
<p>
	We use Github to manage our development. You can download - and even contribute to - our
	software at the following links.
</p>

<h4>Hedgehog</h4>
<p><a href="https://github.com/ads04r/Hedgehog">https://github.com/ads04r/Hedgehog</a></p>

<h4>Grinder</h4>
<p><a href="https://github.com/cgutteridge/Grinder">https://github.com/cgutteridge/Grinder</a></p>

<h4>Graphite</h4>
<p><a href="https://github.com/cgutteridge/Graphite">https://github.com/cgutteridge/Graphite</a></p>

<h4>PHP-SPARQL-Lib</h4>
<p><a href="https://github.com/cgutteridge/PHP-SPARQL-Lib">https://github.com/cgutteridge/PHP-SPARQL-Lib</a></p>

<p>
	There are other tools that make our lives easier. <em>SharePerltopus</em> is a tool for accessing
	Microsoft Sharepoint from Perl. Our friends at the Open Data service of the University
	of Oxford have a <a href="https://github.com/ox-it/python-sharepoint/">similar tool</a>
	written in Python. <em>TripleChecker</em> is a tool for checking for typos and common
	mistakes in RDF documents. We anticipate this functionality will eventually be built into
	Hedgehog.
</p>

<h4>SharePerltopus</h4>
<p><a href="https://github.com/cgutteridge/SharePerltopus">https://github.com/cgutteridge/SharePerltopus</a></p>

<h4>TripleChecker</h4>
<p><a href="https://github.com/cgutteridge/TripleChecker">https://github.com/cgutteridge/TripleChecker</a></p>

<p>Finally, the source code for this site, as well a few others based on it, is available on Github.</p>

<h4>data.southampton.ac.uk</h4>
<p><a href="https://github.com/ads04r/DataSoton">https://github.com/ads04r/DataSoton</a></p>

<h4>data.susu.org</h4>
<p><a href="https://github.com/ads04r/data-susu">https://github.com/ads04r/data-susu</a></p>

