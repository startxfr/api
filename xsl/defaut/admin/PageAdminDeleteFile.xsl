<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:param name="id"/>
<xsl:template match="page">
	<xsl:param name="id" select="$id"/>
	<form enctype="multipart/form-data" method="post" name="PageDeleteFile">
	<input type="hidden" name="action" size="20" value="confirm" />
		<div id="PortletPageDeleteFile" class="Portlet2">
		<xsl:apply-templates select="document/doc[@id = $id]"/>
		<br class="clear"/>
		<div class="footer">
			<input type="submit" name="bouton" value="Effacer"/>
			<input type="reset" name="bouton" onclick="javascript:zuno.popup.close();" value="Annuler"/>
		</div>
		</div>
		<br class="clear"/>
	</form>
</xsl:template>
<xsl:template match="doc">
	<h2>Informations sur le document "<xsl:value-of select="nom"/>"</h2>
	<hr/>
	<fieldset>
			<label for="nomdoc" title="Nom du document">Nom :</label>
			<xsl:value-of select="nom"/>
		<br class="clear"/>
			<label for="nomdoc" title="Description du document">Déscription :</label>
			<xsl:value-of select="desc"/>
		<br class="clear"/>
			<label for="filedoc" title="Fichier à envoyer au serveur">Fichier :</label>
			<a href="{uri}"><xsl:copy-of select="icone"/> <xsl:value-of select="filename"/></a>
		<br class="clear"/>
			<label for="nomdoc" title="Ordre du document">Ordre :</label>
			<xsl:value-of select="@order"/>
		<br class="clear"/>
			<label for="nomdoc" title="Propriétaire du document">Propriétaire :</label>
			<xsl:value-of select="owner"/>
		<br class="clear"/>
			<label for="nomdoc" title="Enregistrement le">Enregistré le :</label>
			<xsl:value-of select="record"/>
		<br class="clear"/>
	</fieldset>
</xsl:template>
</xsl:stylesheet>
