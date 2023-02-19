<?php
$opts = array('http' => array('method' => 'GET', 'proxy' => 'tcp://www-cache:3128', 'request_fulluri' => true));
$context = stream_context_create($opts);

$res = file_get_contents('http://ip-api.com/xml/', false, $context);
$xml = simplexml_load_string($res);


$velos = file_get_contents('https://api.jcdecaux.com/vls/v3/stations?apiKey=frifk0jbxfefqqniqez09tw4jvk37wyf823b5j1i&contract=nancy', false, $context);
$jsonVelos = json_decode($velos);

$context = stream_context_create($opts);
$air = file_get_contents('https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&distance=0.0&units=esriSRUnit_Meter&returnGeodetic=false&outFields=*&returnGeometry=true&featureEncoding=esriDefault&multipatchOption=xyFootprint&maxAllowableOffset=&geometryPrecision=&outSR=&datumTransformation=&applyVCSProjection=false&returnIdsOnly=false&returnUniqueIdsOnly=false&returnCountOnly=false&returnExtentOnly=false&returnQueryGeometry=false&returnDistinctValues=false&cacheHint=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&having=&resultOffset=&resultRecordCount=&returnZ=false&returnM=false&returnExceededLimitFeatures=true&quantizationParameters=&sqlFormat=none&f=pjson&token=', false, $context);
$jsonAir = json_decode($air, true);

$qualite = $jsonAir["features"][0]["attributes"]["lib_qual"];
$couleur = $jsonAir["features"][0]["attributes"]["coul_qual"];


$markerList = '';
foreach ($jsonVelos as $key => $velo) {
    // echo jsonPrint($velo);
    $markerList .= "L.marker([{$velo->position->latitude},{$velo->position->longitude}]).addTo(map).bindPopup('Place disponible : {$velo->totalStands->availabilities->stands} <br> Vélos disponible : {$velo->totalStands->availabilities->bikes}')\n";
}

$lat = $xml->lat;
$lon = $xml->lon;
$city = $xml->city;

$coord = $lat . ',' . $lon;

$context = stream_context_create($opts);
$xsl = new DOMDocument();
$xsl->load('./xsl_xml/meteo/meteo.xsl');

$urlMeteo = 'http://www.infoclimat.fr/public-api/gfs/xml?_ll=' . $coord . '&_auth=VU9XQFUrVXdVeFRjBnABKFM7V2IOeFB3Uy9SMQ5rAn8Bal4%2FAGAGYAdpA35TfAI0V3oFZgoxADAEbwZ%2BCnhVNFU%2FVztVPlUyVTpUMQYpASpTfVc2Di5Qd1MxUjwOYAJ%2FAWdeOgB9BmUHawNkU30CNFdkBWAKKgAnBGYGZwpgVTJVNlc2VTRVNlU5VDcGKQEqU2ZXNA5iUGBTMFI3DmECYgFlXjIAZwZtB2EDZlN9Aj9XYAVnCjwAPARjBmcKZVUpVSlXSlVFVSpVelR0BmMBc1N9V2IOb1A8&_c=a04a6187f15f87439891f17702c39b9b';
$resMeteo = file_get_contents($urlMeteo, false, $context);
$xmlMeteo = simplexml_load_string($resMeteo);

$proc = new XSLTProcessor;
$proc->importStylesheet($xsl);
$html = $proc->transformToXml($xmlMeteo);


function generate($lat, $lon, $markerList, $html, $qualite, $couleur)
{
    $res = $html;

    $res = str_replace('</head>', '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
    <link rel="stylesheet" href="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
    </head>', $res);

    $res = str_replace('<body>', '<body onload="initialize()">', $res);
    $res = str_replace('QAIR', "<div class='item' style='color: $couleur'>$qualite<span class='title'>Qualité de l'air</span></div>", $res);

    $map = <<<EOT
    <h2 style="margin: 1rem; font-size: 2.5rem;">Vélos</h2>
    <div id="map"></div>
    <script type='text/javascript'>
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
    </body>
    EOT;

    $res = str_replace('</body>', $map, $res);


    return $res;
}


echo "
<div>
<a style='color: white !important;' href='http://ip-api.com/xml/'>lien ip</a>
<a style='color: white !important;' href='$urlMeteo'>lien météo</a>
<a style='color: white !important;' href='https://api.jcdecaux.com/vls/v3/stations?apiKey=frifk0jbxfefqqniqez09tw4jvk37wyf823b5j1i&contract=nancy'>lien vélos</a>
</div>";


if ($city !== 'Nancy') {
    echo generate(48.6822, 6.1862, $markerList, $html, $qualite, $couleur);
} else {
    echo generate($lat, $lon, $markerList, $html, $qualite, $couleur);
}
