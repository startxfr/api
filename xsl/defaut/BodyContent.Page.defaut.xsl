<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:include href="GlobalParam.xsl" />
	<xsl:output method="html" encoding="UTF-8"/>
	<xsl:template match="page">
	<div id="PortletPageDesc"><span/>
		<xsl:value-of select="content"/>
	</div>
	</xsl:template>
</xsl:stylesheet>
