<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:nlm="http://dtd.nlm.nih.gov/publishing/2.3"
    xmlns:mml="http://www.w3.org/1998/Math/MathML"
    exclude-result-prefixes="nlm mml">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="nlm:article">
        <request>
            <solr_doc_id>
                <value>
                    <xsl:value-of select="nlm:identifier"/>
                </value>
            </solr_doc_id>
            <title>
                <value>
                    <xsl:value-of select="//nlm:article-title[normalize-space()]"/>
                </value>
            </title>
            <type>
              <value>nlm_ojs</value>
            </type>
            <format>
              <value>Online</value>
            </format>
        </request>
    </xsl:template>
</xsl:stylesheet>
