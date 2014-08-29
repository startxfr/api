<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>

<xsl:template name="calcul-jour-julien">
	<xsl:param name="annee"/>
	<xsl:param name="mois"/>
	<xsl:param name="jour"/>
	<xsl:variable name="a" select="floor((14 - $mois) div 12)"/>
	<xsl:variable name="y" select="$annee + 4800 - $a"/>
	<xsl:variable name="m" select="$mois + 12 * $a - 3"/>
	<xsl:value-of select="$jour + floor((153 * $m +2) div 5) + $y * 365 +
			      floor($y div 4) - floor($y div 100) +
			      floor($y div 400) - 32045"/>
</xsl:template>

<xsl:template name="difference-dates">
	<xsl:param name="annee-debut"/>
	<xsl:param name="mois-debut"/>
	<xsl:param name="jour-debut"/>
	<xsl:param name="annee-fin"/>
	<xsl:param name="mois-fin"/>
	<xsl:param name="jour-fin"/>
	
	<xsl:variable name="jj1">
		<xsl:call-template name="calcul-jour-julien">
			<xsl:with-param name="annee" select="$annee-debut"/>
			<xsl:with-param name="mois" select="$mois-debut"/>
			<xsl:with-param name="jour" select="$jour-debut"/>
		</xsl:call-template>
	</xsl:variable>
	<xsl:variable name="jj2">
		<xsl:call-template name="calcul-jour-julien">
			<xsl:with-param name="annee" select="$annee-fin"/>
			<xsl:with-param name="mois" select="$mois-fin"/>
			<xsl:with-param name="jour" select="$jour-fin"/>
		</xsl:call-template>
	</xsl:variable>
	<xsl:value-of select="$jj2 - $jj1"/>
</xsl:template>
</xsl:stylesheet>
