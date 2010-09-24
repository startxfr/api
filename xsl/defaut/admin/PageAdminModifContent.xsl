<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:param name="message"/>
<xsl:param name="droit"/>
<xsl:param name="lang"/>
<xsl:template match="page">
	<xsl:param name="message" select="$message" />
	<xsl:param name="droit_user" select="$droit"/>
	<xsl:param name="lang" select="$lang"/>
	<form enctype="multipart/form-data" method="post" name="pageModif">
	<input type="hidden" name="action" size="20" value="modif" />
	<input type="hidden" name="language" size="20" value="{$lang}" />
	<input type="hidden" name="id_pg" size="28" value="{id}" />
			<xsl:value-of select="$message"/>
		<div id="PortletPageModifContent" class="Portlet2">
		
		<h2>Contenu de la page</h2>
		<hr/>
		<fieldset>
			<legend>Description : </legend>
				<div class="leftRow">
				<label for="nom" title="Nom de la page. S'affiche dans les menus du site.">Nom :</label>
					<input type="text" name="nom_pg" size="20" value="{nom}"/>
					<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
				<br class="clear"/>
				<label for="titre" title="Titre de la page. S'affiche comme titre de la page.">Titre :</label>
					<input type="text" name="header_pg" size="20" value="{header}"/>
					<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
				<br class="clear"/>
				</div>
				<div class="rightRow">
				<label for="desc" title="Brève déscription de la page.">Description :</label>
					<textarea name="desc_pg" cols="25" rows="2" ><xsl:value-of select="desc"/></textarea>
				</div>
				<br class="clear"/>
			</fieldset>
		<br class="clear"/>
		<br/>
		<textarea id="editor" name="editor" style="width:100%" rows="25" cols="100"><xsl:value-of select="content"/></textarea>
		<br class="clear"/>
		<br/>
		<div class="footer">
			<input type="submit" name="bouton" class="" value="Enregister"/>
			<input type="reset" name="bouton" class="" value="Effacer"/>
		</div>
		</div>
		<br class="clear"/>
	</form>
</xsl:template>
</xsl:stylesheet>
