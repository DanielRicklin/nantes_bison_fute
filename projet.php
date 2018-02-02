<?php

//LE PROXY
$opts = array('http' => array('proxy'=> 'tcp://www-cache.iutnc.univ-lorraine.fr:3128', 'request_fulluri'=> true));
$context = stream_context_create($opts);

//APIKEY
// AIzaSyBjxoI_CFVFGUQcDM6P9_8d5mq5-B3K3SQ 
$googleNantes = "https://maps.googleapis.com/maps/api/geocode/json?address=Nantes&key=AIzaSyBjxoI_CFVFGUQcDM6P9_8d5mq5-B3K3SQ";
$googleNantesJson = file_get_contents($googleNantes, NULL, $context);
$resp = json_decode($googleNantesJson, true);
if($resp['status']=='OK'){
	$latNantes = $resp['results'][0]['geometry']['location']['lat'];
	$lonNantes = $resp['results'][0]['geometry']['location']['lng'];
}

$url = "http://api.loire-atlantique.fr:80/opendata/1.0/traficevents?filter=Tous";

$content = file_get_contents($url, NULL, $context);
$data = json_decode($content);

$marker = '';
foreach($data as $key => $val){

	$long = $val->longitude;
	$lat = $val->latitude;

	$motif = "Motif: ".$val->ligne1;
	$date = "<br/>Durée: ".$val->ligne4;
	$nature = "<br/>Nature: ".$val->nature;
	$type = "<br/>Type: ".$val->type;
	$statut = "<br/>Statut: ".$val->statut;

	if($val->nature == 'Déviation'){
		$icon = "{icon: blueIcon}";
	}
	if($val->nature == 'Chantier'){
		$icon = "{icon: greenIcon}";
	}
	if($val->nature == 'Autre danger'){
		$icon = "{icon: redIcon}";
	}
$marker .= <<<END
L.marker([$lat, $long],$icon).addTo(mymap).bindPopup("$motif.$date.$nature.$type.$statut").openPopup();
END;
}



echo <<<END
<!DOCTYPE html>
	<html>
		<head>
			
			<title>Carte</title>

			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			
			<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />
		    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin=""/>
		    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin=""></script>

		</head>

		<body>

		<div id="mapid" style="width: 600px; height: 400px;"></div>
		<script>
			var greenIcon = new L.Icon({
				iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
				shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});
			var redIcon = new L.Icon({
				iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
				shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});
			var blueIcon = new L.Icon({
				iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
				shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			});

			var mymap = L.map('mapid').setView([$latNantes, $lonNantes], 13);

			L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
				maxZoom: 18,
				attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
					'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
					'Imagery © <a href="http://mapbox.com">Mapbox</a>',
				id: 'mapbox.streets'
			}).addTo(mymap);


			
			//L.marker([$latNantes, $lonNantes]).addTo(mymap).bindPopup("Saluuuuuuut").openPopup();

			$marker;

		</script>
		</body>
	</html>
END;




