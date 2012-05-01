<?php
require_once 'v1/lib/db.php';
	/*
	*  Get Places
	*/
	function getPlaces($version,$howmany,$outform)
	{

		include_once 'lib/wherebuilder.php';
		
		$sql = "select ocurl,geonamesho,geonamelon,hash ";


		switch ($outform) {
			case 'geojson':
				// $the_geom = 'st_asgeojson(st_transform(the_geom,4326)) the_geom';
				break;

			case 'json':
				// $the_geom = 'ST_AsEWKT(st_transform(the_geom,4326)) the_geom';
				break;	

			case 'debug':
				// $the_geom = 'st_askml(st_transform(the_geom,4326)) the_geom';
				break;	

			default:
				// $the_geom = 'st_astext(st_transform(the_geom,4326)) the_geom';
				break;
		}

		$fieldsToOutput = array("ocurl", "geonamesho","geonamelon","hash");

		

		$sql .= implode(',',$fieldsToOutput);
	
		$sql .= "  from oc_geo oc left join geogeo ge on oc.hash=ge.ochash";

		

		$params = $_REQUEST;
		if(count($params) > 0)
		{
			
			$whereclause = buildWhere($params);
			$sql.= ' ' . $whereclause;
			
		}

		switch ($howmany){
// #apidoc			
			case 'all':

			$sql .= " group by ocurl,geonamesho,geonamelon,oc.hash";
			break;


			case 'some':

			$sql .= " AND (the_geom IS NOT NULL AND ge.ochash IS NULL) group by ocurl,geonamesho,geonamelon,oc.hash";
		}
		echo $sql;die();
		produceOutput($outform, $fieldsToOutput, $sql, $params);
	}
	
	
	
	
	function produceOutput($outform, $fieldsToOutput, $sql, $params)
	{
		// Global app variable to set a proper header for actual kml clients later
		global $app;
		
		// Global database variable
		global $db;
		
		// Include models
		include_once 'models/models.php';


		// Try-catch block to catch any wayward exceptions
		try
		{	
			// Set the output parser
			$outparser = 'pg2' . strtoupper($outform);

			// Execute SQL query
			$pgresults = pg_query($db, $sql);

			// Close the connection
			$db = null;
			$result['data'] = $pgresults;
			$result['success'] = true;
		}
		catch(Exception $e)
		{
			// Catch any exceptions and report the problem
			$result = array();
			$result['success'] = false;
			$result['errormsg'] = $e->getMessage();
		}
		
		// Format the output
		echo $outparser($result['data'], $fieldsToOutput,$sql,$params);
		exit();
	}	
?>