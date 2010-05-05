<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="UTF-8" indent="no"/>
    <xsl:include href="../Ref_ZBox.xsl" />
    <xsl:template match="gnoseHistory">

	<xsl:call-template name="generateZBox">
	    <xsl:with-param name="idBox" select="'GnoseHistory'"/>
	    <xsl:with-param name="titre" select="'Historique des modifications du répertoire central'"/>
	    <xsl:with-param name="content">
		<div class="blockTable">
		    <table width="100%" cellspacing="0">
			<tr class="titre">
			    <th colspan="3">Enregistrement</th>
			    <th rowspan="2">Message</th>
			    <th colspan="4" rowspan="2">Modifications</th>
			</tr>
			<tr class="titre">
			    <th><small>n°</small></th>
			    <th><small>user</small></th>
			    <th><small>date</small></th>
			</tr>
			<xsl:apply-templates select="//logentry" mode="Principal">
			    <xsl:sort order="descending" data-type="number" select="@revision"/>
			</xsl:apply-templates>
		    </table>
		</div>
	    </xsl:with-param>
	</xsl:call-template>

    </xsl:template>
    <xsl:template match="logentry" mode="Principal">
	<xsl:variable name="year" select="substring-before(date,'-')"/>
	<xsl:variable name="month" select="substring-before(substring-after(date,'-'),'-')"/>
	<xsl:variable name="day" select="substring-after(substring-after(substring-before(date,'T'),'-'),'-')"/>
	<xsl:variable name="time" select="substring-before(substring-after(date,'T'),'.')"/>
	<xsl:variable name="hour" select="substring-before($time,':')"/>
	<xsl:variable name="minute" select="substring-before(substring-after($time,':'),':')"/>
	<xsl:variable name="FileDelete">
	    <xsl:value-of select="count(paths/path[@action = 'D'])"/>
	</xsl:variable>
	<xsl:variable name="FileAdd">
	    <xsl:value-of select="count(paths/path[@action = 'A'])"/>
	</xsl:variable>
	<xsl:variable name="FileModif">
	    <xsl:value-of select="count(paths/path[@action = 'M'])"/>
	</xsl:variable>
	<xsl:variable name="FileTotal">
	    <xsl:value-of select="count(paths/path)"/>
	</xsl:variable>
	<tr class="altern{position()  mod 2}">
	    <th class="bg"><xsl:attribute name="nowrap"/><xsl:value-of select="@revision"/></th>
	    <th class="bg"><xsl:attribute name="nowrap"/><a href="#" onclick="return zuno.popup.open('../User.php','type=popup&amp;id={author}','550','300','','','','User');" title="Détail sur l'utilisateur" ><b><xsl:value-of select="author"/></b></a></th>
	    <th class="bg barre"><small><xsl:attribute name="nowrap"/><xsl:value-of select="$day"/>/<xsl:value-of select="$month"/>/<xsl:value-of select="$year"/>&#160;<xsl:value-of select="$hour"/>:<xsl:value-of select="$minute"/></small></th>
	    <td class="barre"><i><xsl:value-of select="msg"/></i></td>
	    <td class="barre right"><b><xsl:value-of select="$FileTotal"/></b><xsl:text> </xsl:text><small>fichiers</small></td>
	    <td class="bg right" width="20"><xsl:attribute name="nowrap"/>
		<a href="History.php?rev={@revision}" title="Détail de cet enregistrement">
		<img src="../img/gnose/history.png" alt="Détail de cet enregistrement"/></a>
	    </td>
	</tr>
    </xsl:template>
</xsl:stylesheet>
