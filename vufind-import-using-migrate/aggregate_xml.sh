#!/bin/bash
_dfiles="/usr/local/vufind/local/harvest/DSpace/*.xml"
echo 'identifier,datestamp,publisher,type,language\n' >> test.csv;
for f in $_dfiles
do
  echo "Processing $f file..."
  xsltproc oai.xsl $f >> test.csv
  echo '\n' >> test.csv
done