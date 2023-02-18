<?php


$opts = array('http' => array('proxy' => 'tcp://www-cache:3128', 'request_fulluri' => true));
$context = stream_context_create($opts);

$res = file_get_contents('http://ip-api.com/xml/', false, $context);
$xml = simplexml_load_string($res);

$lat = $xml->lat;
$lon = $xml->lon;

$coord = $lat . ',' . $lon;

$xsl = new DOMDocument();
$xsl->load('./xsl_xml/meteo/meteo.xsl');


$urlMeteo = 'http://www.infoclimat.fr/public-api/gfs/xml?_ll=' . $coord . '&_auth=VU9XQFUrVXdVeFRjBnABKFM7V2IOeFB3Uy9SMQ5rAn8Bal4%2FAGAGYAdpA35TfAI0V3oFZgoxADAEbwZ%2BCnhVNFU%2FVztVPlUyVTpUMQYpASpTfVc2Di5Qd1MxUjwOYAJ%2FAWdeOgB9BmUHawNkU30CNFdkBWAKKgAnBGYGZwpgVTJVNlc2VTRVNlU5VDcGKQEqU2ZXNA5iUGBTMFI3DmECYgFlXjIAZwZtB2EDZlN9Aj9XYAVnCjwAPARjBmcKZVUpVSlXSlVFVSpVelR0BmMBc1N9V2IOb1A8&_c=a04a6187f15f87439891f17702c39b9b';
$resMeteo = file_get_contents($urlMeteo, false, $context);
$xmlMeteo = simplexml_load_string($resMeteo);


$proc = new XSLTProcessor;
$proc->importStylesheet($xsl);
echo $proc->transformToXml($xmlMeteo);
