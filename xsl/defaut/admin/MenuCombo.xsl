<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<xsl:param name="toselect"/>
<xsl:template match="menutree">
	<xsl:param name="toselect" select="string($toselect)"/>
	<option value="">_______Accueil_______</option>
	<xsl:apply-templates select="menu[@actif = '1']" mode="niv0">
		<xsl:sort select="@order" order="ascending"/>
		<xsl:with-param name="toselect" select="$toselect"/>
	</xsl:apply-templates>
</xsl:template>
<xsl:template match="menu" mode="niv0">
	<xsl:param name="toselect" select="$toselect"/>
	<xsl:choose>
		<xsl:when test="$toselect = @id">
		<option value="{id}">
				<xsl:attribute name="selected">selected</xsl:attribute>
				<xsl:value-of select="nom"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{@id}"><xsl:value-of select="nom"/></option>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:apply-templates select="submenu" mode="niv1">
		<xsl:sort select="@order" order="ascending"/>
		<xsl:with-param name="toselect" select="$toselect"/>
	</xsl:apply-templates>
</xsl:template>
<xsl:template match="submenu" mode="niv1">
	<xsl:param name="toselect" select="$toselect"/>
	<xsl:apply-templates select="menu[@actif = '1']" mode="niv1">
		<xsl:with-param name="toselect" select="$toselect"/>
		<xsl:sort select="@order" order="ascending"/>
	</xsl:apply-templates>
</xsl:template>
<xsl:template match="menu" mode="niv1">
	<xsl:param name="toselect" select="$toselect"/>
	<xsl:choose>
		<xsl:when test="$toselect = @id">
			<option value="{@id}">
				<xsl:attribute name="selected">selected</xsl:attribute>
				___<xsl:value-of select="@id" disable-output-escaping="yes"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{@id}">___<xsl:value-of select="@id" disable-output-escaping="yes"/></option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
<xsl:template match="submenu" mode="niv2">
	<xsl:param name="toselect" select="$toselect"/>
	<xsl:apply-templates select="menu[@actif = '1']" mode="niv2">
		<xsl:sort select="@order" order="ascending"/>
		<xsl:with-param name="toselect" select="$toselect"/>
	</xsl:apply-templates>
</xsl:template>
<xsl:template match="menu" mode="niv2">
	<xsl:param name="toselect" select="$toselect"/>
	<xsl:choose>
		<xsl:when test="$toselect = @id">
			<option value="{@id}">
				<xsl:attribute name="selected">selected</xsl:attribute>
				______<xsl:value-of select="@id"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{@id}">______<xsl:value-of select="@id"/></option>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:apply-templates select="submenu" mode="niv2">
		<xsl:with-param name="toselect" select="$toselect"/>
	</xsl:apply-templates>
</xsl:template>
</xsl:stylesheet>
