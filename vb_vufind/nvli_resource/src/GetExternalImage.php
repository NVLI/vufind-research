<?php

namespace Drupal\nvli_resource;

use Drupal\Core\File\FileSystem;
use Drupal\Component\Utility\Crypt;

/**
 * Class GetExternalImage.
 *
 * @package Drupal\nvli_resource
 */
class GetExternalImage implements GetExternalImageInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * @param $url
   *  require image link.
   *
   * @return int|null|string
   *  file id of image.
   */

  public function getExternalImage($url) {
    $image = file_get_contents($url);
    $file_name = basename($url);
    $file = file_save_data($image, 'public://book_image/' . $file_name);
    return $file->id();
  }

  /**
   * @param $ItemId
   *  AWS item id of the image which needs to be stored.
   *
   * @return int|null|string
   *  file id of image.
   */
  public function getAwsImage($ItemId) {
    $client = \Drupal::httpClient();
    $awsaccesskeyid = \Drupal::config('nvli_resource.awscredential')
      ->get('awsaccesskeyid');
    $associatetag = \Drupal::config('nvli_resource.awscredential')
      ->get('associatetag');
    $awssecretaccesskey = \Drupal::config('nvli_resource.awscredential')
      ->get('awssecretaccesskey');
    $request_signature = \Drupal::config('nvli_resource.awscredential')
      ->get('request_signature');
    $ItemId = 'B004HO6I4M';
    $time = date('Ymd');
    $signature = $this->getSignature($awssecretaccesskey);
    $request_url = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&Operation=ItemLookup&ResponseGroup=Images&IdType=ASIN&&ItemId=' . $ItemId . '&AWSAccessKeyId=' . $awsaccesskeyid . '&AssociateTag=' . $associatetag . '&Timestamp=' . $time . '&Signature=' . $signature;
    ep($request_url);
    exit;
    $request = $client->get($request_url);

    ep($request);
    exit;

    $response = $request->getBody();
    return $id;
  }

  protected function getSignature($awssecretaccesskey) {
    $request = 'GET\n/test.txt\nX-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=' . $awssecretaccesskey . date('Ymd') . 'us-east-1%2Fs3%2Faws4_request&X-Amz-Date=' . date('Ymd') . '&X-Amz-Expires=86400&X-Amz-SignedHeaders=host
host:examplebucket.s3.amazonaws.com

host
UNSIGNED-PAYLOAD';

  }
}
