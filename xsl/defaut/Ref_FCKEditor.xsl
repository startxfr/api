<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<!-- TEMPLATE D'INVERTION DE CHAINE PAR DYCHOTOMIE -->


<xsl:template name="generateEditor">
	   <xsl:param name="idBox"/>
	   <xsl:param name="content"/>
		<input type="hidden" id="{$idBox}" name="{$idBox}" value="{$content}" style="display:none" />
		<input type="hidden" id="{$idBox}___Config" value="" style="display:none" />
		<iframe id="{$idBox}___Frame" src="../jss/fckeditor/editor/fckeditor.html?InstanceName={$idBox}&amp;Toolbar=Default" width="100%" height="400" frameborder="0" scrolling="no"><xsl:text>sss</xsl:text></iframe>
</xsl:template>
</xsl:stylesheet>