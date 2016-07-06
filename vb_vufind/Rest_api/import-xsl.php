<?php
/**
 * @file
 * import-xsl.php.
 */

// To get X-CSRF-Token.
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://188.166.208.4/drupal8/rest/session/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: 43e408a3-2a5e-eed9-8551-17bcb764edb4"
  ),
));

$x_csrf_token = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err; exit;
}

$harvest_dir = '/usr/local/vufind/local/harvest/DSpace/processed';
$harvested_files = glob("$harvest_dir/*.xml");

// Get xml template to post from xml data using xsl.

$xslDoc = new DOMDocument();
$xslDoc->load("drupal-dspace.xsl");
foreach ($harvested_files as $oai_dc) {
  $xmlDoc = new DOMDocument();
  $xmlDoc->load($oai_dc);

  $proc = new XSLTProcessor();
  $proc->importStylesheet($xslDoc);
  $input_xml = $proc->transformToXml($xmlDoc);

  // Post it using curl.

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://188.166.208.4/drupal8/entity/solr_annotation?_format=xml",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $input_xml,
    CURLOPT_HTTPHEADER => array(
      "authorization: Basic YWRtaW46VmFsdWViMHVuZEBudmxp",
      "cache-control: no-cache",
      "content-type: application/xml",
      "x-csrf-token: $x_csrf_token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    echo $response;
  }
}
