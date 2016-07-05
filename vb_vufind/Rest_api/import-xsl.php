<?php
/**
 * @file
 * import-xsl.php.
 */

// To get X-CSRF-Token.
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://nvli.local/rest/session/token",
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

// Get xml template to post from xml data using xsl.

$xslDoc = new DOMDocument();
$xslDoc->load("drupal-dspace.xsl");

$xmlDoc = new DOMDocument();
$xmlDoc->load("1467108207_ir_100_18429.xml");

$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$input_xml = $proc->transformToXml($xmlDoc);

// Post it using curl.
$url = "http://nvli.local/entity/solr_annotation?_format=xml";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://nvli.local/entity/solr_annotation?_format=xml",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "<request>\n  <solr_doc_id>\n    <value>ir-100-test</value>\n  </solr_doc_id>\n  <annotation>\n    <value>test</value>\n  </annotation>\n</request>",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic bnZsaTpudmxp",
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
