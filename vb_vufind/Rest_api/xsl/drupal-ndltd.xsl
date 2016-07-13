<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    exclude-result-prefixes="oai_dc dc">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="oai_dc:dc" omit-xml-declaration="yes">

    <xsl:if test="//dc:identifier">

      <xsl:variable name="URLs"><xsl:for-each select="//dc:identifier"><xsl:value-of select="."/></xsl:for-each></xsl:variable>

      <xsl:if test="contains($URLs, '://') or contains($URLs, 'urn:')">
          <request>
              <solr_doc_id>
                  <value>
                      <xsl:value-of select="//identifier"/>
                  </value>
              </solr_doc_id>
              <title>
                  <value>
                      <xsl:value-of select="//dc:title[normalize-space()]"/>
                  </value>
              </title>
              <type>
                <value>ndltd</value>
              </type>
          </request>
        </xsl:if>
     </xsl:if>
    </xsl:template>
</xsl:stylesheet>
