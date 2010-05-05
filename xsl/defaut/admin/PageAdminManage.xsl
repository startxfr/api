<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<xsl:param name="channel"/>
<xsl:param name="droit"/>
<xsl:include href="../Ref_SXToolkit.xsl" />
<xsl:template match="menutree">
	<div id="PortletPageAdmin">
		<form method="post" name="PageAdminModif" action="PageModifLot.php">
		<ul id="{$channel}">
		<xsl:apply-templates select="menu" mode="niv0">
			<xsl:sort select="@order" order="ascending"/>
		</xsl:apply-templates>
		</ul>
		<br class="clear"/>
		<div class="footer">
			<input type="hidden" name="channel" value="{$channel}"/>
			<span style="padding-top: .25em">Pour toutes les pages cochées :</span>
			<input type="submit" name="bouton" value="Supprimer"/>
			<input type="submit" name="bouton" value="Archiver"/>
			<input type="submit" name="bouton" value="Publier"/>
			<input type="submit" name="bouton" value="De-Publier"/>
		</div>
		<br class="clear"/>
		</form>
	</div>
</xsl:template>
<xsl:template match="menu" mode="niv0">
	<xsl:param name="droit" select="$droit" /> 
	<xsl:param name="channel" select="$channel" /> 
	<xsl:variable name="position" select="position()"/>
	<xsl:variable name="uriSuffix">
		<xsl:if test="$channel = 'normal'">../</xsl:if>
	</xsl:variable>
	<xsl:variable name="class">
		<xsl:if test="@actif = '-1'">Desactif</xsl:if>
		<xsl:if test="@actif = '0'">Brouillon</xsl:if>
		<xsl:if test="@actif = '2'">Archive</xsl:if>
	</xsl:variable>
	<xsl:variable name="displayForRight">
		<xsl:call-template name="displayForRight">
		<xsl:with-param name="rightList" select="@droit"/>
		<xsl:with-param name="droit" select="$droit"/>
		</xsl:call-template>
	</xsl:variable>
	<xsl:if test="  $displayForRight = 'ok'">
		<li id="{id}" class="altern{position()  mod 2} {$class}">
			<span class="txt"><input type="checkbox" name="pageDo[]" size="20" value="{id}" style="display:inline"/></span>
			<a href="{$uriSuffix}{uri}" title="{header}" target="_blank"><b><xsl:value-of select="nom"/></b> (<xsl:value-of select="modif/user"/> le <xsl:value-of select="modif/date"/>)</a>
		<xsl:choose>
			<xsl:when test="@actif = '-1'">
				<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
				<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
			</xsl:when>
			<xsl:when test="@actif = '0'">
				<a href="PageModif.php?id={id}&amp;action=publish" class="bouton" >Publier</a>
				<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
				<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
				<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
			</xsl:when>
			<xsl:when test="@actif = '2'">
				<a href="PageModif.php?id={id}&amp;action=publish" class="bouton" >Re-Publier</a>
				<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
				<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
			</xsl:when>
			<xsl:otherwise>
				<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
				<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
				<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
			</xsl:otherwise>
		</xsl:choose>
			<xsl:if test="(string-length(stat/visited) != 0) or (string-length(@droit) &gt; 0)">
				<span class="right">
				<xsl:if test="string-length(stat/visited) != 0">
					<xsl:apply-templates select="stat"/>
				</xsl:if>
				<xsl:if test="string-length(@droit) &gt; 0">
					<xsl:apply-templates select="@droit"/>
				</xsl:if>
				</span>
			</xsl:if>
		</li>
		<xsl:apply-templates select="submenu">
			<xsl:with-param name="channel" select="$channel" />
		</xsl:apply-templates>
	</xsl:if>
</xsl:template>
<xsl:template match="submenu">
		<xsl:param name="channel" select="$channel" /> 
		<ul class="submenu">
		<xsl:apply-templates select="menu" mode="niv1">
			<xsl:sort select="@order" order="ascending"/>
			<xsl:with-param name="channel" select="$channel" />
		</xsl:apply-templates>
		</ul>
	</xsl:template>
	<xsl:template match="menu" mode="niv1">
		<xsl:param name="channel" select="$channel" /> 
		<xsl:variable name="uriSuffix">
			<xsl:if test="$channel = 'normal'">../</xsl:if>
		</xsl:variable>
		<xsl:variable name="class">
			<xsl:if test="@actif = '-1'">Desactif</xsl:if>
			<xsl:if test="@actif = '0'">Brouillon</xsl:if>
			<xsl:if test="@actif = '2'">Archive</xsl:if>
		</xsl:variable>
		<xsl:variable name="displayForRight">
			<xsl:call-template name="displayForRight">
			<xsl:with-param name="rightList" select="@droit"/>
			<xsl:with-param name="droit" select="$droit"/>
			</xsl:call-template>
		</xsl:variable>
		<xsl:if test="  $displayForRight = 'ok'">
			<li id="{id}" class="altern{position()  mod 2} {$class}">
				<span class="txt"><input type="checkbox" name="pageDo[]" size="20" value="{id}" style="display:inline"/></span>
				<a href="{$uriSuffix}{uri}" title="{header}" target="_blank"><xsl:value-of select="nom"/></a>
			<xsl:choose>
				<xsl:when test="@actif = '-1'">
					<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
					<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
				</xsl:when>
				<xsl:when test="@actif = '0'">
					<a href="PageModif.php?id={id}&amp;action=publish" class="bouton" >Publier</a>
					<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
					<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
					<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
				</xsl:when>
				<xsl:when test="@actif = '2'">
					<a href="PageModif.php?id={id}&amp;action=publish" class="bouton" >Re-Publier</a>
					<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
					<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
				</xsl:when>
				<xsl:otherwise>
					<a href="PageModif.php?id={id}" class="bouton" >Modifier</a>
					<a href="PageClone.php?id={id}" class="bouton" >Cloner</a>
					<a href="PageDelete.php?id={id}" class="bouton" >Supprimer</a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="(string-length(stat/visited) != 0) or (string-length(@droit) &gt; 0)">
				<span class="right">
				<xsl:if test="string-length(stat/visited) != 0">
					<xsl:apply-templates select="stat"/>
				</xsl:if>
				<xsl:text> </xsl:text>
				<xsl:if test="string-length(@droit) &gt; 0">
					<xsl:apply-templates select="@droit"/>
				</xsl:if>
				</span>
			</xsl:if>
			</li>
		</xsl:if>
		<xsl:apply-templates select="submenu"/>
	</xsl:template>
	<xsl:template match="stat">
		<img src="../img/exclam.info.png" title="{visited} visites depuis le {from}" hspace="4"/>
	</xsl:template>
	<xsl:template match="@droit">
		<img src="../img/deconnect.mini.png" title="Page privee" hspace="4"/>
	</xsl:template>
</xsl:stylesheet>