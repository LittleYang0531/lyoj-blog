<?php
$path = $_SERVER['PHP_SELF'];
$path = substr($path, 7);
$suffix = explode(".", $path);
$suffix = $suffix[count($suffix) - 1];
if ($suffix == "php") require_once(__DIR__ . "/../admin/$path");
else {
    header("Content-Type: ".mime_content_type($path));
    header("Accept-Ranges: bytes");
    $fp = fopen(__DIR__ . "/../admin/$path", "r");
    $cont = fread($fp, filesize(__DIR__ . "/../admin/$path"));
    fclose($fp); echo $cont;
}
?>