<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:doaj="http://www.doaj.org/schemas/"
    exclude-result-prefixes="doaj">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="doaj:record">
        <request>
          <type>resource</type>
          <field_solr_docid>
              <value>
                  <xsl:value-of select="//doaj:doajIdentifier"/>
              </value>
          </field_solr_docid>
          <title>
              <value>
                  <xsl:value-of select="//doaj:title[normalize-space()]"/>
              </value>
          </title>
          <field_harvest_type>
            <value>doaj</value>
          </field_harvest_type>
          <field_resource_type>
            <value>Article</value>
          </field_resource_type>
        </request>
    </xsl:template>
</xsl:stylesheet>
