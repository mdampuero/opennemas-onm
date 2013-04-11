<?xml version="1.0" encoding="utf-8"?>
<NewsML Version="1.2">
  <!--NewsML documentation and last changes: http://www.efe.com/documentosefe/efenewsml/EfeNewsML.htm.
Documentacion NewsML y últimos cambios: http://www.efe.com/documentosefe/efenewsml/EfeNewsML.htm-->
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="EFE">
        <Property FormalName="Organization" Value="Agencia EFE" />
      </Party>
    </SentFrom>
    <DateAndTime>{$article->created_datetime->format(DateTime::ISO8601)}</DateAndTime>
  </NewsEnvelope>
  <!--** SUGGESTION FOR DEVS: Use 'Duid' attribute to help you on XPath navigation.-->
  <NewsItem Duid="multimedia_2006824">
    <Comment FormalName="EfeNewsMLVersion">1.0.1</Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>multimedia.efeservicios.com</ProviderId>
        <!--Creation date.-->
        <DateId>{$article->created_datetime->format(DateTime::ISO8601)}</DateId>
        <!--Id of the news for THIS format.-->
        <NewsItemId>2006824</NewsItemId>
        <!--Use 'RevisionId' tags to check changes from the previous version of the news item.-->
        <RevisionId PreviousRevision="1" Update="U">2</RevisionId>
        <!--Usefull to check changes between versions on same news item. See NewsML documentation.-->
        <PublicIdentifier>{$article->urn}</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <!--Creation date.-->
      <FirstCreated>{$article->created_datetime->format(DateTime::ISO8601)}</FirstCreated>
      <!--Last modification date.-->
      <ThisRevisionCreated>{$article->updated_datetime->format(DateTime::ISO8601)}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_2006824.multimedia">
      <NewsLines>
        <HeadLine>{$article->title}</HeadLine>
        <SubHeadLine>{$article->subtitle}</SubHeadLine>
      </NewsLines>
      <AdministrativeMetadata>
        <Provider>
          <Party FormalName="Agencia EFE" />
        </Provider>
        <Creator>
          <Party FormalName="Agencia EFE" />
        </Creator>
      </AdministrativeMetadata>
      <!--Text collection.-->
      <NewsComponent Duid="multimedia_{$article->id}.multimedia.texts">
        <Role FormalName="Content list" />
        <NewsComponent Duid="multimedia_{$article->id}.multimedia.texts.{$article->id}" Euid="{$article->id}">
          <Role FormalName="Main" />
          <NewsLines>
            <HeadLine>{$article->title}</HeadLine>
            <SubHeadLine>{$article->subtitle}</SubHeadLine>
          </NewsLines>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>{$article->created_datetime->format(DateTime::ISO8601)}</DateLineDate>
            <Property FormalName="EFE_IdRefObject" Value="{$article->id}" />
          </DescriptiveMetadata>
          <ContentItem>
            <MediaType FormalName="Text" />
            <Format FormalName="NITF" />
            <MimeType FormalName="text/vnd.IPTC.NITF" />
            <DataContent>
              <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                <head>
                  <title>{$article->title}</title>
                  <docdata management-status="usable">
                    <doc-id id-string="5232938" />
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1>{$article->title}</hl1>
                      <hl2>{$article->subtitle}</hl2>
                    </hedline>
                    <dateline>
                      <story.date norm="{$article->created_datetime->format(DateTime::ISO8601)}">{$article->created_datetime->format(DateTime::ISO8601)}</story.date>
                    </dateline>
                    <abstract>
                      {$article->summary}
                    </abstract>
                  </body.head>
                  <body.content>
                    {$article->content}
                  </body.content>
                </body>
              </nitf>
            </DataContent>
          </ContentItem>
        </NewsComponent>
      </NewsComponent>
      <!--Photo collection.-->
      {*<NewsComponent Duid="multimedia_2006824.multimedia.photos">
        <Role FormalName="Content list" />
        <NewsComponent Duid="multimedia_2006824.multimedia.photos.5232934" Euid="5232934">
          <NewsLines>
            <HeadLine>{$article->title}</HeadLine>
          </NewsLines>
          <AdministrativeMetadata>
            <Provider>
              <Party FormalName="Agencia EFE" />
            </Provider>
          </AdministrativeMetadata>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>20130404T121800+0000</DateLineDate>
            <Location HowPresent="Origin">
              <Property FormalName="City" Value="Santiago de Compostela" />
            </Location>
            <!--Useful to identificate NewsComponents from the same kind (photo, audio, ...) and content, but with different Euids or Hrefs.
Util para identificar NewsComponents del mismo tipo y contenido, pero con diferente Euids o Hrefs.-->
            <Property FormalName="EFE_IdRefObject" Value="5232934" />
          </DescriptiveMetadata>
          <!--Photo binary data. Different sizes/formats about the same photo.-->
          <NewsComponent Duid="multimedia_2006824.multimedia.photos.5232934.file">
            <Role FormalName="Main" />
            <ContentItem Href="5232934m.jpg">
              <MediaType FormalName="Photo" />
              <MimeType FormalName="image/jpeg" />
              <Characteristics>
                <SizeInBytes>42699</SizeInBytes>
                <Property FormalName="Height" Value="362" />
                <Property FormalName="PixelDepth" Value="24" />
                <Property FormalName="Width" Value="550" />
              </Characteristics>
            </ContentItem>
          </NewsComponent>
        </NewsComponent>
      </NewsComponent>*}
    </NewsComponent>
  </NewsItem>
</NewsML>