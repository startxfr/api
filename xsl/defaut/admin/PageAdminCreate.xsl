<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:include href="../Ref_ZBox.xsl" />
<xsl:param name="message"/>
<xsl:param name="droit"/>
<xsl:param name="parent_normal_list"/>
<xsl:param name="parent_admin_list"/>
<xsl:param name="droit_list"/>
<xsl:param name="style_list"/>



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
		<div class="block width50" id="accordionLeftContainer">
			<div>
				<h4 class="accordionTitle">Informations Techniques sur la page</h4>
				<div id="PgMdTechnique" class="form accordionContent">
					<div class="row"> 
						<div class="label">ID</div>
						<div class="field"><input type="hidden" name="id_pg" size="28" value="{id}" /><xsl:value-of select="id"/></div>
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
		<a href="javascript:document.pageModif.reset()"><img align="middle" title="'.getStaticUrl('img').'prospec/cancel.png" alt="'.getStaticUrl('img').'prospec/cancel.png" name="img" src="'.getStaticUrl('img').'prospec/cancel.png"/> Annuler</a>
		<a href="javascript:document.pageModif.submit()"><img align="middle" title="'.getStaticUrl('img').'prospec/record.png" alt="'.getStaticUrl('img').'prospec/record.png" name="img" src="'.getStaticUrl('img').'prospec/record.png"/> Enregister</a>
	</div>
</xsl:template>

<xsl:template match="page" mode="content">
	<form enctype="multipart/form-data" method="post" name="pageModifContenu">
	<input type="hidden" name="action" size="20" value="modifContent" />
		<span>
		Ce contenu correspond au contenu de la langue par defaut (<xsl:value-of select="$contentLangDef"/>). Pour administrer le contenu des autres langues, 
		merci de choisir le drapeau corespondant ###[contentLangLink]### <br/>
		Si vous souhaitez modifier le contenu d'après un <acronym title="Fichier OpenDocument créé à partir de OpenOffice">fichier ODT</acronym>, vous pouvez utiliser la <a href="#" onclick="zuno.popup.open('PagePopup.ImportOdt.php','id={id}','750','600','','','','');" title="Upload de fichier ODT"> page suivante</a>
		</span>
		<br class="clear"/>
		<div class="block width50">
			<div class="form">
				<div class="row"> 
					<div class="label" title="Nom de la page. S'affiche dans les menus du site.">Nom</div>
					<div class="field">
						<input type="text" name="nom_pg" size="25" value="{nom}"/>
						<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
					</div>
				</div>
				<div class="row"> 
					<div class="label" title="Titre de la page. S'affiche comme titre de la page.">Titre</div>
					<div class="field">
						<input type="text" name="header_pg" size="35" value="{header}"/>
						<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/>
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
		<textarea id="editor" name="editor" style="width:100%" rows="35" cols="120"><xsl:value-of select="content"/></textarea>
	</form>
	<div class="footer">
		<a href="javascript:document.pageModifContenu.reset()"><img align="middle" title="'.getStaticUrl('img').'prospec/cancel.png" alt="'.getStaticUrl('img').'prospec/cancel.png" name="img" src="'.getStaticUrl('img').'prospec/cancel.png"/> Annuler</a>
		<a href="javascript:document.pageModifContenu.submit()"><img align="middle" title="'.getStaticUrl('img').'prospec/record.png" alt="'.getStaticUrl('img').'prospec/record.png" name="img" src="'.getStaticUrl('img').'prospec/record.png"/> Enregister</a>
	</div>
</xsl:template>

<xsl:template match="page" mode="contentFile">
	<form enctype="multipart/form-data" method="post" name="pageModifFile">
	<input type="hidden" name="action" size="20" value="modifFile" />
		<div class="block width50">
			<h4>Ajouter un document</h4>
			<div id="PgMdTechnique" class="form">
				<div class="row"> 
					<div class="label" title="Nom du document">Nom</div>
					<div class="field"><input type="text" name="nom_doc" size="30" />
						 <img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/>
					</div>
				</div>
				<div class="row"> 
					<div class="label" title="Déscription du document">Déscription</div>
					<div class="field">
						<textarea id="desc_doc" name="desc_doc" rows="2" cols="30"><xsl:text> </xsl:text></textarea>
					</div>
				</div>
				<div class="row"> 
					<div class="label" title="Fichier à envoyer au serveur">Fichier</div>
					<div class="field">
						<input type="file" name="file_doc" size="15" />
						<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/>
					</div>
				</div>
				<div class="row"> 
					<div class="label" title="Ordre d'affichage dans la liste des documents">Ordre</div>
					<div class="field">
						<input type="text" name="order_doc" size="3" />
					</div>
				</div>
			</div>
		</div>	
	
		<div class="block width50">
			<h4>Liste des documents de la page</h4>
			<div id="PgMdTechnique" class="form">
				<xsl:choose>
					<xsl:when test="document">
						<xsl:apply-templates select="document"/>
					</xsl:when>
					<xsl:otherwise>
						<div class="row"> 
							<div class="label" title="Nom du document">Documents</div>
							<div class="field"><i>aucun</i></div>
						</div>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>
	</form>
	<br class="clear"/>
	<div class="footer">
		<a href="javascript:document.pageModifFile.reset()"><img align="middle" title="'.getStaticUrl('img').'prospec/cancel.png" alt="'.getStaticUrl('img').'prospec/cancel.png" name="img" src="'.getStaticUrl('img').'prospec/cancel.png"/> Annuler</a>
		<a href="javascript:document.pageModifFile.submit()"><img align="middle" title="'.getStaticUrl('img').'prospec/record.png" alt="'.getStaticUrl('img').'prospec/record.png" name="img" src="'.getStaticUrl('img').'prospec/record.png"/> Ajouter ce document</a>
	</div>
