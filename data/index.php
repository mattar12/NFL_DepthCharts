<?php
require('../vendor/autoload.php');
require_once __DIR__."/../lib/Dropbox/strict.php";
$json = file_get_contents('http://riofin.com/nfl/test/skim.php');
$data = json_decode($json);

$teams = $data->Teams;
$positions = $data->Positions;
$depthCharts = $data->DepthCharts;
echo "<hr>Arrays: <br>";
echo "<pre>";
print_r($positions);
echo "</pre>";

echo "<pre>";
print_r($teams);
echo "</pre>";

