<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="GlobalParam.xsl" />
<xsl:param name="droit"/>
<xsl:output method="html" encoding="UTF-8"/>
<xsl:template match="page">
<xsl:param name="droit" select="$droit" />
	<div id="PortletPageDesc"><span/>
		<xsl:apply-templates select="submenu">
			<xsl:with-param name="droit" select="$droit"/>
		</xsl:apply-templates>
	</div>
</xsl:template>
<xsl:template match="submenu">
<xsl:param name="droit" select="$droit" />
	<xsl:choose>
		<xsl:when test="count(menu[((string-length(@droit) = 0) or (@droit &gt;= $droit)) and (@menuon = 1)]) &gt; 0">
			<div id="PortletPageDescDocument" class="Portlet2">
			<br/>
			<table cellspacing="0" width="100%">
				<xsl:apply-templates select="menu[((string-length(@droit) = 0) or (@droit &gt;= $droit)) and (@menuon = 1)]"/>
			</table>
			</div>
			<br class="clear"/>
		</xsl:when>
		<xsl:otherwise>
		</xsl:otherwise>
	</xsl:choose>
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

<xsl:template match="img">
	<img src="{.}" name="{../nom}" alt="{../nom}"/>
</xsl:template>
<xsl:template match="icone" mode="submenu">
	<img src="{.}" name="{../nom}" alt="{../nom}" align="left"/>
</xsl:template>
</xsl:stylesheet>
