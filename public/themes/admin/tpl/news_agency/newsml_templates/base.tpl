<?xml version="1.0" encoding="utf-8"?>
<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime>{$article->created_datetime->format('Ymd\THisP')}</DateAndTime>
  </NewsEnvelope>
  <NewsItem Duid="multimedia_{$article->id}">
    <Comment FormalName="OnmNewsMLVersion"><text>1.0.1</text></Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>{setting name=site_name}</ProviderId>
        <DateId>{$article->created_datetime->format('Ymd\THisP')}</DateId>
        <NewsItemId>{$article->id}</NewsItemId>
        <RevisionId PreviousRevision="1" Update="U"><text>2</text></RevisionId>
        <PublicIdentifier>{$article->urn_source}</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <!--Creation date.-->
      <FirstCreated>{$article->created_datetime->format('Ymd\THisP')}</FirstCreated>
      <!--Last modification date.-->
      <ThisRevisionCreated>{$article->updated_datetime->format('Ymd\THisP')}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_{$article->id}.multimedia">
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
            <DateLineDate>{$article->created_datetime->format('Ymd\THisP')}</DateLineDate>
            <Property FormalName="Tesauro" Value="CAT:{$article->category_name|upper}"/>
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
                    <rights>
                      <rights.owner>{$article->author->id}</rights.owner>
                    </rights>
                    <dateline>
                      <story.date norm="{$article->created_datetime->format('Ymd\THis')}">
                        <text>{$article->created_datetime->format('Ymd\THisP')}</text>
                      </story.date>
                    </dateline>
                    <abstract>
                      {$article->summary|unescape:"htmlall"}
                    </abstract>
                  </body.head>
                  <body.content>
                    {$article->body|replace:'<br />':"</p><p>"|unescape:"htmlall"}
                  </body.content>
                </body>
              </nitf>
            </DataContent>
          </ContentItem>
        </NewsComponent>
      </NewsComponent>
      {if !empty($photo) || !empty($photoInner)}
      <!--Photo collection.-->
      <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos">
        <Role FormalName="Content list" />
        {if !empty($photo)}
          <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}" Euid="{$photo->id}">
            <NewsLines>
              <HeadLine>{$article->title|htmlspecialchars_decode}</HeadLine>
            </NewsLines>
            <AdministrativeMetadata>
              <Provider>
                <Party FormalName="{setting name=site_name}" />
              </Provider>
            </AdministrativeMetadata>
            <DescriptiveMetadata>
              <Language FormalName="es" />
              <DateLineDate>{$photo->created_datetime->format('Ymd\THisP')}</DateLineDate>
              <Property FormalName="Onm_IdRefObject" Value="{$photo->id}" />
            </DescriptiveMetadata>
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.file">
              <Role FormalName="Main" />
              <!-- The link to download image -->
              <ContentItem Href="http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
                <MediaType FormalName="Photo" />
                <MimeType FormalName="{$photo->media_type}/{$photo->type_img}" />
                <Characteristics>
                  <SizeInBytes>{$photo->size*1024}</SizeInBytes>
                  <Property FormalName="Onm_Filename" Value="{$photo->name}" />
                  <Property FormalName="Height" Value="{$photo->height}" />
                  <Property FormalName="PixelDepth" Value="24" />
                  <Property FormalName="Width" Value="{$photo->width}" />
                </Characteristics>
              </ContentItem>
            </NewsComponent>
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.text">
              <Role FormalName="Caption" />
              <ContentItem>
                <MediaType FormalName="Text" />
                <Format FormalName="NITF" />
                <MimeType FormalName="text/vnd.IPTC.NITF" />
                <DataContent>
                  <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                    <head>
                      <title>{$article->title|htmlspecialchars_decode}</title>
                      <docdata management-status="usable">
                        <doc-id id-string="{$photo->id}" />
                      </docdata>
                    </head>
                    <body>
                      <body.head>
                        <hedline>
                          <hl1>{$article->title|htmlspecialchars_decode}</hl1>
                        </hedline>
                        <dateline>
                          <story.date norm="{$photo->created_datetime->format('Ymd\THisP')}">
                            {$photo->created_datetime->format('Ymd\THisP')}
                          </story.date>
                        </dateline>
                      </body.head>
                      <body.content>
                        <p>
                          {$photo->description|htmlspecialchars_decode}
                        </p>
                      </body.content>
                    </body>
                  </nitf>
                </DataContent>
              </ContentItem>
            </NewsComponent>
          </NewsComponent>
        {/if}
        {if !empty($photoInner)}
          <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}" Euid="{$photoInner->id}">
            <NewsLines>
              <HeadLine>{$article->title|htmlspecialchars_decode}</HeadLine>
            </NewsLines>
            <AdministrativeMetadata>
              <Provider>
                <Party FormalName="{setting name=site_name}" />
              </Provider>
            </AdministrativeMetadata>
            <DescriptiveMetadata>
              <Language FormalName="es" />
              <DateLineDate>{$photoInner->created_datetime->format('Ymd\THisP')}</DateLineDate>
              <Property FormalName="Onm_IdRefObject" Value="{$photoInner->id}" />
            </DescriptiveMetadata>
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}.file">
              <Role FormalName="Main" />
              <!-- The link to download image -->
              <ContentItem Href="http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photoInner->path_file}{$photoInner->name}">
                <MediaType FormalName="Photo" />
                <MimeType FormalName="{$photoInner->media_type}/{$photoInner->type_img}" />
                <Characteristics>
                  <SizeInBytes>{$photoInner->size*1024}</SizeInBytes>
                  <Property FormalName="Onm_Filename" Value="{$photoInner->name}" />
                  <Property FormalName="Height" Value="{$photoInner->height}" />
                  <Property FormalName="PixelDepth" Value="24" />
                  <Property FormalName="Width" Value="{$photoInner->width}" />
                </Characteristics>
              </ContentItem>
            </NewsComponent>
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}.text">
              <Role FormalName="Caption" />
              <ContentItem>
                <MediaType FormalName="Text" />
                <Format FormalName="NITF" />
                <MimeType FormalName="text/vnd.IPTC.NITF" />
                <DataContent>
                  <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                    <head>
                      <title>{$article->title|htmlspecialchars_decode}</title>
                      <docdata management-status="usable">
                        <doc-id id-string="{$photoInner->id}" />
                      </docdata>
                    </head>
                    <body>
                      <body.head>
                        <hedline>
                          <hl1>{$article->title|htmlspecialchars_decode}</hl1>
                        </hedline>
                        <dateline>
                          <story.date norm="{$photoInner->created_datetime->format('Ymd\THisP')}">
                            {$photoInner->created_datetime->format('Ymd\THisP')}
                          </story.date>
                        </dateline>
                      </body.head>
                      <body.content>
                        <p>
                          {$photoInner->description|htmlspecialchars_decode}
                        </p>
                      </body.content>
                    </body>
                  </nitf>
                </DataContent>
              </ContentItem>
            </NewsComponent>
          </NewsComponent>
        {/if}
      </NewsComponent>
      {/if}
    </NewsComponent>
  </NewsItem>
</NewsML>
