<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="GlobalParam.xsl" />
<xsl:param name="droit"/>
<xsl:output method="html" encoding="UTF-8"/>
<xsl:template match="page">
<xsl:param name="droit" select="$droit" />
	<div id="PortletPageDesc">
		<div id="PortletPageDescContent"><span/>
			<xsl:value-of select="content"/>
		</div>
		<br/>
		<xsl:apply-templates select="document"/>
		<xsl:apply-templates select="submenu">
			<xsl:with-param name="droit" select="$droit"/>
		</xsl:apply-templates>
	</div>
</xsl:template>
<xsl:template match="submenu">
<xsl:param name="droit" select="$droit" />
	<div id="PortletPageDescDocument" class="Portlet2">
	<h2>Les pages de cette rubrique</h2>
	<hr/>
	<table cellspacing="0">
		<xsl:apply-templates select="menu[((string-length(@droit) = 0) or (@droit &gt;= $droit)) and (@menuon = 1)]"/>
	</table>
	</div>
	<br class="clear"/>
	<br/>
</xsl:template>
<xsl:template match="menu">
	<xsl:if test="(position() mod 2) = 1">
		<![CDATA[<tr>]]>
	</xsl:if>
		<td class="barre" width="50%" style="padding-right: 30px">
			<a href="{uri}"><xsl:apply-templates select="icone[string-length(.) &gt; 0]" mode="submenu"/><h3><xsl:value-of select="header"/></h3></a>
			<a href="{uri}"><span class="desc" style="margin-top: 15px"><xsl:value-of select="desc"/></span></a>
		</td>
	<xsl:if test="(position() mod 2) = 0">
		<![CDATA[</tr><tr><td colspan="3">&nbsp;</td></tr>]]>
	</xsl:if>
</xsl:template>

<xsl:template match="document">
	<div id="PortletPageDescDocument" class="Portlet2">
	<h2>Documents à télécharger</h2>                                                                                    
	<hr/>
	<table cellspacing="0">
		<tr class="titre"> 
			<th class="barre"><a href="?order=nom" title="Order">Nom </a></th>
			<th class="center"><a href="?order=file" title="Order">Déscription</a></th>
			<th class="center"><a href="?order=file" title="Order">Doc</a></th>
			<th class="last right">Informations</th>
		</tr>
		<xsl:apply-templates select="doc">
			<xsl:sort order="ascending" select="@order"/>
		</xsl:apply-templates>
	</table>
	</div>
	<br class="clear"/>
	<br/>
</xsl:template>
<xsl:template match="doc">
	<tr class="altern{position()  mod 2}">
		<td class="barre"><xsl:value-of select="nom"/></td>
		<td class="center barre"><a href="{uri}"><xsl:value-of select="desc"/></a></td>
		<td class="center barre"><a href="{uri}"><xsl:copy-of select="icone"/><xsl:value-of select="filename"/></a></td>
		<td class="last right bg" width="10">
				<small><xsl:value-of select="owner"/> - <xsl:value-of select="record"/></small>
		</td>
	</tr>
</xsl:template>
<xsl:template match="img">
	<img src="{.}" name="{../nom}" alt="{../nom}"/>
</xsl:template>
</xsl:stylesheet>
