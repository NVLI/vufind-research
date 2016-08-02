<?php
/**
 * @file
 * import-drupal-xsl.php.
 */

$vufind_home = getenv('VUFIND_HOME');
$oai_ini_path = $vufind_home . '/harvest/oai.ini';
$http_password = 'admin:password@';
$nvli_drupal_host = 'http://' . $http_password . 'dev-nvli.iitb.ac.in';
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
  echo "cURL Error #:" . $err;
  exit;
}

foreach ($oai_pmh_list as $oai_pmh) {
  $oai_pmh_dir = "$vufind_local_dir/harvest/$oai_pmh";
  $harvested_files = glob("$oai_pmh_dir/*.xml");

  // Get harvest xml id for which entity is been created till now.
  if (!file_exists($oai_pmh_dir . '/drupal-harvest-export.log')) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "$nvli_drupal_host/nvli/resource_ids",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
      exit;
    }
    $response = simplexml_load_string($response);
    $file = fopen($oai_pmh_dir . '/drupal-harvest-export.log', 'a+');
    $processed_ids = array();
    foreach ($response  as $item) {
      $processed_ids[] = $item->field_solr_docid;
      fputs($file, $item->field_solr_docid . PHP_EOL);
    }
  }
  else {
    $file = fopen($oai_pmh_dir . '/drupal-harvest-export.log', 'a+');
    $processed_ids = file_get_contents($oai_pmh_dir . '/drupal-harvest-export.log');
    $processed_ids = explode(PHP_EOL, $processed_ids);
  }

  // Get xml template to post from xml data using xsl.
  $xslDoc = new DOMDocument();
  if (!file_exists(__DIR__ . "/xsl/drupal-" . strtolower($oai_pmh) . ".xsl")) {
    continue;
  }
  $xslDoc->load(__DIR__ . "/xsl/drupal-" . strtolower($oai_pmh) . ".xsl");
  foreach ($harvested_files as $oai_dc) {
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($oai_dc);

    $proc = new XSLTProcessor();
    $proc->importStylesheet($xslDoc);
    $input_xml = $proc->transformToXml($xmlDoc);
    $cur_solr_doc_id = (array) simplexml_load_string($input_xml)->field_solr_docid->value;

    if (!in_array($cur_solr_doc_id[0], $processed_ids)) {
      // Post it using curl.
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "$nvli_drupal_host/entity/node?_format=xml",
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $input_xml,
        CURLOPT_HTTPHEADER => array(
          "authorization: Basic YWRtaW46cGFzc3dvcmQ=",
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
        exit;
      }
      else {
        echo 'Created entity for solr_doc_id: ' . $cur_solr_doc_id[0] . ' in ' . $oai_pmh_dir . PHP_EOL;
        fputs($file, $cur_solr_doc_id[0] . PHP_EOL);
      }
    }
  }
}
fclose($file);
