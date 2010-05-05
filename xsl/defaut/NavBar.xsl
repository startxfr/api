<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="text" encoding="UTF-8"/>
	<xsl:param name="droit"/>
	<xsl:param name="select"/>
  <xsl:template match="menutree">
	<xsl:param name="droit" select="$droit" />
	<xsl:param name="select" select="$select" />
	<div id="NavBar">
	<ul>
		<li>Vous etes ici : </li>
		<xsl:choose>
			<xsl:when test="$select = ''">
				<xsl:apply-templates  select="menu[@id = 'index']" mode="MakeLink"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates  select="menu[@id = 'index']" mode="MakeLink"/>
				<xsl:apply-templates  select="menu" mode="ScanForLink">
					 <xsl:with-param name="select" select="$select" />
				</xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</ul>
	</div>
	</xsl:template>
	<xsl:template match="menu" mode="MakeLink">
		<li><a href="{uri}" title="{header}"><xsl:value-of select="nom"/></a></li>
	</xsl:template>
	<xsl:template match="menu" mode="ScanForLink">
		<xsl:param name="select"/>
		<xsl:choose>
			<xsl:when test="@id = $select">
				<li>&gt;</li><li class="selected"><xsl:value-of select="nom"/></li>
			</xsl:when>
			<xsl:when test="descendant::*[@id = $select]">
				 <li>&gt;</li><li><a href="{uri}" title="{header}"><xsl:value-of select="nom"/></a></li>
				<xsl:apply-templates  select="submenu/menu" mode="ScanForLink">
					 <xsl:with-param name="select" select="$select" />
				</xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
