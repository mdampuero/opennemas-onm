<?xml version="1.0" encoding="utf-8"?>
<NewsML Version="1.2">
  <NewsEnvelope>
    <SentFrom>
      <Party FormalName="Opennemas">
        <Property FormalName="Organization" Value="{setting name=site_name}" />
      </Party>
    </SentFrom>
    <DateAndTime>{format_date date=$article->created type="custom" format="Ymd\THisP"}</DateAndTime>
  </NewsEnvelope>
  <NewsItem Duid="multimedia_{$article->id}">
    <Comment FormalName="OnmNewsMLVersion"><text>1.0.1</text></Comment>
    <Identification>
      <NewsIdentifier>
        <ProviderId>{setting name=site_name}</ProviderId>
        <DateId>{format_date date=$article->created type="custom" format="Ymd\THisP"}</DateId>
        <NewsItemId>{$article->id}</NewsItemId>
        <RevisionId PreviousRevision="1" Update="U"><text>2</text></RevisionId>
        <PublicIdentifier>{$article->urn_source}</PublicIdentifier>
      </NewsIdentifier>
    </Identification>
    <NewsManagement>
      <NewsItemType FormalName="News" />
      <!--Creation date.-->
      <FirstCreated>{format_date date=$article->created type="custom" format="Ymd\THisP"}</FirstCreated>
      {if $article->starttime_datetime}
        <!--Published date.-->
        <FirstPublished>{format_date date=$article->starttime type="custom" format="Ymd\THisP"}</FirstPublished>
      {/if}
      <!--Last modification date.-->
      <ThisRevisionCreated>{format_date date=$article->changed type="custom" format="Ymd\THisP"}</ThisRevisionCreated>
      <Status FormalName="Usable" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_{$article->id}.multimedia">
      <NewsLines>
        <HeadLine><![CDATA[{$article->title}]]></HeadLine>
        <SubHeadLine><![CDATA[{$article->subtitle}]]></SubHeadLine>
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
            <HeadLine><![CDATA[{$article->title}]]></HeadLine>
            <SubHeadLine><![CDATA[{$article->subtitle}]]></SubHeadLine>
          </NewsLines>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>{format_date date=$article->created type="custom" format="Ymd\THisP"}</DateLineDate>
            <Property FormalName="Tesauro" Value="{$article->category_name}"/>
            <Property FormalName="Onm_IdRefObject" Value="{$article->id}" />
          </DescriptiveMetadata>
          <ContentItem Href="{$article->uri}">
            <MediaType FormalName="Text" />
            <Format FormalName="NITF" />
            <MimeType FormalName="text/vnd.IPTC.NITF" />
            <DataContent>
              <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                <head>
                  <title><![CDATA[{$article->title}]]></title>
                  <docdata management-status="usable">
                    <doc.rights agent="Opennemas"/>
                    <doc-id id-string="{$article->id}" />
                    <key-list>
                      <keyword key="{renderMetaKeywords content=$article tags=$tags onlyTags=True }"/>
                    </key-list>
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1><![CDATA[{$article->title}]]></hl1>
                      <hl2><![CDATA[{$article->subtitle}]]></hl2>
                    </hedline>
                    <rights>
                      <rights.agent>{setting name=site_name}</rights.agent>
                      {if !empty($article->author)}
                        <rights.owner>{$article->author->name}</rights.owner>
                        {if $article->author->photo}
                          <rights.owner.photo>
                            {$app.instance->getBaseUrl()}{$smarty.const.MEDIA_IMG_PATH_WEB}{$article->author->photo->path_img}
                          </rights.owner.photo>
                        {/if}
                        <rights.owner.url>
                          {$app.instance->getBaseUrl()}{url name=frontend_author_frontpage slug=$article->author->username}
                        </rights.owner.url>
                      {else}
                        <rights.owner>{$article->agency|default:'Redacción'}</rights.owner>
                      {/if}
                    </rights>
                    <dateline>
                      <story.date norm="{format_date date=$article->created type='custom' format='Ymd\THisP'}">
                        {format_date date=$article->created type="custom" format="Ymd\THisP"}
                      </story.date>
                    </dateline>
                    <abstract>
                      <p><![CDATA[{$article->summary}]]></p>
                    </abstract>
                  </body.head>
                  <body.content>
                    <![CDATA[{$article->body}]]>
                  </body.content>
                  <body.end>
                    {if isset($article->related) && !empty($article->related)}
                      <block class="related-contents">
                        {foreach $article->related as $related}
                          <p>
                            <a href="/{$related->uri}">{$related->title}</a>
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
        <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {if !empty($photo)}
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}" Euid="{$photo->id}">
              <NewsLines>
                <HeadLine>
                  <![CDATA[{$article->title}]]>
                </HeadLine>
              </NewsLines>
              <AdministrativeMetadata>
                <Provider>
                  <Party FormalName="{setting name=site_name}" />
                </Provider>
              </AdministrativeMetadata>
              <DescriptiveMetadata>
                <Language FormalName="es" />
                <DateLineDate>{format_date date=$photo->created type="custom" format="Ymd\THisP"}</DateLineDate>
                <Property FormalName="Onm_IdRefObject" Value="{$photo->id}" />
              </DescriptiveMetadata>
              <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.file">
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
              <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.text">
                <Role FormalName="Caption" />
                <ContentItem>
                  <MediaType FormalName="Text" />
                  <Format FormalName="NITF" />
                  <MimeType FormalName="text/vnd.IPTC.NITF" />
                  <DataContent>
                    <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                      <head>
                        <title>
                          <![CDATA[{$article->title}]]>
                        </title>
                        <docdata management-status="usable">
                          <doc-id id-string="{$photo->id}" />
                        </docdata>
                      </head>
                      <body>
                        <body.head>
                          <hedline>
                            <hl1>
                              <![CDATA[{$article->title}]]>
                            </hl1>
                          </hedline>
                          <dateline>
                            <story.date norm="{format_date date=$photo->created type='custom' format='Ymd\THisP'}">
                              {format_date date=$photo->created type="custom" format="Ymd\THisP"}
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
            <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}" Euid="{$photoInner->id}">
              <NewsLines>
                <HeadLine>
                  <![CDATA[{$article->title}]]>
                </HeadLine>
              </NewsLines>
              <AdministrativeMetadata>
                <Provider>
                  <Party FormalName="{setting name=site_name}" />
                </Provider>
              </AdministrativeMetadata>
              <DescriptiveMetadata>
                <Language FormalName="es" />
                <DateLineDate>{format_date date=$photoInner->created type="custom" format="Ymd\THisP"}</DateLineDate>
                <Property FormalName="Onm_IdRefObject" Value="{$photoInner->id}" />
              </DescriptiveMetadata>
              <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}.file">
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
              <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photoInner->id}.text">
                <Role FormalName="Caption" />
                <ContentItem>
                  <MediaType FormalName="Text" />
                  <Format FormalName="NITF" />
                  <MimeType FormalName="text/vnd.IPTC.NITF" />
                  <DataContent>
                    <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                      <head>
                        <title>
                          <![CDATA[{$article->title}]]>
                        </title>
                        <docdata management-status="usable">
                          <doc-id id-string="{$photoInner->id}" />
                        </docdata>
                      </head>
                      <body>
                        <body.head>
                          <hedline>
                            <hl1>
                              <![CDATA[{$article->title}]]>
                            </hl1>
                          </hedline>
                          <dateline>
                            <story.date norm="{format_date date=$photoInner->created type='custom' format='Ymd\THisP'}">
                              {format_date date=$photoInner->created type="custom" format="Ymd\THisP"}
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
      {if isset($article->all_photos) && !empty($article->all_photos)}
        <!--Photo collection.-->
        <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {foreach $article->all_photos as $photo}
            {if $photo->id}
              <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}" Euid="{$photo->id}">
                <NewsLines>
                  <HeadLine>
                    <![CDATA[{$article->title}]]>
                  </HeadLine>
                </NewsLines>
                <AdministrativeMetadata>
                  <Provider>
                    <Party FormalName="{setting name=site_name}" />
                  </Provider>
                </AdministrativeMetadata>
                <DescriptiveMetadata>
                  <Language FormalName="es" />
                  <DateLineDate>{format_date date=$photo->created type="custom" format="Ymd\THisP"}</DateLineDate>
                  <Property FormalName="Onm_IdRefObject" Value="{$photo->id}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.file">
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
                <NewsComponent Duid="multimedia_{$article->id}.multimedia.photos.{$photo->id}.text">
                  <Role FormalName="Caption" />
                  <ContentItem>
                    <MediaType FormalName="Text" />
                    <Format FormalName="NITF" />
                    <MimeType FormalName="text/vnd.IPTC.NITF" />
                    <DataContent>
                      <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                        <head>
                          <title>
                            <![CDATA[{$article->title}]]>
                          </title>
                          <docdata management-status="usable">
                            <doc-id id-string="{$photo->id}" />
                          </docdata>
                        </head>
                        <body>
                          <body.head>
                            <hedline>
                              <hl1>
                                <![CDATA[{$article->title}]]>
                              </hl1>
                            </hedline>
                            <dateline>
                              <story.date norm="{format_date date=$photo->created type='custom' format='Ymd\THisP'}">
                                {format_date date=$photo->created type="custom" format="Ymd\THisP"}
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
