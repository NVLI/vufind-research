<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:doaj="http://www.doaj.org/schemas/"
    exclude-result-prefixes="doaj">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="doaj:record">
        <request>
          <solr_doc_id>
              <value>
                  <xsl:value-of select="//doaj:doajIdentifier"/>
              </value>
          </solr_doc_id>
          <title>
              <value>
                  <xsl:value-of select="//doaj:title[normalize-space()]"/>
              </value>
          </title>
          <type>
            <value>doaj</value>
          </type>
        </request>
    </xsl:template>
</xsl:stylesheet>
