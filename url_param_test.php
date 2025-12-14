<?php
session_start();

echo "<h1>URL Parameter Test</h1>";

echo "<h2>Current URL:</h2>";
echo "<p>" . $_SERVER['REQUEST_URI'] . "</p>";

echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Parsed URL:</h2>";
$urlParts = parse_url($_SERVER['REQUEST_URI']);
echo "<pre>";
print_r($urlParts);
echo "</pre>";

if (isset($urlParts['query'])) {
    echo "<h2>Query Parameters:</h2>";
    $queryParams = [];
    parse_str($urlParts['query'], $queryParams);
    echo "<pre>";
    print_r($queryParams);
    echo "</pre>";
}

echo "<h2>Test Links:</h2>";
echo "<p><a href='?lang=fr'>Set Language to French</a></p>";
echo "<p><a href='?lang=en'>Set Language to English</a></p>";
echo "<p><a href='?theme=light'>Set Theme to Light</a></p>";
echo "<p><a href='?theme=dark'>Set Theme to Dark</a></p>";
echo "<p><a href='?lang=fr&theme=light'>Set Language to French and Theme to Light</a></p>";
echo "<p><a href='?lang=en&theme=dark'>Set Language to English and Theme to Dark</a></p>";
echo "<p><a href='?'>Clear Parameters</a></p>";
?>