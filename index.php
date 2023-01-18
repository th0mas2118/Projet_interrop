<?php
$url = 'http://ip-api.com/xml/';


$ch = curl_init();
try {
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo curl_error($ch);
        die();
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == intval(200)) {
        echo $response;
    } else {
        echo "Ressource introuvable : " . $http_code;
    }
} catch (\Throwable $th) {
    throw $th;
} finally {
    curl_close($ch);
}



$xml = new DOMDocument;
$xml->load('position.xml');

$xsl = new DOMDocument;
$xsl->load('position.xsl');

$proc = new XSLTProcessor;
$proc->importStylesheet($xsl);
echo $proc->transformToXml($xml);



function generate(array $coords)
{
    $coord = <<<EOT
            [$coords[0],$coords[1]]
        EOT;
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
            function initialize() {
                var map = L.map('map').setView($coord, 7); // LIGNE 18
        
                var marker = L.marker($coord).addTo(map);
        
                var osmLayer = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { // LIGNE 20
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                });
            
                map.addLayer(osmLayer);
            }
        </script>
        EOT;
    return $res;
}

echo generate([48.6822, 6.1862]);
// echo '<iframe seamless width="888" height="336" frameborder="0" src="https://www.infoclimat.fr/public-api/mixed/iframeSLIDE?_ll=48.85341,2.3488&_inc=WyJQYXJpcyIsIjQyIiwiMjk4ODUwNyIsIkZSIl0=&_auth=AhhTRFIsU3FSf1RjUiRWf1A4UmcJfwUiUy8DYAxpVSgIYwNiAWFWMFc5B3oBLlVjUn8AYwgzBDRXPFEpCHpUNQJoUz9SOVM0Uj1UMVJ9Vn1QflIzCSkFIlMxA20MYlUoCG4DZwF8VjVXOwdgAS9VY1JhAGUIKAQjVzVRMAhiVDMCYVM0UjhTMFI7VDVSfVZ9UGVSMQllBTpTYwMyDGZVZAhoA2QBalZiVzgHZgEvVWlSYABmCDMENVc9UT8IYVQoAn5TTlJCUyxSfVR0UjdWJFB%2BUmcJaAVp&_c=4417435b52c22ba27af3e38c312fdea0"></iframe>';
