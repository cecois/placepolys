<?php

require 'Slim/Slim.php';

$app = new Slim();

$currentVersion = 'v1';

function debugMe(){
// header("Content-Type: application/xml");
// $app->response()->header('Content-Type', 'application/xml');
echo "You're using version ".$version;
}

function versionMe($version){

			require_once $version.'/routes/routes.php';
			require_once $version.'/lib/db.php';

}

 // Routes
$app->get('/:version/placenames/:howmany/:outform/:spatial','versionMe','getPlaces');
$app->get('/:version/test','debugMe');
 
//GET route
$app->get('/', 'debugMe');
$app->run();