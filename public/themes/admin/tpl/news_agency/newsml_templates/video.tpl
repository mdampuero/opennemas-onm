<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime>{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateAndTime>
    <NewsProduct FormalName="{$video->author_name}" />
  </NewsEnvelope>
  <NewsItem Duid="video_{$video->id}">
    <Comment FormalName="EfeNewsMLVersion">1.0.1</Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>video.opennemas.com</ProviderId>
        <DateId>{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateId>
        <NewsItemId>{$video->id}</NewsItemId>
        <RevisionId PreviousRevision="0" Update="N">1</RevisionId>
        <PublicIdentifier>urn:newsml:video.opennemas.com:{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}:{$video->id}:1</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <FirstCreated>{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}</FirstCreated>
      <FirstPublished>{format_date date=$video->starttime type="custom" format="yMMdd'T'HHmmssxxx"}</FirstPublished>
      <ThisRevisionCreated>{format_date date=$video->changed type="custom" format="yMMdd'T'HHmmssxxx"}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="video_{$video->id}.video">
      <NewsLines>
        <HeadLine>
          <![CDATA[{$video->title}]]>
        </HeadLine>
        <SubHeadLine>
          <![CDATA[{$video->description}]]>
        </SubHeadLine>
      </NewsLines>
      <AdministrativeMetadata>
        <Provider>
          <Party FormalName="{setting name=site_name}" />
        </Provider>
        <Creator>
          <Party FormalName="{setting name=site_name}" />
        </Creator>
      </AdministrativeMetadata>
      <DescriptiveMetadata>
        <Language FormalName="es" />
        <Property FormalName="Tesauro" Value="{$video->category_name}" />
      </DescriptiveMetadata>
      <NewsComponent Duid="video_{$video->id}.video.file" EquivalentsList="yes">
        <Role FormalName="Main" />
        <MediaType FormalName="Video" />
        <Characteristics>
          <Property FormalName="TotalDuration" Value="{$video->information['duration']}" />
        </Characteristics>
      </NewsComponent>
      <NewsComponent Duid="video_{$video->id}.video.text">
        <Role FormalName="Caption" />
        <ContentItem>
          <ContentItem Href="{$app.instance->getBaseUrl()}/{$video->uri}" Url="{$video->video_url}" />
          <MediaType FormalName="Text" />
          <Format FormalName="NITF" />
          <MimeType FormalName="text/vnd.IPTC.NITF" />
          <DataContent>
            <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
              <head>
                <title>
                  <![CDATA[{$video->title}]]>
                </title>
                <docdata management-status="usable">
                  <doc-id id-string="{$video->id}" />
                </docdata>
              </head>
              <body>
                <body.head>
                  <hedline>
                    <hl1>
                      <![CDATA[{$video->title}]]>
                    </hl1>
                    <hl2>
                      <![CDATA[{$video->description}]]>
                    </hl2>
                  </hedline>
                  {if $video->author neq 'null'}
                  <rights>
                    <rights.owner>{$video->author->name}</rights.owner>
                  </rights>
                  {/if}
                  <distributor>{setting name=site_name}</distributor>
                  <dateline>
                    <story.date norm="{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                      {format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}
                    </story.date>
                  </dateline>
                </body.head>
                <body.content>
                  <![CDATA[{$video->description}]]>
                </body.content>
              </body>
            </nitf>
          </DataContent>
        </ContentItem>
      </NewsComponent>
    </NewsComponent>
  </NewsItem>
</NewsML>
