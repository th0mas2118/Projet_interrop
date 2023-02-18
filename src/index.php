<?php
$opts = array('http' => array('method' => 'GET', 'proxy' => 'tcp://www-cache:3128', 'request_fulluri' => true));
$context = stream_context_create($opts);

$res = file_get_contents('http://ip-api.com/xml/', false, $context);
$xml = simplexml_load_string($res);


$velos = file_get_contents('https://api.jcdecaux.com/vls/v3/stations?apiKey=frifk0jbxfefqqniqez09tw4jvk37wyf823b5j1i&contract=nancy', false, $context);
$jsonVelos = json_decode($velos);

// $air = file_get_contents('http://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=1%3D1&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token=', false, $context);
// $jsonAir = json_decode($air);
//ERROR TIME OUT A REVOIR
// echo $jsonAir->error->code;

$markerList = '';
foreach ($jsonVelos as $key => $velo) {
    // echo jsonPrint($velo);
    $markerList .= "L.marker([{$velo->position->latitude},{$velo->position->longitude}]).addTo(map).bindPopup('Place disponible : {$velo->totalStands->availabilities->stands} <br> Vélos disponible : {$velo->totalStands->availabilities->bikes}')\n";
}

$lat = $xml->lat;
$lon = $xml->lon;
$city = $xml->city;

function generate($lat, $lon, $markerList)
{

    $res = <<<EOT
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
                <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
            </head>
            <style>
                html, body { height: 100% }
                #map{ width:50em;}
            </style>
        
            <body onload="initialize()">
                <div id="map" style="width:100%; height:100%"></div>
            </body>
        </html>
        <script type="text/javascript">
        var mapOptions = {
            center: [$lat,$lon],
            zoom: 14,
          }
          var map = new L.map('map', mapOptions)
          var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
          map.addLayer(layer)
          var clientIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
          })
          L.marker([$lat, $lon], { icon: clientIcon })
            .addTo(map)
            .bindPopup('Vous êtes ici')\n
          {$markerList}
        </script>
        EOT;
    return $res;
}

if ($city !== 'Nancy') {
    echo generate(48.6822, 6.1862, $markerList);
} else {
    echo generate($lat, $lon, $markerList);
}
