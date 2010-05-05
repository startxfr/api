<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:include href="../Ref_ZBox.xsl" />
<xsl:include href="../Ref_FCKEditor.xsl" />
<xsl:param name="message"/>
<xsl:param name="droit"/>
<xsl:param name="parent_normal_list"/>
<xsl:param name="parent_admin_list"/>
<xsl:param name="contentLangDef"/>
<xsl:param name="droit_list"/>
<xsl:param name="style_list"/>
<xsl:param name="menu_select0"/>
<xsl:param name="menu_select1"/>

<xsl:template match="page">
	<xsl:param name="message" select="$message" />
	<xsl:value-of select="$message"/>
	<xsl:call-template name="generateZBox">
		<xsl:with-param name="idBox" select="'PortletPageModif'"/>
		<xsl:with-param name="titre" select="concat('Paramètres de configuration de la page ',./nom)"/>
		<xsl:with-param name="content">
			<xsl:apply-templates select="." mode="info"/>
		</xsl:with-param>
	</xsl:call-template>
	<xsl:call-template name="generateZBox">
		<xsl:with-param name="idBox" select="'PortletPageModifContenu'"/>
		<xsl:with-param name="titre" select="concat('Contenu de la page ',./nom)"/>
		<xsl:with-param name="content">
			<xsl:apply-templates select="." mode="content"/>
		</xsl:with-param>
	</xsl:call-template>
	<xsl:call-template name="generateZBox">
		<xsl:with-param name="idBox" select="'PortletPageModifFile'"/>
		<xsl:with-param name="titre" select="concat('Documents de la page ',./nom)"/>
		<xsl:with-param name="content">
			<xsl:apply-templates select="." mode="contentFile"/>
		</xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template match="page" mode="info">
	<xsl:param name="droit_user" select="$droit"/>
	<form enctype="multipart/form-data" method="post" name="pageModif">
	<input type="hidden" name="action" size="20" value="modif" />
	<input type="hidden" name="id_pg" size="28" value="{id}" />
		<div class="block width50" id="accordionLeftContainer">
			<div>
				<h4 class="accordionTitle">Informations Techniques sur la page</h4>
				<div id="PgMdTechnique" class="form accordionContent">
					<div class="row"> 
						<div class="label">ID</div>
						<div class="field"><xsl:value-of select="id"/></div>
					</div>
					<div class="row"> 
						<div class="label">Page</div>
						<div class="field">
							<xsl:choose>
								<xsl:when test="$droit_user &lt;= 2">
									<xsl:choose>
										<xsl:when test="substring-before(uri,'?') = 'page.php'">
											<input type="text" name="page_pg" size="20" value=""/>
										</xsl:when>
										<xsl:otherwise>
											<input type="text" name="page_pg" size="20" value="{uri}"/>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="uri"/>
								</xsl:otherwise>
							</xsl:choose>###[PageSpecial]###
						</div>
					</div>
					<div class="row"> 
						<div class="label">Canal</div>
						<div class="field">
						###[cmb4Channel]###
						###[PageChannel]###</div>
					</div>
				</div>
				<h4 class="accordionTitle">Icône du menu</h4>
				<div id="PgMdImgmenu" class="form accordionContent">
					<div class="row"> 
						<div class="label">Fichier</div>
						<div class="field">
							<input type="file" name="img_menu_pg"/>
							<xsl:apply-templates select="icone"/>
						</div>
					</div>
				</div>
				<h4 class="accordionTitle">Icône du titre de la page</h4>
				<div id="PgMdImgpage" class="form accordionContent">
					<div class="row"> 
						<div class="label">Fichier</div>
						<div class="field">
							<input type="file" name="img_pg"/>
							<xsl:apply-templates select="img"/>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="block width50 accordion" id="accordionRightContainer">
			<div>
				<h4 class="accordionTitle">Paramètres de publication</h4>
				<div id="PgMdPubli" class="form accordionContent">
					<div class="row"> 
						<div class="label">Page parent</div>
						<div class="field">###[outList]###</div>
					</div>
					<div class="row"> 
						<div class="label">ordre</div>
						<div class="field"><input type="text" name="order_pg" size="2" value="{@order}"/></div>
					</div>
					<div class="row"> 
						<div class="label">Etat</div>
						<div class="field">
							<span class="txt">
							<xsl:choose>
								<xsl:when test="@actif = '-1'">
									<i>Page désactivée par l'administrateur</i>
								</xsl:when>
								<xsl:otherwise>
								Brouillon
									<input type="radio" name="actif_pg" value="0">
									<xsl:if test="@actif = '0'">
										<xsl:attribute name="checked"/>
									</xsl:if>
									</input>
								- Publié
									<input type="radio" name="actif_pg" value="1">
									<xsl:if test="@actif = '1'">
										<xsl:attribute name="checked"/>
									</xsl:if>
									</input>
								- Archivé
									<input type="radio" name="actif_pg" value="2">
									<xsl:if test="@actif = '2'">
										<xsl:attribute name="checked"/>
									</xsl:if>
									</input>
								</xsl:otherwise>
							</xsl:choose>
							</span>
						</div>
					</div>
				</div>
				<h4 class="accordionTitle">Paramètres de l'affichage de la page</h4>
				<div id="PgMdDisplay" class="form accordionContent">
					<div class="row"> 
						<div class="label">Page publique</div>
						<div class="field">
							<xsl:choose>
								<xsl:when test="string-length(droit) &gt; 0">
								<span class="txt">
									Non <input type="radio" name="usedroit" value="yes" onchange="MM_changeProp('AccessRightdiv','','style.display','','div');">
										<xsl:attribute name="checked"/>
									    </input>
									- Oui  <input type="radio" name="usedroit" value="no" onchange="MM_changeProp('AccessRightdiv','','style.display','none','div');"/>
								</span>
								<br class="clear"/>
								<div id="AccessRightdiv" style="WIDTH: 100%; POSITION: relative;"> 
									<label for="droit">droit minimum :</label>
									<select name="droit_pg[]" multiple="multiple" style="height:50px;width:200px">
										<xsl:value-of select="string($droit_list)" disable-output-escaping="yes"/>
									</select>
									<hr/>
								</div>
								</xsl:when>
								<xsl:otherwise>
								<span class="txt">
									Non <input type="radio" name="usedroit" value="yes" onchange="MM_changeProp('AccessRightdiv','','style.display','','div');"/>
									- Oui  <input type="radio" name="usedroit" value="no" onchange="MM_changeProp('AccessRightdiv','','style.display','none','div');">
											<xsl:attribute name="checked"/>
										</input>
								</span>
								<div id="AccessRightdiv" style="WIDTH: 100%; POSITION: relative; display:none"> 
								<br class="clear"/>
								<label for="droit">droit minimum :</label>
									<select name="droit_pg[]" multiple="multiple" style="height:50px;width:200px">
										<xsl:value-of select="string($droit_list)"  disable-output-escaping="yes"/>
									</select>
									<hr/>
								</div>
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</div>
					<div class="row"> 
						<div class="label">Affiche dans le menu</div>
						<div class="field">
							<xsl:choose>
								<xsl:when test="string($menu_select0) = 'checked'">
								oui	<input type="radio" name="menuon_pg" value="1" >
									</input>
								- non	<input type="radio" name="menuon_pg" value="0" >
									<xsl:attribute name="checked"/>
									</input>
								</xsl:when>
								<xsl:otherwise>
								oui	<input type="radio" name="menuon_pg" value="1" >
									<xsl:attribute name="checked"/>
									</input>
								- non	<input type="radio" name="menuon_pg" value="0" >
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</div>
					<div class="row"> 
						<div class="label">Style d'affichage</div>
						<div class="field">
							<select name="style_pg" >
								<xsl:value-of select="string($style_list)" disable-output-escaping="yes"/>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<br class="clear"/>
	<script type="text/javascript">
			var accordionLeftContainer = new accordion('accordionLeftContainer', {
			  classNames : {
					toggle : 'accordionTitle',
					toggleActive : 'accordionTitleActive',
					content : 'accordionContent'
				},
			  defaultSize : {
				    height : null,
				    width : null
				}				
			});
			var accordionRightContainer = new accordion('accordionRightContainer', {
			  classNames : {
					toggle : 'accordionTitle',
					toggleActive : 'accordionTitleActive',
					content : 'accordionContent'
				},
			  defaultSize : {
				    height : null,
				    width : null
				}				
			});
	</script>
	<div class="footer">
		<a href="javascript:document.pageModif.reset()"><img align="middle" title="../img/prospec/cancel.png" alt="../img/prospec/cancel.png" name="img" src="../img/prospec/cancel.png"/> Annuler</a>
		<a href="javascript:document.pageModif.submit()"><img align="middle" title="../img/prospec/record.png" alt="../img/prospec/record.png" name="img" src="../img/prospec/record.png"/> Enregister</a>
	</div>
