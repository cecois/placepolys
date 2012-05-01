<?php
function buildWhere($paramarray) {
    $whereArr = array();
    // to prevent injection during what we'll do next, we need to limit valid keys
// #apidoc
    $acceptedIncomingVars = array('bbox');

    // loop through request as array
    foreach ($paramarray as $key => $value) {
        // lowercase it
        $key = strtolower($key);
        // if it's acceptable
        if (in_array($key, $acceptedIncomingVars)) {
            // allow us to reference it by the key
            $$key = $value;
        }
    }


    if (isset($bbox)){
    $withinpoly = wktFromEscaped($bbox);
    $withinclause = "ST_Intersects(st_geomfromtext(the_geom,4326), ".$withinpoly.")";
    array_push($whereArr,$withinclause);
    }
    
    return concatWheres($whereArr);
    // return concatWhere($whereArr);
} // end buildWhere

function wktFromEscaped($wktparam){
$wktraw = urldecode($wktparam);

$wktguts = wktMyBbox($wktraw);
//only poly for now
//and only 4326
$wkt = "ST_GeomFromText('POLYGON((".$wktguts."))', 4326)";
return $wkt;
}

function concatWheres($whereArr) {
    $where = 'where ';
    $whereA = array();
    foreach ($whereArr as $whereAr) {
        array_push($whereA, " (" . $whereAr . ")");
        // $whereA .= " (".$whereAr.")";
        
    }
    // now let's and it all together
    $numItems = count($whereA);
    $i = 0;
    foreach ($whereA as $value) {
        if ($i + 1 == $numItems) {
            $where.= $value;
        } else {
            $where.= $value . " AND ";
        }
        $i++;
    }
    return $where;
}

function wktMyBbox($bbox){
$z = array();
$a = explode(" ", $bbox);
$w=$a[0];
$s=$a[1];
$e=$a[2];
$n=$a[3];


array_push($z, $w." ".$s);
array_push($z, $e." ".$s);
array_push($z, $e." ".$n);
array_push($z, $w." ".$n);
array_push($z, $w." ".$s);
$wktified = implode(",", $z);
return $wktified;

    
}