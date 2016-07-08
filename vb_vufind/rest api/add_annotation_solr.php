<?php
$curl = curl_init();
$base_url = 'http://dev-d8.local';
curl_setopt_array($curl, array(
  CURLOPT_URL => $base_url."/nvli/count?_format=xml",
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic YWRtaW46YWRtaW4=",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
}
else {
  $count = simplexml_load_string($response);
  $curl = curl_init();
  for ($i = 0; $i < ceil($count / 100); $i++) {
    curl_setopt_array($curl, array(
      CURLOPT_URL => $base_url."/nvli/add-annotation?_format=xml&offset=" . ($i * 100) . "&limit=100",
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_HTTPHEADER => array(
        "authorization: Basic YWRtaW46YWRtaW4=",
        "cache-control: no-cache"
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    if ($err) {
      echo "cURL Error #:" . $err;
    }
    else {
      $result = simplexml_load_string($response);
      echo 'Record Processed : '.($result->success+$result->fail+$result->exist).' Success Record : '.$result->success.' Fail Record : '.$result->fail.' Exist Record : '.$result->exist;
    }
  }
  curl_close($curl);
}
