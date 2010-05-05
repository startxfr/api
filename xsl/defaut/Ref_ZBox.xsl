<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<!-- TEMPLATE D'INVERTION DE CHAINE PAR DYCHOTOMIE -->


<xsl:template name="generateZBox">
	   <xsl:param name="idBox"/>
	   <xsl:param name="titre"/>
	   <xsl:param name="content"/>
	   <xsl:param name="footer" value="''"/>
	   <div class="ZBox" id="{$idBox}">
			<div class="header">
				<div class="row">
					<div class="title"><h3><a title="Titre" id="{$idBox}Title" href="#"><xsl:value-of select="$titre"/></a></h3></div>
					<div class="options">
						<ul>
							<li><a title="Fermer la boite de dialogue" id="{$idBox}OptCloser" href="#">x</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="{$idBox}Body" class="body">
				<div class="content">
					<xsl:copy-of select="$content"/>
				</div>
				<xsl:copy-of select="$footer"/>
			</div>
		</div>
		<script language="Javascript">
		var <xsl:value-of select="$idBox"/>ZBox = new Zbox('<xsl:value-of select="$idBox"/>','open');
		</script>
</xsl:template>
</xsl:stylesheet>