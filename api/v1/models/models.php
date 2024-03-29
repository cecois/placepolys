<?php

	/*
	*  JSON
	*/
	function pg2JSON($pgresults, $fieldsToOutput,$sql)
	{ 
		$pg2jsonresult['results'] = array();
		$pg2json = array();
		if(isset($params['callback'])){
		$callback = $params['callback'];}

		while($row = pg_fetch_array($pgresults))
		{   
			for($i=0; $i<count($row); $i++)
			{
				unset($row[$i]);
			}
			// Add feature array to array
			array_push($pg2json, $row);
		}
		
		array_push($pg2jsonresult['results'],$pg2json);
	   
		header('Content-type: application/json',true);
		if(isset($callback)){
			echo $callback . '(' . json_encode($pg2jsonresult) . ')';
		} else {
		echo json_encode($pg2jsonresult);
	}
	}
	
	/*
	*  GEOJSON
	*/
	function pg2GEOJSON($pgresults, $fieldsToOutput,$sql)
	{
		// Return as GeoJSON
		$geojson = array('type' => 'FeatureCollection', 'features' => array());

		if(isset($params['callback'])){
		$callback = $params['callback'];}

		while($row = pg_fetch_array($pgresults))
		{
			$rowcount = count($row);
			$feature = array(
				'type' => 'Feature',
				'geometry' => json_decode($row['the_geom']),
				'crs' => array(
					'type' => 'EPSG',
					'properties' => array('code' => '4326')
				),
				'properties' => array(
					'id' => $row['id'],
					'exhib_name'=>$row['exhib_name'],
					// '2'=>$row[2],
					// '3'=>$row[3],
					// '4'=>$row[4],
					// '5'=>$row[5],
					// '6'=>$row[6],
				)
			);

			// Add feature array to feature collection array

			// this is a gross @returnto but it kills the geom from the row since we used it already
			// $filterOutKeys = array( 'the_geom', '17' );
			// $row = array_diff_key( $row, array_flip( $filterOutKeys ) );

			// array_push($feature['properties'],array_splice($row,0));
			array_push($geojson['features'], $feature);
			
		}
		header('Content-type: application/json',true);
		if(isset($callback)){

		echo $callback . '(' . json_encode($geojson) . ')';	 
		
		} else {return json_encode($geojson);}
	}
	
	/*
	*  KML
	*/
	function pg2KML($pgresults, $fieldsToOutput)
	{ 
		
		$kml = '<?xml version="1.0" encoding="UTF-8"?>
		<kml xmlns="http://www.opengis.net/kml/2.2"
		 xmlns:gx="http://www.google.com/kml/ext/2.2">
		<Document>';

		while($row = pg_fetch_array($pgresults))

		{
		  $tstamp=$row['exhib_year'];
		  $descrip=$row['exhib_name'];
		  html_entity_decode($descrip);
		  $rid=$row['id'];
		  $name=$row['exhib_name'];
		  html_entity_decode($name);
		  
			$kml .= '
			  <Placemark id="'.$rid.'">';

		$kml .= '<TimeStamp>
				  <when>'.$tstamp.'</when>
				</TimeStamp>';
					  
		$kml .= '<name><![CDATA['.$name.']]></name>
				<description><![CDATA['.$descrip.']]></description>';

		$kml .= $row['the_geom'];

			  $kml .= '</Placemark>';



		}
		$kml .= '</Document></kml>';
		// header('Content-type: application/vnd.google-earth.kml+xml',true);
		header('Content-type: application/xml',true);	 
		// header('Content-type: text/html',true);    
		return $kml;
	}
	
	/*
	* DEBUG
	*/
	function pg2DEBUG($pgresults, $fieldsToOutput, $sql){
$count = 0; 
echo $sql."<br/><br/>";
while($row = pg_fetch_array($pgresults))
		{
$count = $count+1;

   }
   echo "total records: ".$count;
}
?>