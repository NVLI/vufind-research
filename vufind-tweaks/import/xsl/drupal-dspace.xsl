<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:php="http://php.net/xsl"
    xmlns:xlink="http://www.w3.org/2001/XMLSchema-instance">
    <xsl:output method="text" indent="yes" encoding="utf-8"/>
    <xsl:template match="dc:jatan">
        <xsl:value-of select="//dc:title"/><xsl:text>,</xsl:text>
        <xsl:value-of select="//dc:description" /><xsl:text>,</xsl:text>
        <xsl:value-of select="//dc:publisher"/><xsl:text>,</xsl:text>
        <xsl:value-of select="//dc:date"/><xsl:text>,</xsl:text>
        <xsl:value-of select="//dc:type"/><xsl:text>,</xsl:text>
        <xsl:value-of select="//dc:format"/><xsl:text>,</xsl:text>
    </xsl:template>
</xsl:stylesheet>
