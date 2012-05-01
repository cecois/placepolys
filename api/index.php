<?php

require 'Slim/Slim.php';

$app = new Slim();
require_once 'v1/lib/db.php';

$currentVersion = 'v1';

function debugMe($version){
global $db;			
print_r($db);
die();

// header("Content-Type: application/xml");
// $app->response()->header('Content-Type', 'application/xml');
echo "You're using version: ".$version;

// Include models
include_once 'v1/models/models.php';

$sql = "SELECT ocurl FROM oc_geo oc limit 5;";

$pgresults = pg_query($db, $sql);

$outparser = 'pg2DEBUG';
echo $outparser($pgresults, $fieldsToOutput,$sql,$params);

}

function versionMe($version){

	switch ($version) {
		case 'v1':
			require_once 'v1/routes.php';
			break;
		
		default:
			require_once 'v1/routes.php';
			break;
	}	

}

 // Routes
$app->get('/:version/placenames/:howmany/:outform','versionMe','getPlaces');
$app->get('/:version/test','versionMe','debugMe');
 
//GET route
$app->get('/', 'debugMe');
$app->run();