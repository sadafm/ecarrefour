<?php
	$result = [];
	foreach($cities as $row){

	array_push($result, $row['city']);

	}

	echo json_encode($result);

?>