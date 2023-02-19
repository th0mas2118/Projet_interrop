<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:date="http://exslt.org/dates-and-times" xmlns:str="http://exslt.org/strings" extension-element-prefixes="date">
    <xsl:output method='html' encoding="utf-8"/>

    <xsl:template match="/previsions">
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="./css/style2.css"/>
                <title>Document</title>
            </head>
            <body>
                <div class="meteo-card">
                    <xsl:apply-templates select="echeance">
                        <xsl:with-param name="type">now</xsl:with-param>
                    </xsl:apply-templates>
                </div>
                
                <h3>Todays Weather</h3>

                <div class="today-list">
                    <xsl:apply-templates select="echeance">
                        <xsl:with-param name="type">today</xsl:with-param>
                    </xsl:apply-templates>
                </div>
                
                <h3>Next Days</h3>
                
                <div class="next-list">
                    <xsl:apply-templates select="echeance">
                        <xsl:with-param name="type">next</xsl:with-param>
                    </xsl:apply-templates>
                </div>
            </body>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        </html>
    </xsl:template>

    <xsl:variable name="hour" select="date:hour-in-day()" />
    <xsl:variable name="day">
        <xsl:choose>
            <xsl:when test="date:day-in-month() > 10">
                <xsl:value-of select="date:day-in-month()" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="concat('0', date:day-in-month())" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="dayName" select="date:day-name()"></xsl:variable>
    <xsl:variable name="monthName" select="date:month-name()"></xsl:variable>
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
        <xsl:param name="type"/>

        <xsl:choose>
            <xsl:when test="$type = 'now'">
                <xsl:choose>
                    <xsl:when test="starts-with(@timestamp, $formated-date)">
                        <xsl:variable name="eHour" select="str:replace(str:replace(str:replace(str:replace(@timestamp, ' UTC', ''), $formated-date, ''), ' ', ''), ':00', '')"/>
                        <xsl:choose>
                            <!-- HEURES APRES -->
                            
                            <xsl:when test="$eHour > $hour"> 
                                <xsl:choose>
                                    <!-- HEURE LA PLUS PROCHE -->
                                    <xsl:when test="$hour + 3 >= $eHour">
                                        <xsl:call-template name="header">
                                            <xsl:with-param name="echeance" select="."/>
                                            <xsl:with-param name="hour" select="$eHour"/>
                                        </xsl:call-template>                             
                                    </xsl:when>
                                </xsl:choose>
                                <br></br>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:when>
                </xsl:choose>
            </xsl:when>

            <xsl:when test="$type = 'today'">
                <xsl:choose>
                    <xsl:when test="starts-with(@timestamp, $formated-date)">
                        <xsl:variable name="eHour" select="str:replace(str:replace(str:replace(str:replace(@timestamp, ' UTC', ''), $formated-date, ''), ' ', ''), ':00', '')"/>
                        <xsl:choose>
                            <!-- HEURES APRES -->
                            
                            <xsl:when test="$eHour > $hour"> 
                                
                                <xsl:choose>
                                    <!-- HEURE LA PLUS PROCHE -->
                                    <xsl:when test="$hour + 3 >= $eHour">
                       
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:call-template name="today-list">
                                            <xsl:with-param name="echeance" select="."/>
                                            <xsl:with-param name="hour" select="$eHour"/>
                                        </xsl:call-template>   
                                    </xsl:otherwise>
                                </xsl:choose>
                                <br></br>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:when>
                </xsl:choose>  
            </xsl:when>

            <xsl:when test="$type = 'next'">
                <xsl:choose>
                    <xsl:when test="starts-with(@timestamp, $formated-date)">
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:variable name="eHour" select="str:replace(str:split(@timestamp, ' ')[2], ':00', '')" />
                        <xsl:choose>
                            <xsl:when test="$eHour = 13">
                                <xsl:call-template name="next-list">
                                    <xsl:with-param name="echeance" select="."/>
                                    <xsl:with-param name="hour" select="$eHour"/>
                                </xsl:call-template>   
                            </xsl:when>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>  
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="header">
        <xsl:param name="echeance"/>
        <xsl:variable name="tempcelsius" select="format-number($echeance/temperature/level[@val='sol'] - 273.15, '0')"></xsl:variable>

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

        <header>
            <div class="today">
                <div class="title">
                    <h1 id="ville">Nancy</h1>
                    <h2><xsl:value-of select="concat($dayName, ' ', $day, ' ', $monthName)" /></h2>
                    <div class="weather">
                        <div class="weather-icon">
                             <i class="{$icon}"> </i>
                        </div>
                        <h3><xsl:value-of select="$tempcelsius"></xsl:value-of>°C</h3>
                    </div>
                </div>
            </div>
            <div class="data">
                <div class="item">
                    <xsl:value-of select="./vent_moyen/level" /> Km/h
                    <span class="title">Vent moyen</span>
                </div>
                <div class="item">
                    <xsl:value-of select="./pluie" /> mm
                    <span class="title">Pluie</span>
                </div>
                <div class="item">
                    <xsl:value-of select="./humidite" />
                    <span class="title">Humidité</span>
                </div>
                    QAIR
            </div>
        </header>
    </xsl:template>
    
    <xsl:template name="next-list">
        <xsl:param name="echeance"/>
        <xsl:param name="hour"></xsl:param>
        <xsl:variable name="tempcelsius" select="format-number(./temperature/level[@val='sol'] - 273.15, '0')"></xsl:variable>
    
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

        <div class="large-card">
            <xsl:variable name="cDay" select="str:split(str:split(@timestamp, '-')[3], ' ')[1]" />
            <xsl:variable name="cMonth" select="str:split(@timestamp, '-')[2]" />
            <xsl:variable name="cYear" select="str:split(@timestamp, '-')[1]" />
            <xsl:variable name="cDayName" select="date:day-name(concat($cYear, '-', $cMonth, '-', $cDay))" />

            <div class="date">
                <span><xsl:value-of select="$cDayName" /></span>
                <span><xsl:value-of select="$cDay" /></span>
            </div>
            <div class="weather-icon">
                <i class="{$icon}"> </i>
            </div>
            <xsl:value-of select="$tempcelsius" />°C
            <div class="item">
                <xsl:value-of select="./vent_moyen/level" /> Km/h
                <span class="title">Vent moyen</span>
            </div>
            <div class="item">
                <xsl:value-of select="./pluie" /> mm
                <span class="title">Pluie</span>
            </div>
            <div class="item">
                <xsl:value-of select="./humidite" />
                <span class="title">Humidité</span>
            </div>
        </div>
    </xsl:template>

    <xsl:template name="today-list">
        <xsl:param name="echeance"/>
        <xsl:param name="hour"></xsl:param>
        <xsl:variable name="tempcelsius" select="format-number(./temperature/level[@val='sol'] - 273.15, '0')"></xsl:variable>

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

        <div class="mini-card">
            <span>
                <xsl:value-of select="$hour" />h
                <div class="weather-icon">
                     <i class="{$icon}"> </i>
                </div>
                <xsl:value-of select="$tempcelsius" />°C
            </span>
        </div>
    </xsl:template>

    <xsl:output 
    method="xml"
    standalone="no"
    doctype-system="meteo.dtd"
    />


</xsl:stylesheet>