<?php 
	use common\components\SiteHelper;

	$data = file_get_contents('zones__addresses.csv');
	$data = explode("\n", $data);
	$coords = array();
	foreach ($data as $key => $value) {
		$temp = explode(";", $value);
		$coords[$temp[0]] = explode(',', str_replace('"', '', $temp[1]));
	}

	echo SiteHelper::dataFromPHPtoJS('coords', $coords);
?>
<style type="text/css">
	#map{
		height: 500px;
	}
</style>
<div id="map"></div>
<button id="load">Load</button>
<button id="parse">Parse</button>