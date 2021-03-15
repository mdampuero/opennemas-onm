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
      <Status FormalName="{if $content->in_litter}Canceled{else}{if $content->content_status}Usable{else}Withheld{/if}{/if}" />
      <Urgency FormalName="5" />
    </NewsManagement>
    <NewsComponent Duid="multimedia_{$content->id}.multimedia">
      <NewsLines>
        <HeadLine><![CDATA[{$content->title}]]></HeadLine>
        <SubHeadLine><![CDATA[{$content->pretitle}]]></SubHeadLine>
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
            <SubHeadLine><![CDATA[{$content->pretitle}]]></SubHeadLine>
          </NewsLines>
          <DescriptiveMetadata>
            <Language FormalName="es" />
            <DateLineDate>{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
            <Property FormalName="Tesauro" Value="{get_category_slug($content)}"/>
            <Property FormalName="Onm_IdRefObject" Value="{$content->id}" />
          </DescriptiveMetadata>
          <ContentItem Href="{get_url($content, [ '_absolute' => true ])}">
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
                      <keyword key="{renderMetaKeywords content=$content onlyTags=True}"/>
                    </key-list>
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1><![CDATA[{$content->title}]]></hl1>
                      <hl2><![CDATA[{$content->pretitle}]]></hl2>
                    </hedline>
                    <rights>
                      <rights.agent>{setting name=site_name}</rights.agent>
                      {if has_author($content)}
                        <rights.owner>{get_author_name($content)}</rights.owner>
                        {if has_author_avatar($content)}
                          <rights.owner.photo>
                          {get_url(get_content(get_author_avatar($content), 'Photo'), [ '_absolute' => true ])}
                          </rights.owner.photo>
                        {/if}
                        {if has_author_url($content)}
                          <rights.owner.url>
                            {get_url(get_author($content), [ '_absolute' => true ])}
                          </rights.owner.url>
                        {/if}
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
                    {if has_related_contents($content, 'inner')}
                      <block class="related-contents">
                        {foreach get_related_contents($content, 'inner') as $related}
                          <p>
                            <a href="{get_url($related)}">
                              {get_title($related)}
                            </a>
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
      {if (has_featured_media($content, 'frontpage') && get_type(get_featured_media($content, 'frontpage')) === 'photo') || (has_featured_media($content, 'inner') && get_type(get_featured_media($content, 'inner')) === 'photo')}
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {foreach [ 'frontpage', 'inner' ] as $type}
            {if has_featured_media($content, $type) && get_type(get_featured_media($content, $type)) === 'photo'}
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{get_property(get_featured_media($content, $type), 'pk_content')}" Euid="{get_property(get_featured_media($content, $type), 'pk_content')}">
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
                  <DateLineDate>{format_date date=get_property(get_featured_media($content, $type), 'created') type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                  <Property FormalName="Onm_IdRefObject" Value="{get_property(get_featured_media($content, $type), 'pk_content')}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{get_property(get_featured_media($content, $type), 'pk_content')}.file">
                  <Role FormalName="Main" />
                  <!-- The link to download image -->
                  <ContentItem Href="{$app.instance->getBaseUrl()}{get_photo_path(get_featured_media($content, $type))}">
                    <MediaType FormalName="PhotoFront" />
                    <MimeType FormalName="{get_photo_mime_type(get_featured_media($content, $type))}" />
                    <Characteristics>
                      <SizeInBytes>{get_photo_size(get_featured_media($content, $type)) * 1024}</SizeInBytes>
                      <Property FormalName="Onm_Filename" Value="{basename(get_property(get_featured_media($content, $type), 'path'))}" />
                      <Property FormalName="Height" Value="{get_photo_height(get_featured_media($content, $type))}" />
                      <Property FormalName="PixelDepth" Value="24" />
                      <Property FormalName="Width" Value="{get_photo_width(get_featured_media($content, $type))}" />
                    </Characteristics>
                  </ContentItem>
                </NewsComponent>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{get_property(get_featured_media($content, $type), 'pk_content')}.text">
                  <Role FormalName="Caption" />
                  <ContentItem>
                    <MediaType FormalName="Text" />
                    <Format FormalName="NITF" />
                    <MimeType FormalName="text/vnd.IPTC.NITF" />
                    <DataContent>
                      <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                        <head>
                          <title>
                            <![CDATA[{get_title(get_featured_media($content, $type))}]]>
                          </title>
                          <docdata management-status="usable">
                            <doc-id id-string="{get_property(get_featured_media($content, $type), 'pk_content')}" />
                          </docdata>
                        </head>
                        <body>
                          <body.head>
                            <hedline>
                              <hl1>
                                <![CDATA[{get_title(get_featured_media($content, $type))}]]>
                              </hl1>
                            </hedline>
                            <dateline>
                              <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                                {format_date date=get_property(get_featured_media($content, $type), 'created') type="custom" format="yMMdd'T'HHmmssxxx"}
                              </story.date>
                            </dateline>
                          </body.head>
                          <body.content>
                            <p>
                              <![CDATA[{get_description(get_featured_media($content, $type))|htmlspecialchars_decode|trim}]]>
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
      {if has_album_photos($content)}
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {foreach get_album_photos($content) as $photo}
            {if $photo->pk_content}
              <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{$photo->pk_content}" Euid="{$photo->pk_content}">
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
                  <Property FormalName="Onm_IdRefObject" Value="{$photo->pk_content}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{$photo->pk_content}.file">
                  <Role FormalName="Main" />
                  <!-- The link to download image -->
                  <ContentItem Href="{$app.instance->getBaseUrl()}{get_photo_path($photo)}">
                    <MediaType FormalName="Photo" />
                    <MimeType FormalName="{get_photo_mime_type($photo)}" />
                    <Characteristics>
                      <SizeInBytes>{get_photo_size($photo)*1024}</SizeInBytes>
                      <Property FormalName="Onm_Filename" Value="{$photo->title}" />
                      <Property FormalName="Height" Value="{get_photo_height($photo)}" />
                      <Property FormalName="PixelDepth" Value="24" />
                      <Property FormalName="Width" Value="{get_photo_width($photo)}" />
                    </Characteristics>
                  </ContentItem>
                </NewsComponent>
                <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{$photo->pk_content}.text">
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
                            <doc-id id-string="{$photo->pk_content}" />
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
