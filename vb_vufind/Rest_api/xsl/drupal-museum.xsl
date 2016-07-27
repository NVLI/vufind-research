<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    exclude-result-prefixes="oai_dc dc">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="oai_dc:dc">
      <request>
          <type>resource</type>
          <field_solr_docid>
              <value>
                  <xsl:value-of select="//identifier"/>
              </value>
          </field_solr_docid>
          <title>
              <value>
                  <xsl:value-of select="//dc:title[normalize-space()]"/>
              </value>
          </title>
          <field_harvest_type>
            <value>museum</value>
          </field_harvest_type>
          <field_resource_type>
            <value>
              <xsl:value-of select="//dc:type" />
            </value>
          </field_resource_type>
      </request>
  </xsl:template>
</xsl:stylesheet>
