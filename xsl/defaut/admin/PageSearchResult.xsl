<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<xsl:variable name="lowercase" select="'abcdefghijklmnopqrstuvwxyz'"/>
<xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
<xsl:param name="SearchedString"/>
<xsl:template match="Index">
	<xsl:param name="SearchedString" select="$SearchedString" />
	<xsl:param name="searched" select="translate($SearchedString,$lowercase,$uppercase)" />
	<div id="PortletPageSearch" class="blockTable">
	<br/>
		<h2>Resultat de la recherche sur "<xsl:value-of select="$SearchedString" />"</h2>
		<hr/>
			<!-- On compte le nombre de resultat trouves selon les types de contenu -->
			<xsl:variable name="PageCounter">
				<xsl:apply-templates select="ContentType[@table = 'page']" mode="CountPage">
					<xsl:with-param name="searched" select="$searched" />
				</xsl:apply-templates>
			</xsl:variable>
			<xsl:variable name="DocCounter">
				<xsl:apply-templates select="ContentType[@table = 'document']" mode="CountDoc">
					<xsl:with-param name="searched" select="$searched" />
				</xsl:apply-templates>
			</xsl:variable>
			<xsl:variable name="ActuCounter">
				<xsl:apply-templates select="ContentType[@table = 'actualite']" mode="CountActualite">
					<xsl:with-param name="searched" select="$searched" />
				</xsl:apply-templates>
			</xsl:variable>
			<xsl:variable name="GlobalCounter">
				<xsl:value-of select="$PageCounter+$DocCounter+$ActuCounter"/>
			</xsl:variable>
			<!-- On affiche le nombre de resultat trouve par type de documents -->
			<xsl:choose>
				<xsl:when test="$GlobalCounter = 0">
					<center><b><span class="important">Aucun resultat pertinent pour votre requete</span></b></center>
				</xsl:when>
				<xsl:otherwise>
					<i><b><xsl:value-of select="$GlobalCounter"/></b> document(s) trouvee(s)  dont: </i>
					<xsl:choose>
						<xsl:when test="$PageCounter = 0">
						</xsl:when>
						<xsl:otherwise>
							<b><xsl:value-of select="$PageCounter"/></b> page(s), 
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="$DocCounter = 0">
						</xsl:when>
						<xsl:otherwise>
							<b><xsl:value-of select="$DocCounter"/></b> document(s), 
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="$ActuCounter = 0">
						</xsl:when>
						<xsl:otherwise>
							<b><xsl:value-of select="$ActuCounter"/></b> actualite(s), 
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
			
			<xsl:choose>
				<xsl:when test="$PageCounter = 0"></xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="ContentType[@table = 'page']" mode="DisplayPage">
						<xsl:with-param name="searched" select="$searched" />
					</xsl:apply-templates>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="$DocCounter = 0"></xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="ContentType[@table = 'document']" mode="DisplayDoc">
						<xsl:with-param name="searched" select="$searched" />
					</xsl:apply-templates>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="$ActuCounter = 0"></xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="ContentType[@table = 'actualite']" mode="DisplayActualite">
						<xsl:with-param name="searched" select="$searched" />
					</xsl:apply-templates>
				</xsl:otherwise>
			</xsl:choose>
		<br class="clear"/>
	</div>
</xsl:template>
<!-- -->
<!--       Page Search handling        -->
<!-- -->
<xsl:template match="ContentType" mode="CountPage">
	<xsl:param name="searched" />
	<xsl:value-of select="
	count(page[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched) or 
		contains(translate(./header,$lowercase,$uppercase),$searched) or 
		contains(translate(./content,$lowercase,$uppercase),$searched)
	])
	"/>
</xsl:template>
<xsl:template match="ContentType" mode="DisplayPage">
	<xsl:param name="searched" />
	<br class="clear"/>
	<br/>
	<h3>Pages trouvees</h3>
	<hr/>
	<ul>
	<xsl:for-each select="page[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched) or 
		contains(translate(./header,$lowercase,$uppercase),$searched) or 
		contains(translate(./content,$lowercase,$uppercase),$searched)
	]">
		<xsl:apply-templates select="."/>
	</xsl:for-each>
	</ul>
