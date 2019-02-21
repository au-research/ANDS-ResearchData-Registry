<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:ro="http://ands.org.au/standards/rif-cs/registryObjects"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:mdb="http://standards.iso.org/iso/19115/-3/mdb/1.0"
    xmlns:cat="http://standards.iso.org/iso/19115/-3/cat/1.0"
    xmlns:cit="http://standards.iso.org/iso/19115/-3/cit/1.0"
    xmlns:gco="http://standards.iso.org/iso/19115/-3/gco/1.0"
    xmlns:gcx="http://standards.iso.org/iso/19115/-3/gcx/1.0"
    xmlns:gex="http://standards.iso.org/iso/19115/-3/gex/1.0"
    xmlns:gfc="http://standards.iso.org/iso/19110/gfc/1.1"
    xmlns:gml="http://www.opengis.net/gml/3.2"
    xmlns:lan="http://standards.iso.org/iso/19115/-3/lan/1.0"
    xmlns:mac="http://standards.iso.org/iso/19115/-3/mac/1.0"
    xmlns:mas="http://standards.iso.org/iso/19115/-3/mas/1.0"
    xmlns:mcc="http://standards.iso.org/iso/19115/-3/mcc/1.0"
    xmlns:mco="http://standards.iso.org/iso/19115/-3/mco/1.0"
    xmlns:mda="http://standards.iso.org/iso/19115/-3/mda/1.0"
    xmlns:mdq="http://standards.iso.org/iso/19157/-2/mdq/1.0"
    xmlns:mds="http://standards.iso.org/iso/19115/-3/mds/1.0"
    xmlns:mdt="http://standards.iso.org/iso/19115/-3/mdt/1.0"
    xmlns:mex="http://standards.iso.org/iso/19115/-3/mex/1.0"
    xmlns:mmi="http://standards.iso.org/iso/19115/-3/mmi/1.0"
    xmlns:mpc="http://standards.iso.org/iso/19115/-3/mpc/1.0"
    xmlns:mrc="http://standards.iso.org/iso/19115/-3/mrc/1.0"
    xmlns:mrd="http://standards.iso.org/iso/19115/-3/mrd/1.0"
    xmlns:mri="http://standards.iso.org/iso/19115/-3/mri/1.0"
    xmlns:mrl="http://standards.iso.org/iso/19115/-3/mrl/1.0"
    xmlns:mrs="http://standards.iso.org/iso/19115/-3/mrs/1.0"
    xmlns:msr="http://standards.iso.org/iso/19115/-3/msr/1.0"
    xmlns:srv="http://standards.iso.org/iso/19115/-3/srv/2.0" exclude-result-prefixes="ro">

    <!-- stylesheet to convert RIFCS service xml to ISO19115-3 service record -->

    <xsl:output method="xml" omit-xml-declaration="yes" version="1.0" encoding="UTF-8" indent="yes"/>

    <xsl:strip-space elements="*"/>

    <xsl:template match="/ | ro:registryObjects | ro:registryObject">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="ro:service">
        <mdb:MD_Metadata>
            <xsl:attribute name="xsi:schemaLocation">
                <xsl:text>http://standards.iso.org/iso/19115/-3/gco/1.0 ../../../etc/schema/19115/-3/gco/1.0/gco.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/cat/1.0 ../../../etc/schema/19115/-3/cat/1.0/cat.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/cit/1.0 ../../../etc/schema/19115/-3/cit/1.0/cit.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/gcx/1.0 ../../../etc/schema/19115/-3/gcx/1.0/gcx.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/gex/1.0 ../../../etc/schema/19115/-3/gex/1.0/gex.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/lan/1.0 ../../../etc/schema/19115/-3/lan/1.0/lan.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/srv/2.0 ../../../etc/schema/19115/-3/srv/2.0/srv.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mas/1.0 ../../../etc/schema/19115/-3/mas/1.0/mas.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mcc/1.0 ../../../etc/schema/19115/-3/mcc/1.0/mcc.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mco/1.0 ../../../etc/schema/19115/-3/mco/1.0/mco.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mda/1.0 ../../../etc/schema/19115/-3/mda/1.0/mda.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mdb/1.0 ../../../etc/schema/19115/-3/mdb/1.0/mdb.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mds/1.0 ../../../etc/schema/19115/-3/mds/1.0/mds.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mdt/1.0 ../../../etc/schema/19115/-3/mdt/1.0/mdt.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mex/1.0 ../../../etc/schema/19115/-3/mex/1.0/mex.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mmi/1.0 ../../../etc/schema/19115/-3/mmi/1.0/mmi.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mpc/1.0 ../../../etc/schema/19115/-3/mpc/1.0/mpc.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mrc/1.0 ../../../etc/schema/19115/-3/mrc/1.0/mrc.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mrd/1.0 ../../../etc/schema/19115/-3/mrd/1.0/mrd.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mri/1.0 ../../../etc/schema/19115/-3/mri/1.0/mri.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mrl/1.0 ../../../etc/schema/19115/-3/mrl/1.0/mrl.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mrs/1.0 ../../../etc/schema/19115/-3/mrs/1.0/mrs.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/msr/1.0 ../../../etc/schema/19115/-3/msr/1.0/msr.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19157/-2/mdq/1.0 ../../../etc/schema/19157/-2/mdq/1.0/mdq.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19115/-3/mac/1.0 ../../../etc/schema/19115/-3/mac/1.0/mac.xsd</xsl:text>
                <xsl:text> http://standards.iso.org/iso/19110/gfc/1.1 ../../../etc/schema/19110/gfc/1.1/gfc.xsd</xsl:text>
                <xsl:text> http://www.opengis.net/gml/3.2 ../../../etc/schema/19136/gml.xsd</xsl:text>
                <xsl:text> http://www.w3.org/1999/xlink ../../../etc/schema/xlink.xsd</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates select="ro:identifier"/>
            <xsl:call-template name="addScope">
                <xsl:with-param name="scope" select="local-name()"/>
            </xsl:call-template>
            <!---

     FILL THIS IN
                        -->
            <xsl:element name="mdb:contact">
                <xsl:choose>
                    <xsl:when
                        test="ro:location/ro:address/ro:physical | ro:location/ro:address/ro:electronic">
                        <xsl:element name="cit:CI_Responsibility">
                            <xsl:element name="cit:role">
                                <xsl:element name="cit:CI_RoleCode">
                                    <xsl:attribute name="codeList">https://standards.iso.org/iso/19115/resources/Codelists/cat/codelists.xml#CI_RoleCode</xsl:attribute>
                                    <xsl:attribute name="codeListValue">pointOfContact</xsl:attribute>
                                </xsl:element>
                            </xsl:element>
                            <xsl:element name="cit:party">
                                <xsl:element name="cit:CI_Organisation">
                                    <xsl:element name="cit:name">
                                        <xsl:element name="gco:CharacterString">
                                            <xsl:apply-templates select="//ro:addressPart[position() = 1]" mode="partyName"/>
                                        </xsl:element>
                                    </xsl:element>
                                    <xsl:element name="cit:contactInfo">
                                        <xsl:element name="cit:CI_Contact">
                                            <xsl:apply-templates
                                              select="ro:location/ro:address/ro:physical/ro:addressPart[@type = 'telephoneNumber' or @type='faxNumber']"/>
                                            <xsl:element name="cit:address">
                                              <xsl:element name="cit:CI_Address">
                                              <xsl:apply-templates select="ro:location/ro:address/ro:physical/ro:addressPart[position() > 1]" mode="deliveryPoint"/>
                                              <xsl:apply-templates select="ro:location/ro:address/ro:electronic[@type = 'email']"/>
                                              </xsl:element>
                                            </xsl:element>
                                        </xsl:element>
                                    </xsl:element>
                                </xsl:element>
                            </xsl:element>
                         </xsl:element>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:element name="cit:CI_Responsibility">
                            <xsl:element name="cit:role"/>
                            <xsl:element name="cit:party"/>
                        </xsl:element>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:element>
            <xsl:element name="mdb:dateInfo">
                <!--  FILL  -->
            </xsl:element>

            <xsl:call-template name="addMetadataStandard"/>
            <xsl:element name="mdb:identificationInfo">
                <xsl:element name="srv:SV_ServiceIdentification">
                    <xsl:element name="mri:citation">
                        <xsl:element name="cit:CI_Citation">
                            <xsl:element name="cit:title">
                                <xsl:apply-templates select="ro:name"/>
                            </xsl:element>
                        </xsl:element>
                    </xsl:element>
                    <xsl:apply-templates select="ro:description"/>
                    <xsl:apply-templates select="ro:coverage/ro:spatial"/>
                    <xsl:if test="ro:subject">
                        <xsl:element name="mri:descriptiveKeywords">
                            <xsl:element name="mri:MD_Keywords">
                                <xsl:apply-templates select="ro:subject"/>
                            </xsl:element>
                        </xsl:element>
                    </xsl:if>
                    <xsl:choose>
                        <xsl:when test="ro:rights">
                            <xsl:apply-templates select="ro:rights"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:element name="mri:resourceConstraints">
                                <xsl:element name="mco:MD_Constraints">
                                    <xsl:element name="mco:useLimitation">
                                        <xsl:element name="gco:CharacterString">none</xsl:element>
                                    </xsl:element>
                                </xsl:element>
                            </xsl:element>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:element name="srv:serviceType">
                       <xsl:element name="gco:ScopedName">
                            <xsl:value-of select="@type"/>
                       </xsl:element>
                    </xsl:element>
                    <xsl:apply-templates select="ro:relatedInfo"/>
                </xsl:element>
            </xsl:element>
        </mdb:MD_Metadata>
    </xsl:template>

    <xsl:template match="ro:addressPart[@type = 'telephoneNumber']">
        <xsl:element name="cit:phone">
            <xsl:element name="cit:CI_Telephone">
                <xsl:element name="cit:number">
                    <xsl:element name="gco:CharacterString">
                        <xsl:value-of select="."/>
                    </xsl:element>
                </xsl:element>
                        <xsl:element name="cit:numberType">
                            <xsl:element name="cit:CI_TelephoneTypeCode">
                                <xsl:attribute name="codeList">https://standards.iso.org/iso/19115/resources/Codelists/cat/codelists.xml#CI_TelephoneTypeCode</xsl:attribute>
                                <xsl:attribute name="codeListValue">fax</xsl:attribute>
                            </xsl:element>
                        </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:addressPart[@type = 'faxNumber']">
        <xsl:element name="cit:phone">
            <xsl:element name="cit:CI_Telephone">
                <xsl:element name="cit:number">
                    <xsl:element name="gco:CharacterString">
                        <xsl:value-of select="."/>
                    </xsl:element>
                </xsl:element>
                        <xsl:element name="cit:numberType">
                            <xsl:element name="cit:CI_TelephoneTypeCode">
                                <xsl:attribute name="codeList">https://standards.iso.org/iso/19115/resources/Codelists/cat/codelists.xml#CI_TelephoneTypeCode</xsl:attribute>
                                <xsl:attribute name="codeListValue">fax</xsl:attribute>
                            </xsl:element>
                        </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:addressPart" mode="deliveryPoint">
        <xsl:element name="cit:deliveryPoint">
            <xsl:element name="gco:CharacterString">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:addressPart" mode="partyName">
        <xsl:value-of select="."/>
    </xsl:template>




    <xsl:template match="ro:electronic[@type = 'email']">
        <xsl:element name="cit:electronicMailAddress">
            <xsl:element name="gco:CharacterString">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:spatial">
        <xsl:variable name="lowText"
            select="translate(text(), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>
        <xsl:element name="mri:extent">
            <xsl:element name="gex:EX_Extent">
                <xsl:element name="gex:geographicElement">
                    <xsl:element name="gex:EX_GeographicBoundingBox">
                        <xsl:element name="gex:westBoundLongitude">
                            <xsl:element name="gco:Decimal">
                                <xsl:value-of select="substring-before(substring-after($lowText, 'westlimit='), ';')"/>
                            </xsl:element>
                        </xsl:element>
                        <xsl:element name="gex:eastBoundLongitude">
                            <xsl:element name="gco:Decimal">
                                <xsl:value-of select="substring-before(substring-after($lowText, 'eastlimit='), ';')"/>
                            </xsl:element>
                        </xsl:element>
                        <xsl:element name="gex:southBoundLatitude">
                            <xsl:element name="gco:Decimal">
                                <xsl:value-of select="substring-before(substring-after($lowText, 'southlimit='), ';')"/>
                            </xsl:element>
                        </xsl:element>
                        <xsl:element name="gex:northBoundLatitude">
                            <xsl:element name="gco:Decimal">
                                <xsl:value-of select="substring-before(substring-after($lowText, 'northlimit='), ';')"/>
                            </xsl:element>
                        </xsl:element>
                    </xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>


    <xsl:template match="ro:name">
        <xsl:element name="gco:CharacterString">
            <xsl:apply-templates select="ro:namePart"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:namePart">
        <xsl:value-of select="text()"/>
    </xsl:template>


    <xsl:template match="ro:rights">
        <xsl:element name="mri:resourceConstraints">
            <xsl:element name="mco:MD_Constraints">
                <xsl:element name="mco:useLimitation">
                    <xsl:element name="gco:CharacterString">
                        <xsl:value-of select="."/>
                    </xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:description">
        <xsl:element name="mri:abstract">
            <xsl:element name="gco:CharacterString">
                <xsl:value-of select="text()"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:subject">
        <xsl:element name="mri:keyword">
            <xsl:element name="gco:CharacterString">
                <xsl:value-of select="text()"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:relatedInfo">
        <xsl:element name="srv:operatesOn">
            <xsl:attribute name="uuidref">
                <xsl:apply-templates select="ro:identifier" mode="justValue"/>
            </xsl:attribute>
            <xsl:attribute name="xlink:href">
                <xsl:apply-templates select="ro:identifier" mode="getByIdRef"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>

    <xsl:template match="ro:identifier" mode="justValue">
        <xsl:value-of select="text()"/>
    </xsl:template>

    <xsl:template match="ro:identifier" mode="getByIdRef">
        <xsl:value-of select="text()"/>
    </xsl:template>

    <xsl:template match="ro:identifier">
        <xsl:element name="mdb:metadataIdentifier">
            <xsl:element name="mcc:MD_Identifier">
                <xsl:element name="mcc:authority">
                    <xsl:element name="cit:CI_Citation">
                        <xsl:element name="cit:title">
                            <xsl:element name="gco:CharacterString">GeoNetwork UUID</xsl:element>
                        </xsl:element>
                    </xsl:element>
                </xsl:element>
                <xsl:element name="mcc:code">
                    <xsl:element name="gco:CharacterString">
                        <xsl:value-of select="text()"/>
                    </xsl:element>
                </xsl:element>
                <xsl:element name="mcc:codeSpace">
                    <xsl:element name="gco:CharacterString">urn:uuid</xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template name="addScope">
        <xsl:param name="scope" select="'collection'"/>
        <xsl:element name="mdb:metadataScope">
            <xsl:element name="mdb:MD_MetadataScope">
                <xsl:element name="mdb:resourceScope">
                    <xsl:element name="mcc:MD_ScopeCode">
                        <xsl:attribute name="codeList">codeListLocation#MD_ScopeCode</xsl:attribute>
                        <xsl:attribute name="codeListValue">
                            <xsl:value-of select="$scope"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:element>
                <xsl:element name="mdb:name">
                    <xsl:element name="gco:CharacterString">
                        <xsl:value-of select="$scope"/>
                    </xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template name="addMetadataStandard">
        <xsl:element name="mdb:metadataStandard">
            <xsl:element name="cit:CI_Citation">
                <xsl:element name="cit:title">
                    <xsl:element name="gco:CharacterString">ISO 19115-3 (Draft Schemas
                        2015)</xsl:element>
                </xsl:element>
                <xsl:element name="cit:editionDate">
                    <xsl:element name="gco:DateTime">2015-07-01T00:00:00</xsl:element>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="node()"/>

</xsl:stylesheet>