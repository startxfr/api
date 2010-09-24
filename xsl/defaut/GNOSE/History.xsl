<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:include href="../Ref_SXToolkit.xsl" />
    <xsl:include href="../Ref_FileType.xsl" />
    <xsl:include href="../Ref_ZBox.xsl" />
    <xsl:output method="html" encoding="UTF-8" indent="no"/>
    <xsl:param name="Rev"/>
    <xsl:template match="gnoseHistory">
	<xsl:param name="Rev" select="$Rev" />
	<xsl:if test="count(//logentry[./@revision = $Rev]) &gt; 0">
	    <xsl:variable name="dateM" select="//logentry[./@revision = $Rev]/date"/>
	    <xsl:variable name="year" select="substring-before($dateM,'-')"/>
	    <xsl:variable name="month" select="substring-before(substring-after($dateM,'-'),'-')"/>
	    <xsl:variable name="day" select="substring-after(substring-after(substring-before($dateM,'T'),'-'),'-')"/>
	    <xsl:variable name="time" select="substring-before(substring-after($dateM,'T'),'.')"/>
	    <xsl:variable name="hour" select="substring-before($time,':')"/>
	    <xsl:variable name="minute" select="substring-before(substring-after($time,':'),':')"/>
	    <xsl:variable name="previous" select="number($Rev) - 1"/>
	    <xsl:variable name="next" select="number($Rev) + 1"/>


	    <xsl:call-template name="generateZBox">
		<xsl:with-param name="idBox" select="'GnoseHistoryDetail'"/>
		<xsl:with-param name="titre">
		    Historique de l'enregistrement n°<xsl:value-of select="$Rev"/>
		</xsl:with-param>
		<xsl:with-param name="content">
		    <div class="blockTable" id="History">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 0px !important">
			    <tr class="titre">
				<td width="200"><a href="History.php?rev={$previous}"><img alt="précédent" src="'.getStaticUrl('img').'back.png"/> Modification précédente</a></td>
				<td> </td>
				<td width="200" align="right" class="right"><a href="History.php?rev={$next}">Modification suivante <img alt="suivant" src="'.getStaticUrl('img').'next.png"/></a></td>
			    </tr>
			</table>
			<table width="100%" cellspacing="0">
			    <tr class="altern0">
				<th class="bg barre right" width="300">Utilisateur </th>
				<td class="last"><a href="#" onclick="return zuno.popup.open('../User.php','type=popup&amp;id={//logentry[./@revision = $Rev]/author}','550','300','','','','User');" title="Détail sur l'utilisateur" ><b><xsl:value-of select="//logentry[./@revision = $Rev]/author"/></b></a></td>
			    </tr>
			    <tr class="altern1">
				<th class="bg barre right">Date </th>
				<td class="last"><xsl:attribute name="nowrap"/><xsl:value-of select="$day"/>/<xsl:value-of select="$month"/>/<xsl:value-of select="$year"/>&#160;<xsl:value-of select="$hour"/>:<xsl:value-of select="$minute"/></td>
			    </tr>
			    <tr class="altern0">
				<th class="bg barre right">Nbre de fichier </th>
				<td class="last"><xsl:value-of select="count(//logentry[./@revision = $Rev]/paths/path)"/></td>
			    </tr>
			    <tr class="altern1">
				<th class="bg barre right">Message </th>
				<td class="last"><xsl:value-of select="//logentry[./@revision = $Rev]/msg"/></td>
			    </tr>
			</table>
		    </div>
		</xsl:with-param>
	    </xsl:call-template>


	    <xsl:call-template name="generateZBox">
		<xsl:with-param name="idBox" select="'GnoseHistoryDetailFiles'"/>
		<xsl:with-param name="titre" select="'Liste des fichiers de ce lot'"/>
		<xsl:with-param name="content">
		    <div class="blockTable" id="HistoryFiles">

			<table width="100%" cellspacing="0">
			    <tr class="titre">
				<th colspan="3" class="left last">Liste des fichiers de ce lot</th>
			    </tr>
			    <xsl:apply-templates select="//logentry[./@revision = $Rev]/paths/path" mode="Principal"/>
			</table>
		    </div>
		</xsl:with-param>
	    </xsl:call-template>
	    
	</xsl:if>
    </xsl:template>
    <xsl:template match="path" mode="Principal">
	<xsl:variable name="WP"><xsl:value-of select="substring-before(substring-after(.,'/'),'/')"/></xsl:variable>
	<xsl:variable name="IDFile"><xsl:value-of select="substring-after(.,$WP)"/></xsl:variable>
	<xsl:variable name="IsDir">
	    <xsl:if test="contains($IDFile,'.')">false</xsl:if>
	    <xsl:if test="not(contains($IDFile,'.'))">true</xsl:if>
	</xsl:variable>
	<xsl:variable name="NameInversedTmp">
	    <xsl:call-template name="invertion">
		<xsl:with-param name="input" select="$IDFile"/>
	    </xsl:call-template>
	</xsl:variable>
	<xsl:variable name="NameInversed">
	    <xsl:value-of select="substring-before($NameInversedTmp,'/')"/>
	</xsl:variable>
	<xsl:variable name="NameFile">
	    <xsl:call-template name="invertion">
		<xsl:with-param name="input" select="$NameInversed"/>
	    </xsl:call-template>
	</xsl:variable>
	<xsl:variable name="BrowseURI">
	    <xsl:if test="$WP = 'PERSO'">BrowsePerso.php</xsl:if>
	    <xsl:if test="$WP = 'ARCHIVES'">BrowseArchive.php</xsl:if>
	    <xsl:if test="$WP = 'WORK'">BrowseWork.php</xsl:if>
	    <xsl:if test="string-length($WP) = 0">BrowseWork.php</xsl:if>
	</xsl:variable>





	<xsl:variable name="WPCopied">
	    <xsl:if test="count(@copyfrom-path) &gt; 0">
		<xsl:value-of select="substring-before(substring-after(@copyfrom-path,'/'),'/')"/>
	    </xsl:if>
	</xsl:variable>
	<xsl:variable name="BrowseURICopied">
	    <xsl:if test="$WPCopied = 'PERSO'">BrowsePerso.php</xsl:if>
	    <xsl:if test="$WPCopied = 'ARCHIVES'">BrowseArchive.php</xsl:if>
	    <xsl:if test="$WPCopied = 'WORK'">BrowseWork.php</xsl:if>
	    <xsl:if test="string-length($WPCopied) = 0">BrowseWork.php</xsl:if>
	</xsl:variable>
	<xsl:variable name="URICopied">
	    <xsl:if test="count(@copyfrom-path) &gt; 0">/<xsl:value-of select="substring-after(substring-after(@copyfrom-path,'/'),'/')"/></xsl:if>
	</xsl:variable>
	<xsl:variable name="NameCopiedInversedTmp">
	    <xsl:call-template name="invertion">
		<xsl:with-param name="input" select="$URICopied"/>
	    </xsl:call-template>
	</xsl:variable>
	<xsl:variable name="NameCopiedInversed">
	    <xsl:value-of select="substring-before($NameCopiedInversedTmp,'/')"/>
	</xsl:variable>
	<xsl:variable name="NameCopied">
	    <xsl:call-template name="invertion">
		<xsl:with-param name="input" select="$NameCopiedInversed"/>
	    </xsl:call-template>
	</xsl:variable>
	<xsl:variable name="ParentURICopied">
	    <xsl:value-of select="substring(substring-before($URICopied,$NameCopied),1,string-length(substring-before($URICopied,$NameCopied))-1)"/>
	</xsl:variable>

	<xsl:variable name="modifClass">
	    <xsl:choose>
		<xsl:when test="./@action = 'M'">modif</xsl:when>
		<xsl:when test="./@action = 'D'">delete</xsl:when>
		<xsl:when test="./@action = 'A'">add</xsl:when>
		<xsl:otherwise>altern</xsl:otherwise>
	    </xsl:choose>
	</xsl:variable>

	<tr class="{$modifClass}{position()  mod 2}">
	    <td>
		<xsl:attribute name="nowrap"/>
		<xsl:if test="$IsDir = 'true'">
		    <a href="{$BrowseURI}?rep=WORK{$IDFile}&amp;sortie=popup" title="voir le répertoire {$NameFile}">
			<img src="'.getStaticUrl('img').'files/dir.png" alt="voir le répertoire {$NameFile}" title="voir le répertoire {$NameFile}"/>
			<b><xsl:value-of select="$NameFile"/></b>
		    </a>
		</xsl:if>
		<xsl:if test="$IsDir = 'false'">
		    <a href="#" onclick="return zuno.popup.open('../gnose/FileInfo.php','wpath={$WP}&amp;id={$IDFile}','730','600','','','','Info');" title="Info sur ce fichier">
			<xsl:call-template name="AnalyseFilename">
			    <xsl:with-param name="file" select="$NameFile" />
			    <xsl:with-param name="type" select="'IMAGE'" />
			</xsl:call-template>
			<b><xsl:value-of select="$NameFile"/></b>
		    </a>
		</xsl:if>
	    </td>
	    <td class="barre">
		<xsl:if test="count(@copyfrom-path) &gt; 0">
		    copié depuis le fichier
		    <a href="#" onclick="return zuno.popup.open('../gnose/FileInfo.php','wpath={$WPCopied}&amp;id={$URICopied}','730','600','','','','Info2');" title="Info sur ce sujet">
		    <b><xsl:value-of select="$NameCopied"/></b><img src="'.getStaticUrl('img').'go.info.png" hspace="4" alt="Info sur ce sujet"/></a>
		    ( répertoire <xsl:value-of select="$WPCopied"/>:
		    <a href="#" onclick="window.location.href='../gnose/{$BrowseURICopied}?dir={$ParentURICopied}';" title="Voir le repertoire parent">
		    <xsl:value-of select="$ParentURICopied"/><img src="'.getStaticUrl('img').'go.dir.png" hspace="4" title="Voir le repertoire parent"/></a>)
		    <br/>
		</xsl:if>
	    </td>
	    <td class="right" width="20"><xsl:attribute name="nowrap"/>
		<xsl:if test="$IsDir = 'false'">
		    <xsl:choose>
			<xsl:when test="./@action = 'D'"></xsl:when>
			<xsl:otherwise><a href="../gnose/BrowseWork.php?action=download&amp;fich={$WP}{$IDFile}&amp;rev={../../@revision}" title="Télécharger ce fichier"><img src="'.getStaticUrl('img').'gnose/download.png" alt="Télécharger ce fichier"/></a></xsl:otherwise>
		    </xsl:choose>
		</xsl:if>
	    </td>
	</tr>
    </xsl:template>
</xsl:stylesheet>
