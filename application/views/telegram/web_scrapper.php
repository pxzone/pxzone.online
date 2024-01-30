<?php

// Target URL
$url = 'https://bitcointalk.org/index.php?topic=5480300.msg63438405#new';

// Get the HTML content of the page
$html = file_get_contents($url);

// Check if content was retrieved successfully
if ($html === false) {
    die('Unable to retrieve the content from the URL.');
}

// Create a DOMDocument object
$dom = new DOMDocument();

// Suppress errors related to poorly formatted HTML
libxml_use_internal_errors(true);

// Load the HTML into the DOMDocument
$dom->loadHTML($html);

// Restore error handling
libxml_use_internal_errors(false);

// Use DOMXPath to query the DOMDocument
$xpath = new DOMXPath($dom);

// Example: Extract all the anchor (a) tags
$anchors = $xpath->query('//div[@class="post"]');

// Output the href attribute of each anchor tag
foreach ($anchors as $anchor) {
    echo $anchor->getAttribute('href') . PHP_EOL;
}

?>
