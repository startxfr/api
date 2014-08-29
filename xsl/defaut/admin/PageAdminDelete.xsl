<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:param name="message"/>
<xsl:param name="droit"/>
<xsl:template match="page">
	<xsl:param name="message" select="$message" />
	<xsl:param name="droit_user" select="$droit"/>
	<form enctype="multipart/form-data" method="post" name="produitModif">
	<input type="hidden" name="action" size="20" value="delete" />
	<input type="hidden" name="id" size="20" value="{id}" />
	<input type="hidden" name="channel" size="20" value="{channel}" />
		<xsl:value-of select="$message"/>
		<div id="PortletPageModif" class="Portlet2">
		<h2>Informations sur la page</h2>
		<hr/>
		<div class="leftRow">
			<fieldset>
				<legend>Informations technique : </legend>
				<label for="id" title="ID de la page.">ID :</label>
				<span class="txt">
					<xsl:value-of select="id"/>
				</span>
				<br class="clear"/>
				<label for="page" title="">Page :</label>
				<xsl:choose>
					<xsl:when test="$droit_user &lt;= 2">
						<xsl:value-of select="uri"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="uri"/>
					</xsl:otherwise>
				</xsl:choose>
				<br class="clear"/>
				<label for="administration" title="">Canal :</label>
					<span class="txt">
						<xsl:if test="channel = 'admin'">
							admin
						</xsl:if>
						<xsl:if test="channel != 'admin'">
							normal
						</xsl:if>
					</span>
				<br class="clear"/>
			</fieldset>
			<fieldset>
				<legend>Description : </legend>

				<label for="nom" title="Nom de la page. S'affiche dans les menus du site.">Nom :</label>
					<span class="txt">
						<xsl:value-of select="nom"/>
					</span>
				<br class="clear"/>
				<label for="titre" title="Titre de la page. S'affiche comme titre de la page.">Titre :</label>
					<span class="txt">
						<xsl:value-of select="header"/>
					</span>
				<br class="clear"/>
				<label for="desc" title="Brève déscription de la page.">Description :</label>
					<span class="txt">
						<xsl:value-of select="desc"/>
					</span>
			</fieldset>
			<fieldset>
				<legend>Publication : </legend>
				<label for="parent" title="">Page parent :</label>
					<span class="txt"><xsl:text> </xsl:text>
					<xsl:if test="count(parent) &gt; 0">
						<xsl:value-of select="parent/menu/nom"/>
					</xsl:if>
					</span>
					<br class="clear"/>
				<label for="ordre" title="ordonancement de la page.">ordre :</label>
					<span class="txt">
						<xsl:value-of select="@order"/>
					</span>
					<br class="clear"/>
				<label for="administration" title="">Etat :</label>
					<span class="txt">
						<xsl:choose>
						<xsl:when test="@actif = '-1'">
							<i>Page désactivée par l'administrateur</i>
						</xsl:when>
						<xsl:otherwise>
							<xsl:if test="@actif = '0'">
								Brouillon
							</xsl:if>
							<xsl:if test="@actif = '1'">
								Publié
							</xsl:if>
							<xsl:if test="@actif = '2'">
								Archivé
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
					</span>
					<br class="clear"/>
			</fieldset>
		</div>
		<div class="rightRow">
			<fieldset id="Affichage">
				<legend>Affichage : </legend>
				<label for="acces" title="Cette page est elle restreinte.">Page publique :</label>
				<xsl:choose>
					<xsl:when test="string-length(droit) &gt; 0">
					<span class="txt">
						Non
					</span>
					<br class="clear"/>
					</xsl:when>
					<xsl:otherwise>
					<span class="txt">
						Oui
					</span>
					</xsl:otherwise>
				</xsl:choose>
				<br class="clear"/>
				<label for="menuon" title="Affiche cette page dans les menu.">Affiche dans le menu :</label>
					<span class="txt">
						<xsl:if test="@menuon = 1">
							oui
						</xsl:if>
						<xsl:if test="@menuon = 0">
							non
						</xsl:if>
					</span>
						
				<br class="clear"/>
				<label for="style">Style d'affichage :</label>
					<span class="txt">
						<xsl:value-of select="style"/>
					</span>
			</fieldset>
			<fieldset>
				<legend>Image du menu : </legend>
				<label for="file" title="Fichiers.">Fichier :</label>
					<xsl:apply-templates select="icone"/>
			</fieldset>
			<fieldset>
				<legend>Image de la page : </legend>
				<label for="file" title="Fichiers.">Fichier :</label>
					<xsl:apply-templates select="img"/>
			</fieldset>
		</div>
		<br class="clear"/>
		<br/>
		<h2>Contenu de la page</h2>
		<hr/>
		<span class="txt">
			<xsl:value-of select="content"/>
		</span>
		
		<br/>
		<br/>
		<br/>
		<div class="footer">

			<input type="submit" name="bouton" class="" value="Confirmer"/>
			<input type="submit" name="bouton" class="" value="Annuler"/>
		</div>
		</div>
	</form>
	</xsl:template>
	<xsl:template match="img">
		<xsl:if test="string-length(.) != 0">
		<img src="{.}" name="{../nom}" alt="apercu"/>
		</xsl:if>
		<xsl:if test="string-length(.) = 0">
			<i>aucune image</i>
		</xsl:if>
		<br/>
	</xsl:template>
	<xsl:template match="icone">
		<xsl:if test="string-length(.) != 0">
		<img src="{.}" name="{../nom}" alt="apercu"/>
		</xsl:if>
		<xsl:if test="string-length(.) = 0">
			<i>aucune image</i>
		</xsl:if>
		<br/>
	</xsl:template>
</xsl:stylesheet>
