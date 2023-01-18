<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
    <div>
        <p><xsl:value-of select="query/lon"/></p>
        <p><xsl:value-of select="query/lat"/></p>
    </div>
</xsl:template>

</xsl:stylesheet>