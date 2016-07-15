### Setting up Drupal
#### WHERE TO PLACE THESE FILES?
Placing downloaded and custom modules in this directory separates downloaded and
custom modules from Drupal core's modules. This allows Drupal core to be updated
without overwriting these files.

**Modules to be placed in module/contrib folder**
* search_api
* search_api_solr
* custom_solr_search

**Modules to be placed in module/custom**
* nvli_annotation_services
* nvli_resource
* custom_solr_annotation

These are the basic module we will need to be installed in Drupal 8 instance.

**Apart from drupal 8 modules we need to add few files in solr directory /usr/local/vufind/harvest**
*  vb_rest_api/add_annotation_solr.php
*  vb_rest_api/import-drupal-xsl.php

#### Installation
* Install both contributed and custom Drupal modules listed above.
* Install core Rest module. And download and install contributed Rest_ui module.
* Enable following Rest resources:
** Add annotation rest resource
** Annotation count rest resource
** Nvli Resource entity

### Create Drupal entity for harvested xml docs (Rest_api)
#### Harvesting workflow
Whenever harvesting process is done and before processing indexing, need to run this script. Including the harvesting process we need to run following commands:
* `cd /usr/local/vufind/harvest`
* `php harvest_oai.php`
* `php vb_rest_api/import-drupal-xsl.php`

#### Re - Indexing annotation workflow
* `cd /usr/local/vufind`
* `./solr.sh stop`
* `rm -rf solr/vufind/biblio/index solr/vufind/biblio/spell*`
* `./solr.sh start`
* `cd /usr/local/vufind/harvest`
* `sh batch-import-xsl.sh ./DSpace ../import/dspace.properties`
* `../solr.sh restart`
* `php vb_rest_api/add_annotation_solr.php`

#### About internal log file
We maintain a log file named 'drupal-harvest-export.log' to keep track for which all harvested xml we had created entity in Drupal backend. This log file can be found in corresponding harvest directory, for example if DSpace then location will be `/usr/local/vufind/local/harvest/DSpace/drupal-harvest-export.log`.

Initially or log file doesn't exist or deleted, then Rest API will be called to get all list of entities already created in Drupal. With those entities solr doc ids, log file will be created to avoid duplication.