</xsl:template>
<xsl:template match="page">
		<li id="{id}">
		<a href="{uri}" title="{header}">
		<span class="titre"><xsl:apply-templates select="icone" mode="normal"/><xsl:value-of select="nom"/></span>
		<span class="visit">(modifie le <xsl:value-of select="modif/date"/>)</span>
		<br class="clear"/>
		<span class="content"><xsl:value-of select="desc"/></span>
		</a>
		</li>
</xsl:template>

<!-- -->
<!--     Document Search handling      -->
<!-- -->
<xsl:template match="ContentType" mode="CountDoc">
	<xsl:param name="searched" />
	<xsl:value-of select="
	count(doc[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched) or 
		contains(translate(./filename,$lowercase,$uppercase),$searched)
	])
	"/>
</xsl:template>
<xsl:template match="ContentType" mode="DisplayDoc">
	<xsl:param name="searched" />
	<br class="clear"/>
	<br/>
	<h3>Documents trouvees</h3>
	<hr/>
	<ul>
	<xsl:for-each select="doc[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched) or 
		contains(translate(./filename,$lowercase,$uppercase),$searched)
	]">
		<xsl:apply-templates select="."/>
	</xsl:for-each>
	</ul>
</xsl:template>
<xsl:template match="doc">
		<li id="{id}">
		<a href="{uri}" title="{filename}">
		<span class="titre"><xsl:apply-templates select="icone" mode="copy"/><xsl:value-of select="nom"/> (fichier: <xsl:value-of select="filename"/>)</span>
		<span class="visit">(enregistre le <xsl:value-of select="record"/> par  <xsl:value-of select="modif/user"/>)</span>
		<br class="clear"/>
		<span class="content"><xsl:value-of select="desc"/></span>
		</a>
		</li>
</xsl:template>

<!-- -->
<!--     Actualite Search handling     -->
<!-- -->
<xsl:template match="ContentType" mode="CountActualite">
	<xsl:param name="searched" />
	<xsl:value-of select="
	count(actualite[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched)
	])
	"/>
</xsl:template>
<xsl:template match="ContentType" mode="DisplayActualite">
	<xsl:param name="searched" />
	<br class="clear"/>
	<br/>
	<h3>Actualites trouvees</h3>
	<hr/>
	<ul>
	<xsl:for-each select="actualite[
		contains(translate(./nom,$lowercase,$uppercase),$searched) or 
		contains(translate(./desc,$lowercase,$uppercase),$searched)
	]">
		<xsl:apply-templates select="."/>
	</xsl:for-each>
	</ul>
</xsl:template>
<xsl:template match="actualite">
		<li id="{id}">
		<a href="Actualite.php?id={id}" title="{nom}">
		<span class="titre"><xsl:apply-templates select="img" mode="normal"/><xsl:value-of select="nom"/></span>
		<span class="visit">(enregistre le <xsl:value-of select="create"/>)</span>
		<br class="clear"/>
		<span class="content"><xsl:value-of select="desc"/> <xsl:apply-templates select="file"/></span>
		</a>
		</li>
</xsl:template>

<xsl:template match="file">
	<xsl:if test="string-length(.) &gt; 0">
		<br/><a href="{.}" title="{../nom}">telecharger le fichier</a>
	</xsl:if>
</xsl:template>
<xsl:template match="icone|img" mode="normal">
	<xsl:if test="string-length(.) &gt; 0">
		<img src="{.}" hspace="4"/>
	</xsl:if>
</xsl:template>
<xsl:template match="icone" mode="copy">
<xsl:if test="img">
	<img src="{img/@src}" hspace="4"/>
</xsl:if>
</xsl:template>
</xsl:stylesheet>
