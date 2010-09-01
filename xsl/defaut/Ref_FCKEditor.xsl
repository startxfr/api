<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<!-- TEMPLATE D'INVERTION DE CHAINE PAR DYCHOTOMIE -->


<xsl:template name="generateEditor">
	   <xsl:param name="idBox"/>
	   <xsl:param name="content"/>
		<textarea id="{$idBox}" name="{$idBox}"><xsl:value-of select="$content"/></textarea>
</xsl:template>
</xsl:stylesheet>