</xsl:template>

<xsl:template match="page" mode="content">
	<form enctype="multipart/form-data" method="post" name="pageModifContenu">
	<input type="hidden" name="action" size="20" value="modifContent" />
	<input type="hidden" name="id_pg" size="28" value="{id}" />
		<div class="block width50">
			<div class="form">
				<div class="row">
					<div class="label"><span/></div>
					<div class="field">
						Ce contenu correspond au contenu de la langue par defaut (<xsl:value-of select="$contentLangDef"/>). Pour administrer le contenu des autres langues, 
						merci de choisir le drapeau corespondant ###[contentLangLink]### <br/>
						Si vous souhaitez modifier le contenu d'après un <acronym title="Fichier OpenDocument créé à partir de OpenOffice">fichier ODT</acronym>, vous pouvez utiliser la <a href="#" onclick="zuno.popup.open('PagePopup.ImportOdt.php','id={id}','750','600','','','','');" title="Upload de fichier ODT"> page suivante</a>
					</div>
				</div>
			</div>
		</div>
		<br class="clear"/>
		<div class="block width50">
			<div class="form">
				<div class="row"> 
					<div class="label" title="Nom de la page. S'affiche dans les menus du site.">Nom</div>
					<div class="field">
						<input type="text" name="nom_pg" size="25" value="{nom}"/>
						<img src="../img/exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
					</div>
				</div>
				<div class="row"> 
					<div class="label" title="Titre de la page. S'affiche comme titre de la page.">Titre</div>
					<div class="field">
						<input type="text" name="header_pg" size="35" value="{header}"/>
						<img src="../img/exclam.png" border="0" title="Champ obligatoire" valign="middle"/>
					</div>
				</div>
			</div>
		</div>
		<div class="block width50">
			<div class="form">
				<div class="row"> 
					<div class="label" title="Brève déscription de la page.">Description</div>
					<div class="field">
						<textarea name="desc_pg" cols="33" rows="2" ><xsl:value-of select="desc"/></textarea>
					</div>
				</div>
			</div>
		</div>
		<br class="clear"/>
		<br/>
		<xsl:call-template name="generateEditor">
			<xsl:with-param name="idBox" select="'editor'"/>
			<xsl:with-param name="content">
				<xsl:value-of select="contentEntities"/>
			</xsl:with-param>
		</xsl:call-template>
		<br/>
	</form>
	<div class="footer">
		<a href="javascript:document.pageModifContenu.reset()"><img align="middle" title="../img/prospec/cancel.png" alt="../img/prospec/cancel.png" name="img" src="../img/prospec/cancel.png"/> Annuler</a>
		<a href="javascript:document.pageModifContenu.submit()"><img align="middle" title="../img/prospec/record.png" alt="../img/prospec/record.png" name="img" src="../img/prospec/record.png"/> Enregister</a>
	</div>
</xsl:template>


<xsl:template match="img">
	<br/>
	<br class="clear"/>
	<label for="orderdoc" title="Suppression">Supprimer :</label>
	<input type="checkbox" name="img_del" value="1"/><br class="clear"/>
	<img src="{.}" name="{../nom}" alt="apercu" align="right"/>
	<br/>
</xsl:template>
<xsl:template match="icone">
	<br/>
	<br class="clear"/>
	<label for="orderdoc" title="Suppression">Supprimer :</label>
	<input type="checkbox" name="icone_del" value="1"/><br class="clear"/>
	<img src="{.}" name="{../nom}" alt="apercu" align="right"/>
	<br/>
</xsl:template>
</xsl:stylesheet>
