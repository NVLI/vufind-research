<?php
/**
 * @file
 * import-drupal-xsl.php.
 */

if (!file_exists(__DIR__ . '/harvest-import.log')) {
  $response = simplexml_load_string('<?xml version="1.0"?>
<response><item key="0"><solr_doc_id>ir-100-278</solr_doc_id></item><item key="1"><solr_doc_id>ir-100-285</solr_doc_id></item><item key="2"><solr_doc_id>ir-100-282</solr_doc_id></item><item key="3"><solr_doc_id>ir-100-283</solr_doc_id></item><item key="4"><solr_doc_id>ir-100-287</solr_doc_id></item><item key="5"><solr_doc_id>ir-100-286</solr_doc_id></item><item key="6"><solr_doc_id>ir-100-281</solr_doc_id></item><item key="7"><solr_doc_id>ir-100-284</solr_doc_id></item><item key="8"><solr_doc_id>ir-100-279</solr_doc_id></item><item key="9"><solr_doc_id>ir-100-277</solr_doc_id></item></response>');
  $response_item = $response->item;
  $file = fopen(__DIR__ . '/harvest-import.log', 'a+');
  foreach ($response_item  as $item) {
    $processed_ids[] = $item->solr_doc_id;
    fputs($file, $item->solr_doc_id . PHP_EOL);
  }
}
else {
  $file = fopen(__DIR__ . '/harvest-import.log', 'a+');
  $processed_ids = file_get_contents(__DIR__ . '/harvest-import.log');
  $processed_ids = explode(PHP_EOL, $processed_ids);
}
$harvested_ids = array('ir-100','ir-101','ir-102','ir-103','ir-104','ir-106');
foreach ($harvested_ids as $id) {
  if (!in_array($id, $processed_ids)) {
    fputs($file, $id . PHP_EOL);
  }
}
fclose($file);
exit;

$vufind_home = getenv('VUFIND_HOME');
$oai_ini_path = $vufind_home . '/harvest/oai.ini';
$nvli_drupal_host = 'http://188.166.216.52/d8';
$vufind_local_dir = getenv('VUFIND_LOCAL_DIR');

// Load oai.ini
$oai_settings = parse_ini_file($oai_ini_path, TRUE);
if ($oai_settings) {
  $oai_pmh_list = array_keys($oai_settings);
}
else {
  echo 'Getting `oai.ini` settings failed.'; exit;
}

// To get X-CSRF-Token.
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "$nvli_drupal_host/rest/session/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
  ),
));

$x_csrf_token = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err; exit;
}

foreach ($oai_pmh_list as $oai_pmh) {
  $oai_pmh_dir = "$vufind_local_dir/harvest/$oai_pmh";
  $harvested_files = glob("$oai_pmh_dir/*.xml");

  // Get xml template to post from xml data using xsl.

  $xslDoc = new DOMDocument();
  $xslDoc->load(__DIR__ . "/drupal-dspace.xsl");
  foreach ($harvested_files as $oai_dc) {
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($oai_dc);

    $proc = new XSLTProcessor();
    $proc->importStylesheet($xslDoc);
    $input_xml = $proc->transformToXml($xmlDoc);

    // Post it using curl.

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "$nvli_drupal_host/entity/solr_annotation?_format=xml",
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $input_xml,
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic YWRtaW46YWRtaW4=",
        "cache-control: no-cache",
        "content-type: application/xml",
        "x-csrf-token: $x_csrf_token"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err; exit;
    }
    else {
      echo $response;

    }
  }
}
