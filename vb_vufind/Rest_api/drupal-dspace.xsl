<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                exclude-result-prefixes="oai_dc dc">
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>
    <xsl:template match="oai_dc:dc">
        <request>
            <solr_doc_id>
                <value>
                    <xsl:value-of select="//identifier"/>
                </value>
            </solr_doc_id>
            <field_title>
                <value>
                    <xsl:value-of select="//dc:title[normalize-space()]"/>
                </value>
            </field_title>
        </request>
    </xsl:template>
</xsl:stylesheet>
