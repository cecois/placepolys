<?php
	try 
	{
		$connection_string = "host= dbname= user= password=";
		$db = pg_connect($connection_string);
	}
	catch(PDOException $err)
	{
		phpinfo();
	}

?>