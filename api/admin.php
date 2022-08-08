<?php
$path = $_SERVER['PHP_SELF'];
$path = substr($path, 7);
$suffix = explode(".", $path);
$suffix = $suffix[count($suffix) - 1];
require_once(__DIR__ . "/../admin/$path");
?>