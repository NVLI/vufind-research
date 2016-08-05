<?php
$vufind_home = getenv('VUFIND_HOME');
//$oai_ini_path = $vufind_home . '/harvest/oai.ini';
$nvli_drupal_host = 'http://dev-nvli.iitb.ac.in';
$vufind_local_dir = getenv('VUFIND_LOCAL_DIR');


$oai_pmh_list = array('kohache', 'kohaiitb');
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
  $oai_pmh_dir = "$vufind_local_dir/local/import/marc/$oai_pmh";
  $harvested_files = glob("$oai_pmh_dir/*.xml");

//   Get harvest xml id for which entity is been created till now.
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

 $cnt = 0;
  foreach ($harvested_files as $oai_dc) {
    $input_xml = (array) simplexml_load_file($oai_dc);
    $title = $cur_solr_doc_id = '';
    foreach ($input_xml['record'] as $i_xml) {
      $i = $j = 0;
      foreach ($i_xml as $val) {
        if ((string) $i_xml->attributes()['tag'][0] == '952' && (string) $val->attributes()['code'] == 'p') {
          $cur_solr_doc_id = $i_xml->subfield[$i];
        }
        $i++;
      }
      foreach ($i_xml as $val) {
        if ((string) $i_xml->attributes()['tag'][0] == '020' && (string) $val->attributes()['code'] == 'a') {
          $title = $i_xml->subfield[$j];
        }
        $j++;
      }
    }

    $request_data = '<request>
                        <type>resource</type>
                        <field_solr_docid>
                            <value>'.$cur_solr_doc_id.'</value>
                        </field_solr_docid>
                        <title>
                          <value>'.$title.'</value>
                        </title>
                        <field_harvest_type>
                          <value>'.$oai_pmh.'</value>
                        </field_harvest_type>
                        <field_resource_type>
                          <value>Book</value>
                        </field_resource_type>
                    </request>';

                          
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
        CURLOPT_POSTFIELDS => $request_data,
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
      if($cnt == 9) {exit;} $cnt++;
    }
  }
}
fclose($file);
