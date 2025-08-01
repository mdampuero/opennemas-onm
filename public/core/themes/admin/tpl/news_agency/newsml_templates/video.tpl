<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime>{format_date date=$video->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateAndTime>
    <NewsProduct FormalName="{$video->type}" />
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
      <Status FormalName="{if $video->in_litter}Canceled{else}{if $video->content_status}Usable{else}Withheld{/if}{/if}" />
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
        <Property FormalName="Tesauro" Value="{get_category_slug($video)}" />
      </DescriptiveMetadata>
      <NewsComponent Duid="video_{$video->id}.video.file" EquivalentsList="yes">
        <Role FormalName="Main" />
        <MediaType FormalName="Video" />
        <Characteristics>
          {if !empty($video->information) && array_key_exists('duration', $video->information)}
            <Property FormalName="TotalDuration" Value="{$video->information['duration']}" />
          {/if}
        </Characteristics>
      </NewsComponent>
      <NewsComponent Duid="video_{$video->id}.video.text">
        <Role FormalName="Caption" />
        <ContentItem Href="{get_url item=$video absolute=true}" {if $video->path}Url="{$video->path|escape:'html'}"{elseif $video->type == 'external'}Url="{$video->information['source']['mp4']|escape:'html'}"{/if}>
          <MediaType FormalName="Text" />
          <Catalog>
            <Resource>
              <Url>{if $video->path}{$video->path|escape:'html'}{elseif $video->type == 'external'}{$video->information['source']['mp4']|escape:'html'}{/if}</Url>
            </Resource>
          </Catalog>
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
                  {if !empty($video->author)}
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
                  {if $video->type == 'script'}
                    <![CDATA[{$video->body}]]>
                  {else}
                    <![CDATA[{$video->description}]]>
                  {/if}
                </body.content>
              </body>
            </nitf>
          </DataContent>
        </ContentItem>
      </NewsComponent>
    </NewsComponent>
  </NewsItem>
</NewsML>
