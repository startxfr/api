<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<!-- TEMPLATE D'INVERTION DE CHAINE PAR DYCHOTOMIE -->
<xsl:template name="invertion">
	<xsl:param name="input"/>
	<xsl:variable name="length" select="string-length($input)"/>
	<xsl:choose>
		<xsl:when test="$length &lt; 2">
			<xsl:value-of select="$input"/>
		</xsl:when>
		<xsl:when test="$length = 2">
			<xsl:value-of select="substring($input,2,1)"/>
			<xsl:value-of select="substring($input,1,1)"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="mil" select="floor($length div 2)"/>
			<xsl:call-template name="invertion">
				<xsl:with-param name="input" select="substring($input,$mil+1,$mil+1)"/>
			</xsl:call-template>
			<xsl:call-template name="invertion">
				<xsl:with-param name="input" select="substring($input,1,$mil)"/>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>



<xsl:template name="escapeQuotes">
	<xsl:param name="string"/>
	<xsl:param name="apos">'</xsl:param>
	<xsl:if test="not(contains($string,$apos))">
		<xsl:value-of select="$string"/>
	</xsl:if>
	<xsl:if test="contains($string,$apos)">
		<xsl:value-of select="substring-before($string,$apos)"/>
		<xsl:text>&apos;</xsl:text>
		<xsl:call-template name="escapeQuotes">
			<xsl:with-param name="string" select="substring-after($string,$apos)"/>
		</xsl:call-template>
	</xsl:if>
</xsl:template>



<xsl:template name="displayForRight">
	   <xsl:param name="rightList"/>
	   <xsl:param name="droit"/>
	   <xsl:choose>
		<!-- la page a un droit, ce droit est une liste -->
		<xsl:when test="(string-length($rightList) &gt; 0) and (string-length(substring-after($rightList,',')) &gt; 0)">
			<!-- l'utilisateur a un droit et
			     ce droit est contenu dans la liste (on cherche ',42,') -->
			<xsl:if test="(string-length($droit) &gt; 0) and 
					((string-length(substring-after($rightList,concat(',',$droit,','))) &gt; 0) or
					 (substring($rightList,1,string-length(concat($droit,','))) = concat($droit,',')))">
					<xsl:text>ok</xsl:text>
			</xsl:if>
		</xsl:when>
		<!-- la page a un droit, ce droit est un droit simple -->
		<xsl:when test="(string-length($rightList) &gt; 0) and (string-length(substring-after($rightList,',')) = 0)">
			<!-- l'utilisateur a un droit et il est egal au droit de la page -->
			<xsl:if test="(string-length($droit) &gt; 0) and ($rightList >= $droit)">
				<xsl:text>ok</xsl:text>
			</xsl:if>
		</xsl:when>
		<!-- la page n'a pas de droit -->
		<xsl:otherwise>
			<xsl:text>ok</xsl:text>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
</xsl:stylesheet>
