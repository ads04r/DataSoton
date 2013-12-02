<?php

$path = ltrim($_SERVER['REQUEST_URI'], "/");
$url = "http://bus.southampton.ac.uk/" . $path;
header("Location: " . $url, true, 301);
exit();
