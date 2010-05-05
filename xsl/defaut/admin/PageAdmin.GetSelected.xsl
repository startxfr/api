<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes"/>
<xsl:template match="page"><xsl:apply-templates select="parent"/></xsl:template>
<xsl:template match="parent"><xsl:value-of select="menu/id"/></xsl:template>
</xsl:stylesheet>
