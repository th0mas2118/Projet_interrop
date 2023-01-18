<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:date="http://exslt.org/dates-and-times" extension-element-prefixes="date">
    <xsl:output method='html' encoding="utf-8"/>

    <xsl:template match="/previsions">
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="./css/style.css"/>
                <title>Document</title>
            </head>
            <body>
                <h1>Météo</h1>
                <div class="meteo-card">
                    <xsl:apply-templates select="echeance">

                    </xsl:apply-templates>
                </div>
            </body>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        </html>
    </xsl:template>

    <xsl:variable name="hour" select="date:hour-in-day()" />
    <xsl:variable name="day" select="date:day-in-month()"></xsl:variable>
    <xsl:variable name="month">
        <xsl:choose>
            <xsl:when test="date:month-in-year() > 10">
                <xsl:value-of select="date:month-in-year()" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="concat('0', date:month-in-year())" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="year" select="date:year()"></xsl:variable>
    <xsl:variable name="formated-date" select="concat($year,'-',$month,'-',$day)"></xsl:variable>

    <xsl:template match="echeance">  
        <xsl:choose>
            <xsl:when test="starts-with(@timestamp, $formated-date)">
                <xsl:choose>
                    <!-- HEURES APRES -->
                    <xsl:when test="@hour > $hour"> 
                        <xsl:choose>
                            <!-- HEURE LA PLUS PROCHE -->
                            <xsl:when test="$hour + 3 >= @hour">
                                <xsl:call-template name="header">
                                    <xsl:with-param name="echeance" select="."/>
                                </xsl:call-template>                             
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="@timestamp" />
                            </xsl:otherwise>
                        </xsl:choose>
                        <br></br>
                    </xsl:when>
                </xsl:choose>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="header">
        <xsl:param name="echeance"/>
        <xsl:variable name="tempcelsius" select="format-number($echeance/temperature/level[@val='sol'] - 273.15, '0.00')"></xsl:variable>

        <xsl:variable name="icon">
            <xsl:choose>
                <xsl:when test="$echeance/pluie > 0">
                    <xsl:choose>
                        <xsl:when test="$echeance/risque_neige = 'oui'">
                            fa-solid fa-snowflake
                        </xsl:when>
                        <xsl:otherwise>
                            fa-solid fa-cloud-rain
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    fa-solid fa-sun
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="bg">
            <xsl:choose>
                <xsl:when test="$echeance/pluie > 0">
                    <xsl:choose>
                        <xsl:when test="$echeance/risque_neige = 'oui'">
                            snow
                        </xsl:when>
                        <xsl:otherwise>
                            rain
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    sun
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <div class="bg {$bg}">
             
        </div>
        <header>
            <div class="weather-icon">
                 <i class="{$icon}"> </i>
            </div>
            <h2>Nancy</h2>
            <h3><xsl:value-of select="$tempcelsius"></xsl:value-of>°C</h3>
        </header>
    </xsl:template>

    <xsl:output 
    method="xml"
    standalone="no"
    doctype-system="meteo.dtd"
    />


</xsl:stylesheet>