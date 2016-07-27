<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:mets="http://www.loc.gov/METS/"
    xmlns:METS="http://www.loc.gov/METS/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:access="http://www.fedora.info/definitions/1/0/access/"
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
    xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result"
    xmlns:fedora-model="info:fedora/fedora-system:def/model#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rel="info:fedora/fedora-system:def/relations-external#"
    xmlns:foxml="info:fedora/fedora-system:def/foxml#"
    xmlns:exsl="http://exslt.org/common"
    xmlns:math="http://exslt.org/math"
    exclude-result-prefixes="exsl math foxml rel rdf fedora-model sparql oai_dc access xlink dc METS mets"
    >
    <xsl:output method="xml" indent="yes" encoding="utf-8" omit-xml-declaration="yes"/>

    <xsl:template match="/">



        <xsl:variable name="PID" select="//dc:identifier"/>

        <xsl:variable name="DC" select="document(concat($fedoraURL, ':', $fedoraPort, '/fedora/objects/', $PID, '/datastreams/DC/content'))"/>

        <xsl:variable name="RELS-EXT" select="document(concat($fedoraURL, ':', $fedoraPort, '/fedora/objects/', $PID, '/datastreams/RELS-EXT/content'))"/>
        <!-- <xsl:variable name="modelType" select="substring-after($RELS-EXT//fedora-model:hasModel[last()]/@rdf:resource, 'info:fedora/')"/> -->
        <!--
        <xsl:variable name="objectInfo" select="document(concat($fedoraURL, '/fedora/objects/', $PID, '?field_resource_type=xml'))"/>
        -->
        <!-- -->
        <xsl:variable name="listDatastreams" select="document(concat($fedoraURL, ':', $fedoraPort, '/fedora/objects/', $PID, '/datastreams?field_resource_type=xml'))"/>
            <request>
                <type>resource</type>
                <field_solr_docid>
                    <value>
                        <xsl:value-of select="$PID"/>
                    </value>
                </field_solr_docid>
                <title>
                    <value>
                        <xsl:value-of select="$DC//dc:title[normalize-space()]"/>
                    </value>
                </title>
                <type>
                  <value>vudl_foxml</value>
                </type>
                <xsl:for-each select="$DC//dc:field_resource_type">
                    <field_resource_type>
                      <value>
                        <xsl:value-of select="."/>
                      </value>
                    </field_resource_type>
                </xsl:for-each>
            </request>
    </xsl:template>

    <xsl:template name="parent">
        <xsl:param name="parentURI_template"/>
        <xsl:param name="parentName_template"/>
        <!-- <xsl:if test="substring-after($parentURI_template,'/') != 'vudl:1' and substring-after($parentURI_template,'/') != 'vudl:3'"> --> <!--   -->
            <parent uri="{$parentURI_template}" PID="{substring-after($parentURI_template,'/')}" name="{$parentName_template}">
                <xsl:for-each select="//sparql:child[@uri=$parentURI_template]">
                    <xsl:variable name="new_parentURI_template" select="../sparql:parent/@uri"/>
                    <xsl:variable name="new_parentName_template" select="../sparql:parentTitle"/>
                    <xsl:call-template name="parent">
                        <xsl:with-param name="parentURI_template" select="$new_parentURI_template"/>
                        <xsl:with-param name="parentName_template" select="$new_parentName_template"/>
                    </xsl:call-template>
                </xsl:for-each>
            </parent>
        <!-- </xsl:if> -->
    </xsl:template>

</xsl:stylesheet>
