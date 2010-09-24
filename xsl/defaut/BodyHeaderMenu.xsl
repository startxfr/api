<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:include href="Ref_SXToolkit.xsl" />
    <xsl:output method="text" encoding="UTF-8"/>
    <xsl:param name="droit"/>
    <xsl:param name="tmpPath"/>
    <xsl:param name="tmpMenuPrefix"/>
    <xsl:param name="tmpMenuSuffix"/>
    <xsl:param name="selectedChannel"/>
    <xsl:param name="lang"/>
	
	
	<!--
		TEMPLATE DU ROOT ELEMENT
	-->
    <xsl:template match="configuration">
        <xsl:apply-templates select="group[@name = 'CHANNEL_list']" mode="channel"/>
    </xsl:template>

	<!--
		TEMPLATE DU GROUPE DES CHANNELS
	-->
    <xsl:template match="group" mode="channel">
        <ul class="level1" id="menuTree">
            <xsl:text> </xsl:text>
            <xsl:apply-templates  select="value" mode="channel"/>
        </ul>
    </xsl:template>

	<!--
		TEMPLATE DE CHAQUE CHANNEL
	-->
    <xsl:template match="value" mode="channel">
        <xsl:param name="channel">
            <xsl:value-of select="concat('CHANNEL_',@id)"/>
        </xsl:param>
        <xsl:param name="channel_name">
            <xsl:value-of select="concat('',.)"/>
        </xsl:param>
        <xsl:apply-templates select="//group[@name = $channel]" mode="channelDetail">
            <xsl:with-param name="channel" select="$channel"/>
            <xsl:with-param name="channel_name" select="$channel_name"/>
            <xsl:with-param name="id" select="@id"/>
        </xsl:apply-templates>
    </xsl:template>

	<!--
		TEMPLATE DU DETAIL DE CHAQUE CHANNEL
	-->
    <xsl:template match="group" mode="channelDetail">
        <xsl:param name="channel"/>
        <xsl:param name="channel_name"/>
        <xsl:param name="position"/>
        <xsl:param name="id"/>
        <xsl:param name="selectedChannel" select="$selectedChannel"/>
        <xsl:param name="droit" select="$droit"/>
        <xsl:param name="lang" select="$lang"/>
        <xsl:variable name="displayForRight">
            <xsl:call-template name="displayForRight">
                <xsl:with-param name="rightList" select="concat(value[@id = 'RequiredRight'],',,')"/>
                <xsl:with-param name="droit" select="$droit"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="uriPrefix">
            <xsl:if test="$selectedChannel != 'normal'">
                <xsl:value-of select="'../'"/>
            </xsl:if>
        </xsl:variable>
        <xsl:variable name="classForSelected">
            <xsl:if test="$selectedChannel = $id">
                <xsl:value-of select="' sel'"/>
            </xsl:if>
        </xsl:variable>
        <xsl:if test="$displayForRight = 'ok' and value[@id = 'isOnMenu'] != 'false'">
            <li id="{$id}">
                <xsl:if test="$id = 'prospec'">
                    <xsl:attribute name="class">
                        <xsl:text>first</xsl:text>
                    </xsl:attribute>
                </xsl:if>
                <xsl:if test="$id = 'gnose'">
                    <xsl:if test="$droit > 1">
                        <xsl:attribute name="class">
                            <xsl:text>last</xsl:text>
                        </xsl:attribute>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="$id = 'admin'">
                    <xsl:attribute name="class">
                        <xsl:text>last</xsl:text>
                    </xsl:attribute>
                </xsl:if>
                <a href="{$uriPrefix}{value[@id = 'path']}" title="{$channel_name}" class="h2{$classForSelected}">
                    <xsl:if test="string-length(value[@id = 'icone']) &gt; 0">
                        <xsl:apply-templates  select="value[@id = 'icone']" mode="icone">
                            <xsl:with-param name="channel_name" select="$channel_name"/>
                        </xsl:apply-templates>
                    </xsl:if>
                    <xsl:value-of select="$channel_name"/>
                </a>
                <xsl:call-template name="getChannelMenu">
                    <xsl:with-param name="channel" select="$id" />
                    <xsl:with-param name="uriPrefix" select="concat($uriPrefix,value[@id = 'path'])" />
                </xsl:call-template>
            </li>
        </xsl:if>
    </xsl:template>
	
	<!--
		TEMPLATE DES ICONES DES CHANNELS
	-->
    <xsl:template match="value" mode="icone">
        <xsl:param name="channel_name"/>
        <img src="{.}" hspace="4" alt="{$channel_name}"/>&#160;&#160;
    </xsl:template>
	
	
	<!--
		TEMPLATE DE RECUPERATION DU FICHIER XML D'UN MENU
	-->
	
    <xsl:template name="getChannelMenu">
        <xsl:param name="tmpPath" select="$tmpPath"/>
        <xsl:param name="tmpMenuPrefix" select="$tmpMenuPrefix"/>
        <xsl:param name="tmpMenuSuffix" select="$tmpMenuSuffix"/>
        <xsl:param name="lang" select="$lang"/>
        <xsl:param name="droit" select="$droit"/>
        <xsl:param name="channel"/>
        <xsl:param name="uriPrefix"/>
        <xsl:param name="menuFile" select="concat($tmpPath,$tmpMenuPrefix,$channel,'.',$lang,$tmpMenuSuffix)" />
        <ul class="level2">
            <xsl:apply-templates select="document($menuFile)//menutree" mode="niv0">
                <xsl:with-param name="channel" select="$channel"/>
                <xsl:with-param name="uriPrefix" select="$uriPrefix"/>
            </xsl:apply-templates>
            <xsl:text>&#160;</xsl:text>
        </ul>
    </xsl:template>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<!--
		TEMPLATE DU ROOT ELEMENT
	-->
    <xsl:template match="menutree" mode="niv0">
        <xsl:param name="uriPrefix"/>
        <xsl:apply-templates select="menu[@actif = '1']">
            <xsl:sort select="@order" order="ascending"/>
            <xsl:with-param name="uriPrefix" select="$uriPrefix"/>
        </xsl:apply-templates>
    </xsl:template>
	
	
	
	<!--
		TEMPLATE DU PREMIER NIVEAU DE MENU
	-->
    <xsl:template match="menu">
        <xsl:param name="uriPrefix"/>
        <xsl:param name="droit" select="$droit"/>
        <xsl:variable name="displayForRight">
            <xsl:call-template name="displayForRight">
                <xsl:with-param name="rightList" select="@droit"/>
                <xsl:with-param name="droit" select="$droit"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:if test="@menuon = 1 and  $displayForRight = 'ok'">
            <li id="{@id}">
                <a href="{$uriPrefix}{uri}" title="{header}">
                    <xsl:if test="string-length(icone) &gt; 0">
                        <xsl:apply-templates  select="icone"/>
                    </xsl:if>
                    <xsl:value-of select="nom"/>
                </a>
                <xsl:apply-templates select="submenu">
                    <xsl:with-param name="uriPrefix" select="$uriPrefix"/>
                </xsl:apply-templates>
            </li>
        </xsl:if>
    </xsl:template>
	
	<!--
		TEMPLATE D'AFFICHAGE DES ICONES
	-->
    <xsl:template match="icone">
        <img src="{.}" hspace="4" alt="{../header}"/>
    </xsl:template>
	
	
	
	
	<!--
		TEMPLATE D'AFFICHAGE DES SUBMENU
	-->
    <xsl:template match="submenu">
        <xsl:param name="uriPrefix"/>
        <ul class="level3">
            <xsl:apply-templates select="menu[@actif = '1']">
                <xsl:sort select="@order" order="ascending"/>
                <xsl:with-param name="uriPrefix" select="$uriPrefix"/>
            </xsl:apply-templates>
            <xsl:text>&#160;</xsl:text>
        </ul>
    </xsl:template>

</xsl:stylesheet>
