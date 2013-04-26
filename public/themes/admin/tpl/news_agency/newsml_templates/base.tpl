<?xml version="1.0" encoding="utf-8"?>
<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime></DateAndTime>
  </NewsEnvelope>
  <NewsItem Duid="multimedia_{$article->id}">
    <Comment FormalName="OnmNewsMLVersion"><text>1.0.1</text></Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>{setting name=site_name}</ProviderId>
        <DateId>{$article->created_datetime->format('Ymd\THis')}</DateId>
        <NewsItemId>{$article->id}</NewsItemId>
        <RevisionId PreviousRevision="1" Update="U"><text>2</text></RevisionId>
        <PublicIdentifier>{$article->urn}</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <!--Creation date.-->
      <FirstCreated>{$article->created_datetime->format('Ymd\THis')}</FirstCreated>
      <!--Last modification date.-->
      <ThisRevisionCreated>{$article->updated_datetime->format('Ymd\THis')}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_2006824.multimedia">
      <NewsLines>
        <HeadLine>{$article->title|htmlspecialchars_decode}</HeadLine>
        <SubHeadLine>{$article->subtitle|htmlspecialchars_decode}</SubHeadLine>
      </NewsLines>
      <AdministrativeMetadata>
        <Provider>
          <Party FormalName="{setting name=site_name}" />
        </Provider>
        <Creator>
          <Party FormalName="{setting name=site_name}" />
        </Creator>
      </AdministrativeMetadata>
      <!--Text collection.-->
      <NewsComponent Duid="multimedia_{$article->id}.multimedia.texts">
        <Role FormalName="Content list" />
        <NewsComponent Duid="multimedia_{$article->id}.multimedia.texts.{$article->id}" Euid="{$article->id}">
          <Role FormalName="Main" />
          <NewsLines>
            <HeadLine>{$article->title|htmlspecialchars_decode}</HeadLine>
            <SubHeadLine>{$article->subtitle|htmlspecialchars_decode}</SubHeadLine>
          </NewsLines>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>{$article->created_datetime->format('Ymd\THis')}</DateLineDate>
            <Property FormalName="Onm_IdRefObject" Value="{$article->id}" />
          </DescriptiveMetadata>
          <ContentItem>
            <MediaType FormalName="Text" />
            <Format FormalName="NITF" />
            <MimeType FormalName="text/vnd.IPTC.NITF" />
            <DataContent>
              <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                <head>
                  <title>{$article->title|htmlspecialchars_decode}</title>
                  <docdata management-status="usable">
                    <doc-id id-string="{$article->id}" />
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1>{$article->title|htmlspecialchars_decode}</hl1>
                      <hl2>{$article->subtitle|htmlspecialchars_decode}</hl2>
                    </hedline>
                    <dateline>
                      <story.date norm="{$article->created_datetime->format('Ymd\THis')}"><text>{$article->created_datetime->format('Ymd\THis')}</text></story.date>
                    </dateline>
                    <abstract>
                      {$article->summary|unescape:"htmlall"}
                    </abstract>
                  </body.head>
                  <body.content>
                    {$article->body|unescape:"htmlall"}
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
              <Party FormalName="{setting name=site_name}" />
            </Provider>
          </AdministrativeMetadata>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>20130404T121800+0000</DateLineDate>
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