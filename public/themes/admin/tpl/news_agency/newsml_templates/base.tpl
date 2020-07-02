<?xml version="1.0" encoding="utf-8"?>
<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime>{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateAndTime>
  </NewsEnvelope>
  <NewsItem Duid="multimedia_{$content->id}">
    <Comment FormalName="OnmNewsMLVersion"><text>1.0.1</text></Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>{setting name=site_name}</ProviderId>
        <DateId>{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateId>
        <NewsItemId>{$content->id}</NewsItemId>
        <RevisionId PreviousRevision="1" Update="U"><text>2</text></RevisionId>
        <PublicIdentifier>{$content->urn_source}</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <FirstCreated>{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}</FirstCreated>
      <FirstPublished>{format_date date=$content->starttime type="custom" format="yMMdd'T'HHmmssxxx"}</FirstPublished>
      <ThisRevisionCreated>{format_date date=$content->changed type="custom" format="yMMdd'T'HHmmssxxx"}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_{$content->id}.multimedia">
      <NewsLines>
        <HeadLine><![CDATA[{$content->title}]]></HeadLine>
        <SubHeadLine><![CDATA[{$content->subtitle}]]></SubHeadLine>
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
      <NewsComponent Duid="multimedia_{$content->id}.multimedia.texts">
        <Role FormalName="Content list" />
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.texts.{$content->id}" Euid="{$content->id}">
          <Role FormalName="Main" />
          <NewsLines>
            <HeadLine><![CDATA[{$content->title}]]></HeadLine>
            <SubHeadLine><![CDATA[{$content->subtitle}]]></SubHeadLine>
          </NewsLines>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
            <Property FormalName="Tesauro" Value="{get_category_slug($content)}"/>
            <Property FormalName="Onm_IdRefObject" Value="{$content->id}" />
          </DescriptiveMetadata>
          <ContentItem Href="{get_url($video)}">
            <MediaType FormalName="Text" />
            <Format FormalName="NITF" />
            <MimeType FormalName="text/vnd.IPTC.NITF" />
            <DataContent>
              <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                <head>
                  <title><![CDATA[{$content->title}]]></title>
                  <docdata management-status="usable">
                    <doc.rights agent="Opennemas"/>
                    <doc-id id-string="{$content->id}" />
                    <key-list>
                      <keyword key="{renderMetaKeywords content=$content tags=$tags onlyTags=True }"/>
                    </key-list>
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1><![CDATA[{$content->title}]]></hl1>
                      <hl2><![CDATA[{$content->subtitle}]]></hl2>
                    </hedline>
                    <rights>
                      <rights.agent>{setting name=site_name}</rights.agent>
                      {if !empty($content->author)}
                        <rights.owner>{$content->author->name}</rights.owner>
                        {if $content->author->photo}
                          <rights.owner.photo>
                            {$app.instance->getBaseUrl()}{$smarty.const.MEDIA_IMG_PATH_WEB}{$content->author->photo->path_img}
                          </rights.owner.photo>
                        {/if}
                        <rights.owner.url>
                          {$app.instance->getBaseUrl()}{url name=frontend_author_frontpage slug=$content->author->username}
                        </rights.owner.url>
                      {else}
                        <rights.owner>{$content->agency|default:'Redacción'}</rights.owner>
                      {/if}
                    </rights>
                    <dateline>
                      <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                        {format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}
                      </story.date>
                    </dateline>
                    <abstract>
                      <p><![CDATA[{$content->summary}]]></p>
                    </abstract>
                  </body.head>
                  <body.content>
                    {if $content->content_type_name == 'album'}
                      <![CDATA[{$content->description}]]>
                    {else}
                      <![CDATA[{$content->body}]]>
                    {/if}
                  </body.content>
                  <body.end>
                    {if isset($content->related) && !empty($content->related)}
                      <block class="related-contents">
                        {foreach $content->related as $related}
                          <p>
                            <a href="{get_url($related)}">{$related->title}</a>
                          </p>
                        {/foreach}
                      </block>
                    {/if}
                  </body.end>
                </body>
              </nitf>
            </DataContent>
          </ContentItem>
        </NewsComponent>
      </NewsComponent>
      {if !empty($photo) || !empty($photoInner)}
        <!--Photo collection.-->
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {if !empty($photo)}
            <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}" Euid="{$photo->id}">
              <NewsLines>
                <HeadLine>
                  <![CDATA[{$content->title}]]>
                </HeadLine>
              </NewsLines>
              <AdministrativeMetadata>
                <Provider>
                  <Party FormalName="{setting name=site_name}" />
                </Provider>
              </AdministrativeMetadata>
              <DescriptiveMetadata>
                <Language FormalName="es" />
                <DateLineDate>{format_date date=$photo->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                <Property FormalName="Onm_IdRefObject" Value="{$photo->id}" />
              </DescriptiveMetadata>
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}.file">
                <Role FormalName="Main" />
                <!-- The link to download image -->
                <ContentItem Href="{$app.instance->getBaseUrl()}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
                  <MediaType FormalName="PhotoFront" />
                  <MimeType FormalName="image/{$photo->type_img}" />
                  <Characteristics>
                    <SizeInBytes>{$photo->size*1024}</SizeInBytes>
                    <Property FormalName="Onm_Filename" Value="{$photo->name}" />
                    <Property FormalName="Height" Value="{$photo->height}" />
                    <Property FormalName="PixelDepth" Value="24" />
                    <Property FormalName="Width" Value="{$photo->width}" />
                  </Characteristics>
                </ContentItem>
              </NewsComponent>
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}.text">
                <Role FormalName="Caption" />
                <ContentItem>
                  <MediaType FormalName="Text" />
                  <Format FormalName="NITF" />
                  <MimeType FormalName="text/vnd.IPTC.NITF" />
                  <DataContent>
                    <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                      <head>
                        <title>
                          <![CDATA[{$content->title}]]>
                        </title>
                        <docdata management-status="usable">
                          <doc-id id-string="{$photo->id}" />
                        </docdata>
                      </head>
                      <body>
                        <body.head>
                          <hedline>
                            <hl1>
                              <![CDATA[{$content->title}]]>
                            </hl1>
                          </hedline>
                          <dateline>
                            <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                              {format_date date=$photo->created type="custom" format="yMMdd'T'HHmmssxxx"}
                            </story.date>
                          </dateline>
                        </body.head>
                        <body.content>
                          <p>
                            <![CDATA[{$photo->description|htmlspecialchars_decode|trim}]]>
                          </p>
                        </body.content>
                      </body>
                    </nitf>
                  </DataContent>
                </ContentItem>
              </NewsComponent>
            </NewsComponent>
          {/if}
          {if !empty($photoInner) && (empty(photo) || (!empty(photo) && $photo->id !== $photoInner->id))}
            <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photoInner->id}" Euid="{$photoInner->id}">
              <NewsLines>
                <HeadLine>
                  <![CDATA[{$content->title}]]>
                </HeadLine>
              </NewsLines>
              <AdministrativeMetadata>
                <Provider>
                  <Party FormalName="{setting name=site_name}" />
                </Provider>
              </AdministrativeMetadata>
              <DescriptiveMetadata>
                <Language FormalName="es" />
                <DateLineDate>{format_date date=$photoInner->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                <Property FormalName="Onm_IdRefObject" Value="{$photoInner->id}" />
              </DescriptiveMetadata>
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photoInner->id}.file">
                <Role FormalName="Main" />
                <!-- The link to download image -->
                <ContentItem Href="{$app.instance->getBaseUrl()}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photoInner->path_file}{$photoInner->name}">
                  <MediaType FormalName="PhotoInner" />
                  <MimeType FormalName="image/{$photoInner->type_img}" />
                  <Characteristics>
                    <SizeInBytes>{$photoInner->size*1024}</SizeInBytes>
                    <Property FormalName="Onm_Filename" Value="{$photoInner->name}" />
                    <Property FormalName="Height" Value="{$photoInner->height}" />
                    <Property FormalName="PixelDepth" Value="24" />
                    <Property FormalName="Width" Value="{$photoInner->width}" />
                  </Characteristics>
                </ContentItem>
              </NewsComponent>
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photoInner->id}.text">
                <Role FormalName="Caption" />
                <ContentItem>
                  <MediaType FormalName="Text" />
                  <Format FormalName="NITF" />
                  <MimeType FormalName="text/vnd.IPTC.NITF" />
                  <DataContent>
                    <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                      <head>
                        <title>
                          <![CDATA[{$content->title}]]>
                        </title>
                        <docdata management-status="usable">
                          <doc-id id-string="{$photoInner->id}" />
                        </docdata>
                      </head>
                      <body>
                        <body.head>
                          <hedline>
                            <hl1>
                              <![CDATA[{$content->title}]]>
                            </hl1>
                          </hedline>
                          <dateline>
                            <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                              {format_date date=$photoInner->created type="custom" format="yMMdd'T'HHmmssxxx"}
                            </story.date>
                          </dateline>
                        </body.head>
                        <body.content>
                          <p>
                            <![CDATA[{$photoInner->description|htmlspecialchars_decode|trim}]]>
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
      {if isset($content->all_photos) && !empty($content->all_photos)}
        <!--Photo collection.-->
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {foreach $content->all_photos as $photo}
            {if $photo->id}
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}" Euid="{$photo->id}">
                <NewsLines>
                  <HeadLine>
                    <![CDATA[{$content->title}]]>
                  </HeadLine>
                </NewsLines>
                <AdministrativeMetadata>
                  <Provider>
                    <Party FormalName="{setting name=site_name}" />
                  </Provider>
                </AdministrativeMetadata>
                <DescriptiveMetadata>
                  <Language FormalName="es" />
                  <DateLineDate>{format_date date=$photo->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                  <Property FormalName="Onm_IdRefObject" Value="{$photo->id}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}.file">
                  <Role FormalName="Main" />
                  <!-- The link to download image -->
                  <ContentItem Href="{$app.instance->getBaseUrl()}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
                    <MediaType FormalName="Photo" />
                    <MimeType FormalName="image/{$photo->type_img}" />
                    <Characteristics>
                      <SizeInBytes>{$photo->size*1024}</SizeInBytes>
                      <Property FormalName="Onm_Filename" Value="{$photo->name}" />
                      <Property FormalName="Height" Value="{$photo->height}" />
                      <Property FormalName="PixelDepth" Value="24" />
                      <Property FormalName="Width" Value="{$photo->width}" />
                    </Characteristics>
                  </ContentItem>
                </NewsComponent>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$photo->id}.text">
                  <Role FormalName="Caption" />
                  <ContentItem>
                    <MediaType FormalName="Text" />
                    <Format FormalName="NITF" />
                    <MimeType FormalName="text/vnd.IPTC.NITF" />
                    <DataContent>
                      <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                        <head>
                          <title>
                            <![CDATA[{$content->title}]]>
                          </title>
                          <docdata management-status="usable">
                            <doc-id id-string="{$photo->id}" />
                          </docdata>
                        </head>
                        <body>
                          <body.head>
                            <hedline>
                              <hl1>
                                <![CDATA[{$content->title}]]>
                              </hl1>
                            </hedline>
                            <dateline>
                              <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                                {format_date date=$photo->created type="custom" format="yMMdd'T'HHmmssxxx"}
                              </story.date>
                            </dateline>
                          </body.head>
                          <body.content>
                            <p>
                              <![CDATA[{$photo->description|htmlspecialchars_decode|trim}]]>
                            </p>
                          </body.content>
                        </body>
                      </nitf>
                    </DataContent>
                  </ContentItem>
                </NewsComponent>
              </NewsComponent>
            {/if}
          {/foreach}
        </NewsComponent>
      {/if}
    </NewsComponent>
  </NewsItem>
</NewsML>