</xsl:template>

























<xsl:template match="page">
	<xsl:param name="message" select="$message" />
	<xsl:param name="droit_user" select="$droit"/>
	<xsl:param name="parent_normal_list" select="$parent_normal_list" />
	<xsl:param name="parent_admin_list" select="$parent_admin_list"/>
	<xsl:param name="droit_list" select="$droit_list" />
	<xsl:param name="style_list" select="$style_list"/>
	<form enctype="multipart/form-data" method="post" name="produitModif">
	<input type="hidden" name="action" size="20" value="create" />
		<xsl:value-of select="$message"/>
		<div id="PortletPageModif" class="Portlet2">
		<h2>Informations sur la page</h2>
		<hr/>
		<div class="leftRow">
			<fieldset>
				<legend>Informations technique : </legend>
				<label for="id" title="ID de la page.">ID :</label>
				<span class="txt">
					<input type="text" name="id_pg" size="28" value="" />
					<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/>
					###[HelpID]###
				</span>
				<br class="clear"/>
				<label for="page" title="">Page :</label>
					<xsl:if test="$droit_user = 0">
						<input type="text" name="page_pg" size="20" />
					</xsl:if>
					<xsl:if test="$droit_user &gt; 0">
						Non accessible avec vos droits
					</xsl:if>###[PageSpecial]###
				<br class="clear"/>
				<label for="administration" title="">Canal :</label>
					<span class="txt">
					###[cmb4Channel]###
					###[PageChannel]###</span>
				<br class="clear"/>
			</fieldset>
			<fieldset>
				<legend>Description : ###[PageBlocDesc]###</legend>

				<label for="nom" title="Nom de la page. S'affiche dans les menus du site.">Nom :</label>
					<input type="text" name="nom_pg" size="25" />
					<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
				<br class="clear"/>
				<label for="titre" title="Titre de la page. S'affiche comme titre de la page.">Titre :</label>
					<input type="text" name="header_pg" size="35" />
					<img src="'.getStaticUrl('img').'exclam.png" border="0" title="Champ obligatoire" valign="middle"/> 
				<br class="clear"/>
				<label for="desc" title="Brève déscription de la page.">Description :</label>
					<textarea name="desc_pg" cols="33" rows="2" ><xsl:text> </xsl:text></textarea>
			</fieldset>
			<fieldset>
				<legend>Publication : </legend>
				<label for="parent" title="">Page parent :</label>
					###[outList]###
					<br/>
				<label for="ordre" title="ordonancement de la page.">ordre :</label>
					<input type="text" name="order_pg" size="2"/>
					<br class="clear"/>
				<label for="administration" title="">Etat :</label>
					<span class="txt">
					Brouillon
						<input type="radio" name="actif_pg" value="0"/>
					- Publié
						<input type="radio" name="actif_pg" value="1">
							<xsl:attribute name="checked"/>
						</input>
					</span>
			</fieldset>
		</div>
		<div class="rightRow">
			<fieldset id="Affichage">
				<legend>Affichage : ###[PageBlocDisplay]###</legend>
				<label for="acces" title="Cette page est elle restreinte.">Page publique :</label>
				<span class="txt">
				Non
					<input type="radio" name="usedroit" value="yes" onchange="MM_changeProp('AccessRightdiv','','style.display','','div');"/>
				- Oui 
					<input type="radio" name="usedroit" value="no" onchange="MM_changeProp('AccessRightdiv','','style.display','none','div');">
						<xsl:attribute name="checked"/>
					</input>
				</span>
				<div id="AccessRightdiv" style="WIDTH: 100%; display:none; POSITION: relative;"> 
					<br class="clear"/>
					<label for="droit">droit minimum :</label>
					<select name="droit_pg[]" multiple="multiple" style="height:50px;width:200px">
						<xsl:value-of select="$droit_list"/>
					</select>
					<hr/>
				</div>
				<br class="clear"/>
				<label for="menuon" title="Affiche cette page dans les menu.">Affiche dans le menu :</label>
				oui	<input type="radio" name="menuon_pg" value="1" >
					<xsl:attribute name="checked">checked</xsl:attribute>
					</input>
				- non	<input type="radio" name="menuon_pg" value="0" />
				<br class="clear"/>
				<label for="style">Style d'affichage :</label>
				<select name="style_pg" >
					<xsl:value-of select="$style_list"/>
				</select>
			</fieldset>
			<fieldset>
				<legend>Image du menu : </legend>
				<label for="file" title="Fichiers.">Fichier :</label>
					<input type="file" name="img_menu_pg"/>
			</fieldset>
			<fieldset>
				<legend>Image de la page : </legend>
				<label for="file" title="Fichiers.">Fichier :</label>
					<input type="file" name="img_pg"/>
			</fieldset>
		</div>
		<br class="clear"/>
		<br/>
		<h2>Contenu de la page</h2>
		<hr/>
		<textarea id="editor" name="editor" style="width:100%" rows="35" cols="120"><xsl:text> </xsl:text></textarea>
		<br/>
		<div class="footer">
			<input type="submit" name="bouton" class="" value="Enregister"/>
			<input type="reset" name="bouton" class="" value="Effacer"/>
		</div>
		</div>
	</form>
	</xsl:template>
</xsl:stylesheet>
