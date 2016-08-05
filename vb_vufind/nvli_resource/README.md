###Cleaning and Harvesting In Nvli Server and drupal
### Deleting content in drupal
*Login to Nvli dev server drupal root
* drush en drush_delete -y
* drush delete-all resource

###Remove all existing xml data and cleaning log files
*Login to Nvli server 
*cd /usr/local/vufind/harvest/local/harvest/

###For d7oai :
*rm -rf d7oai/*.xml
*rm -rf d7oai/*.delete
*rm -rf d7oai/processed/*.xml
*rm -rf d7oai/drupal-harvest-export.log
*touch d7oai/drupal-harvest-export.log
*rm -rf d7oai/harvest.log
*touch d7oai/harvest.log
*rm -rf d7oai/last_harvest.txt
*touch d7oai/last_harvest.txt

###For Dspace :
*rm -rf DSpace/*.xml
*rm -rf DSpace/*.delete
*rm -rf DSpace/processed/*.xml
*rm -rf DSpace/drupal-harvest-export.log
*touch DSpace/drupal-harvest-export.log
*rm -rf DSpace/harvest.log
*touch DSpace/harvest.log
*rm -rf DSpace/last_harvest.txt
*touch DSpace/last_harvest.txt

###For kohaoai :
*rm -rf kohaoai/*.xml
*rm -rf kohaoai/*.delete
*rm -rf kohaoai/processed/*.xml
*rm -rf kohaoai/drupal-harvest-export.log
*touch kohaoai/drupal-harvest-export.log
*rm -rf kohaoai/harvest.log
*touch kohaoai/harvest.log
*rm -rf kohaoai/last_harvest.txt
*touch kohaoai/last_harvest.txt

###For museum :
*rm -rf museum/*.xml
*rm -rf museum/*.delete
*rm -rf museum/processed/*.xml
*rm -rf museum/drupal-harvest-export.log
*touch museum/drupal-harvest-export.log
*rm -rf museum/harvest.log
*touch museum/harvest.log
*rm -rf museum/last_harvest.txt
*touch museum/last_harvest.txt

###For newspaper :
*rm -rf newspaper/*.xml
*rm -rf newspaper/*.delete
*rm -rf newspaper/processed/*.xml
*rm -rf newspaper/drupal-harvest-export.log
*touch newspaper/drupal-harvest-export.log
*rm -rf newspaper/harvest.log
*touch newspaper/harvest.log
*rm -rf newspaper/last_harvest.txt
*touch newspaper/last_harvest.txt

***After deleting everything now we need to harvest the data***

*cd /usr/local/vufind/harvest
*php harvest_oai.php
*php vb_rest_api/import-drupal-xsl.php

*cd /usr/local/vufind
*./solr.sh stop

*rm -rf solr/vufind/biblio/index solr/vufind/biblio/spell*

*./solr.sh start

*cd /usr/local/vufind/harvest

*sh ../../harvest/batch-import-xsl.sh ./d7oai  ../../import/d7oai.properties

*sh batch-import-xsl.sh ./DSpace ../import/dspace.properties

*sh batch-import-xsl.sh ./kohaoai ../import/kohaoai.properties

*sh batch-import-xsl.sh ./museum ../import/museum.properties

*sh batch-import-xsl.sh ./newspaper ../import/newspaper.properties

*sudo ../solr.sh restart