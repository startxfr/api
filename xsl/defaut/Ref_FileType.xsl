<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8"/>
<xsl:template name="AnalyseFilename">
<xsl:param name="file"/>
<xsl:param name="type"/>
	<xsl:choose>
		<xsl:when test="string-length(substring-after($file,'.')) = 0">
			<xsl:call-template name="GetDetail">
				<xsl:with-param name="file" select="$file" />
				<xsl:with-param name="type" select="$type" />
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="AnalyseFilename">
				<xsl:with-param name="file" select="substring-after($file,'.')" />
				<xsl:with-param name="type" select="$type" />
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="GetDetail">
<xsl:param name="file"/>
<xsl:param name="type"/>
<xsl:choose>
	<xsl:when test="$file = 'png'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/img.png" title="Image PNG"/></xsl:when>
			<xsl:otherwise>Image PNG</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'gif'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/img.png" title="Image GIF"/></xsl:when>
			<xsl:otherwise>Image GIF</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'jpg')or($file = 'jpeg')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/img.png" title="Image JPEG"/></xsl:when>
			<xsl:otherwise>Image JPEG</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'zip'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/archives.png" title="archive ZIP"/></xsl:when>
			<xsl:otherwise>Archive ZIP</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'tar')or($file = 'gz')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/archives.png" title="archive TAR/GZ"/></xsl:when>
			<xsl:otherwise>Archive Tar/Gz</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'rar'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/archives.png" title="archive RAR"/></xsl:when>
			<xsl:otherwise>Archive RAR</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'mp3'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/audio.png" title="Audio MP3"/></xsl:when>
			<xsl:otherwise>Audio MP3</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'wmf'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/audio.png" title="Audio Windows Media File"/></xsl:when>
			<xsl:otherwise>Audio Windows Media File</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'rm'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/audio.png" title="Audio RealMedia"/></xsl:when>
			<xsl:otherwise>Audio RealMedia</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'ogg'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/audio.png" title="Audio OGG/Vorbis"/></xsl:when>
			<xsl:otherwise>Audio OGG/Vorbis</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'sxf'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/vector.png" title="Animation FLASH"/></xsl:when>
			<xsl:otherwise>Animation Flash</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'ai'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/vector.png" title="Image Illustrator"/></xsl:when>
			<xsl:otherwise>Image Illustrator</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'svg'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/vector.png" title="Image SVG"/></xsl:when>
			<xsl:otherwise>Image SVG</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'mpg')or($file = 'mpeg')or($file = 'divx')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/video.png" title="Video MPEG"/></xsl:when>
			<xsl:otherwise>Video MPEG</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'avi'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/video.png" title="Video AVI"/></xsl:when>
			<xsl:otherwise>Video AVI</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'ram'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/video.png" title="Video RealPlayer"/></xsl:when>
			<xsl:otherwise>Video RealPlayer</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'doc')or($file = 'dot')or($file = 'sxw')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/writer.png" title="Document Writer"/></xsl:when>
			<xsl:otherwise>Document Writer</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'xls')or($file = 'xlt')or($file = 'csv')or($file = 'sxc')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/calc.png" title="Document tableur"/></xsl:when>
			<xsl:otherwise>Document Tableur</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'ppt')or($file = 'sxi')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/presentation.png" title="Document de presentation"/></xsl:when>
			<xsl:otherwise>Document de presentation</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'ps')or($file = 'tex')or($file = 'latex')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/pdf.png" title="Document d'impression"/></xsl:when>
			<xsl:otherwise>Document d'impression</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'pdf'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/pdf.png" title="Document PDF"/></xsl:when>
			<xsl:otherwise>Document PDF</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="($file = 'exe')or($file = 'com')">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/bin.png" title="Executables Windows"/></xsl:when>
			<xsl:otherwise>Executables Windows</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:when test="$file = 'sh'">
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/bin.png" title="Executables UNIX"/></xsl:when>
			<xsl:otherwise>Executables UNIX</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:otherwise>
		<xsl:choose>
			<xsl:when test="$type = 'IMAGE'"><img src="../img/files/unknown.png" title="Format de fichier non connu"/></xsl:when>
			<xsl:otherwise>Format de fichier inconnu</xsl:otherwise>
		</xsl:choose>
	</xsl:otherwise>
</xsl:choose>
</xsl:template>
</xsl:stylesheet>
