<?php
require __DIR__ . '/vendor/autoload.php';

use function Jawira\PlantUml\encodep;

$plantUML = $_GET["plantUML"];
$format = $_GET["format"];

$encode = encodep($plantUML);
$umlURL = "http://www.plantuml.com/plantuml/" . $format . "/" . $encode;

match ($format) {
    "png" => header('Content-Type: image/png'),
    "svg" => header('Content-Type: text/html'),
    "txt" => header('Content-Type: text/html')
};
$content = @file_get_contents($umlURL);
if ($content === false) {
    echo http_response_code(400);
} else {
    echo $content;
}
