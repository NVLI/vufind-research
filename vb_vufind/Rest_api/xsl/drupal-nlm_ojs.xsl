<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:nlm="http://dtd.nlm.nih.gov/publishing/2.3"
    xmlns:mml="http://www.w3.org/1998/Math/MathML"
    exclude-result-prefixes="nlm mml">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="nlm:article">
        <request>
            <type>resource</type>
            <field_solr_docid>
                <value>
                    <xsl:value-of select="nlm:identifier"/>
                </value>
            </field_solr_docid>
            <title>
                <value>
                    <xsl:value-of select="//nlm:article-title[normalize-space()]"/>
                </value>
            </title>
            <field_harvest_type>
              <value>nlm_ojs</value>
            </field_harvest_type>
            <field_resource_type>
              <value>Online</value>
            </field_resource_type>
        </request>
    </xsl:template>
</xsl:stylesheet>
