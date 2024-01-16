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
          <ContentItem Href="{get_url item=$content absolute=true}">
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
                    {if $content->content_type_name == 'event'}
                      <identified-content>
                        <event start-date="{format_date date=$content->event_start_date type='custom' format="yMMdd'T'"}{format_date date=$content->event_start_hour|default:'00:00' type='custom' format="HHmmssxxx"}" end-date="{format_date date=$content->event_end_date type='custom' format="yMMdd'T'"}{format_date date=$content->event_end_hour|default:'00:00' type='custom' format="HHmmssxxx"}"></event>
                        <location>{$content->event_place}</location>
                        {if $content->event_website}
                        <virtloc value="{$content->event_website}"></virtloc>
                        {/if}
                      </identified-content>
                    {/if}
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
                          {get_url item=get_content(get_author_avatar($content), 'Photo') absolute=true}
                          </rights.owner.photo>
                        {/if}
                        {if has_author_url($content)}
                          <rights.owner.url>
                            {$app.instance->getBaseUrl()}{get_author_url($content)}
                          </rights.owner.url>
                        {/if}
                      {elseif $content->content_type_name == 'letter'}
                        <rights.owner>{$content->author}</rights.owner>
                        <rights.owner.email>{$content->email}</rights.owner.email>
                      {else}
                        <rights.owner>{$content->agency|default:'Redacción'}</rights.owner>
                      {/if}
                    </rights>
                    <byline>
                      <person>{$content->agency}</person>
                      <author>{if get_author_id($content)}{get_author_name($content)}{/if}</author>
                    </byline>
                    <dateline>
                      <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                        {format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}
                      </story.date>
                    </dateline>
                    <abstract>
                      <p><![CDATA[{get_summary($content)}]]></p>
                    </abstract>
                  </body.head>
                  <body.content>
                    {if $content->content_type_name == 'album'}
                      <![CDATA[{$content->description}]]>
                    {elseif $content->content_type_name == 'poll'}
                      {foreach $content->items as $response}
                        <div class="response {$response.pk_item}">
                          {$response.item}
                        </div>
                        <div class="votes">
                          {$response.votes}
                        </div>
                      {/foreach}
                      {if $content->closetime}
                        <div class="close-time">
                          {format_date date=$content->closetime type='custom' format="yMMdd'T'HHmmssxxx"}
                        </div>
                      {/if}
                    {else}
                      <![CDATA[{$content->body}]]>
                    {/if}
                  </body.content>
                  <body.end>
                    {if has_related_contents($content, 'inner')}
                      <block class="related-contents">
                        {foreach get_related_contents($content, 'inner') as $related}
                          <p>
                            <a href="{get_url item=$related}">
                              <![CDATA[{get_title($related)}]]>
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
      {if !empty($featuredMedia['frontpage']) && $featuredMedia['frontpage']->content_type_name === 'photo'|| !empty($featuredMedia['inner']) && $featuredMedia['inner']->content_type_name === 'photo'}
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos">
          <Role FormalName="Content list" />
          {foreach $featuredMedia as $type => $featuredMediaContent}
            {if !empty($featuredMediaContent) && $featuredMediaContent->content_type_name === 'photo'}
              <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$featuredMediaContent->pk_content}" Euid="{$featuredMediaContent->pk_content}">
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
                  <DateLineDate>{format_date date=$featuredMediaContent->created type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                  <Property FormalName="Onm_IdRefObject" Value="{$featuredMediaContent->pk_content}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$featuredMediaContent->pk_content}.file">
                  <Role FormalName="Main" />
                  <!-- The link to download image -->
                  <ContentItem Href="{if !empty($featuredMediaContent->external_uri)}{$featuredMediaContent->external_uri}{else}{$app.instance->getBaseUrl()}{get_photo_path($featuredMediaContent)}{/if}">
                    <MediaType FormalName="PhotoFront" />
                    <MimeType FormalName="{get_photo_mime_type($featuredMediaContent)}" />
                    {if empty($featuredMediaContent->external_uri)}
                    <Characteristics>
                      <SizeInBytes>{get_photo_size($featuredMediaContent) * 1024}</SizeInBytes>
                      <Property FormalName="Onm_Filename" Value="{basename(get_property($featuredMediaContent, 'path'))}" />
                      <Property FormalName="Height" Value="{get_photo_height($featuredMediaContent)}" />
                      <Property FormalName="PixelDepth" Value="24" />
                      <Property FormalName="Width" Value="{get_photo_width($featuredMediaContent)}" />
                    </Characteristics>
                    {/if}
                  </ContentItem>
                </NewsComponent>
                <NewsComponent Duid="multimedia_{$content->id}.multimedia.photos.{$featuredMediaContent->pk_content}.text">
                  <Role FormalName="Caption" />
                  <ContentItem>
                    <MediaType FormalName="Text" />
                    <Format FormalName="NITF" />
                    <MimeType FormalName="text/vnd.IPTC.NITF" />
                    <DataContent>
                      <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                        <head>
                          <title>
                            <![CDATA[{if !empty(get_title($featuredMediaContent))}{get_title($featuredMediaContent)}{else}{get_description($featuredMediaContent)|htmlspecialchars_decode|trim}{/if}]]>
                          </title>
                          <docdata management-status="usable">
                            <doc-id id-string="{$featuredMediaContent->pk_content}" />
                          </docdata>
                        </head>
                        <body>
                          <body.head>
                            <hedline>
                              <hl1>
                                <![CDATA[{if !empty(get_title($featuredMediaContent))}{get_title($featuredMediaContent)}{else}{get_description($featuredMediaContent)|htmlspecialchars_decode|trim}{/if}]]>
                              </hl1>
                            </hedline>
                            <dateline>
                              <story.date norm="{format_date date=$content->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                                {format_date date=get_property($featuredMediaContent, 'created') type="custom" format="yMMdd'T'HHmmssxxx"}
                              </story.date>
                            </dateline>
                            <abstract>
                              <p>
                                <![CDATA[{get_description($featuredMediaContent)|htmlspecialchars_decode|trim}]]>
                              </p>
                            </abstract>
                          </body.head>
                          <body.content>
                            <p>
                              {if has_featured_media_caption($content, $type)}
                                <![CDATA[{get_featured_media_caption($content, $type)}]]>
                              {else}
                                <![CDATA[{get_description($featuredMediaContent)|htmlspecialchars_decode|trim}]]>
                              {/if}
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
      {if !empty($featuredMedia['frontpage']) && $featuredMedia['frontpage']->content_type_name === 'video'|| !empty($featuredMedia['inner']) && $featuredMedia['inner']->content_type_name === 'video'}
        <NewsComponent Duid="multimedia_{$content->id}.multimedia.videos">
          <Role FormalName="Content list" />
          {foreach $featuredMedia as $type => $featuredMediaContent}
            {if !empty($featuredMediaContent) && get_type($featuredMediaContent) === 'video'}
              <NewsComponent Duid="video_{$content->id}.video">
                <NewsLines>
                  <HeadLine>
                    <![CDATA[{$featuredMediaContent->title}]]>
                  </HeadLine>
                  <SubHeadLine>
                    <![CDATA[{$featuredMediaContent->description}]]>
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
                  <Property FormalName="Tesauro" Value="{get_category_slug($featuredMediaContent)}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="video_{$content->id}.video.file" EquivalentsList="yes">
                  <Role FormalName="Video Main" />
                  <MediaType FormalName="Video" />
                  <Characteristics>
                    {if !empty($featuredMediaContent->information) && array_key_exists('duration', $featuredMediaContent->information)}
                      <Property FormalName="TotalDuration" Value="{$featuredMediaContent->information['duration']}" />
                    {/if}
                  </Characteristics>
                </NewsComponent>
                <NewsComponent Duid="video_{$content->id}.video.text">
                  <Role FormalName="Video Caption" />
                  <ContentItem Href="{get_url item=$featuredMediaContent absolute=true}" {if $featuredMediaContent->path}Url="{$featuredMediaContent->path|escape:'html'}"{elseif $featuredMediaContent->type == 'external'}Url="{$featuredMediaContent->information['source']['mp4']|escape:'html'}"{/if}>
                    <MediaType FormalName="Text" />
                    <Catalog>
                      <Resource>
                        <Url>{if $featuredMediaContent->path}{$featuredMediaContent->path|escape:'html'}{elseif $featuredMediaContent->type == 'external'}{$featuredMediaContent->information['source']['mp4']|escape:'html'}{/if}</Url>
                      </Resource>
                    </Catalog>
                    <Format FormalName="NITF" />
                    <MimeType FormalName="text/vnd.IPTC.NITF" />
                    <DataContent>
                      <nitf version="-//IPTC//DTD NITF 3.2//EN" change.date="October 10, 2003" change.time="19:30" baselang="es-ES">
                        <head>
                          <title>
                            <![CDATA[{$featuredMediaContent->title}]]>
                          </title>
                          <docdata management-status="usable">
                            <doc-id id-string="{$content->id}" />
                          </docdata>
                        </head>
                        <body>
                          <body.head>
                            <hedline>
                              <hl1>
                                <![CDATA[{$featuredMediaContent->title}]]>
                              </hl1>
                              <hl2>
                                <![CDATA[{$featuredMediaContent->description}]]>
                              </hl2>
                            </hedline>
                            {if !empty($featuredMediaContent->author)}
                              <rights>
                                <rights.owner>{$featuredMediaContent->author->name}</rights.owner>
                              </rights>
                            {/if}
                            <distributor>{setting name=site_name}</distributor>
                            <dateline>
                              <story.date norm="{format_date date=$featuredMediaContent->created type="custom" format="yMMdd'T'HHmmssxxx"}">
                                {format_date date=$featuredMediaContent->created type="custom" format="yMMdd'T'HHmmssxxx"}
                              </story.date>
                            </dateline>
                          </body.head>
                          <body.content>
                            {if $featuredMediaContent->type == 'script'}
                              <![CDATA[{$featuredMediaContent->body}]]>
                            {else}
                              <![CDATA[{$featuredMediaContent->description}]]>
                            {/if}
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
            {if get_id($photo)}
              <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{get_id($photo)}" Euid="{get_id($photo)}">
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
                  <DateLineDate>{format_date date=get_creation_date($photo) type="custom" format="yMMdd'T'HHmmssxxx"}</DateLineDate>
                  <Property FormalName="Onm_IdRefObject" Value="{get_id($photo)}" />
                </DescriptiveMetadata>
                <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{get_id($photo)}.file">
                  <Role FormalName="Main" />
                  <!-- The link to download image -->
                  <ContentItem Href="{$app.instance->getBaseUrl()}{get_photo_path($photo)}">
                    <MediaType FormalName="Photo" />
                    <MimeType FormalName="{get_photo_mime_type($photo)}" />
                    <Characteristics>
                      <SizeInBytes>{get_photo_size($photo)*1024}</SizeInBytes>
                      <Property FormalName="Onm_Filename" Value="{basename(get_property($photo, 'path'))}" />
                      <Property FormalName="Height" Value="{get_photo_height($photo)}" />
                      <Property FormalName="PixelDepth" Value="24" />
                      <Property FormalName="Width" Value="{get_photo_width($photo)}" />
                    </Characteristics>
                  </ContentItem>
                </NewsComponent>
                <NewsComponent Duid="multimedia_{$content->pk_content}.multimedia.photos.{get_id($photo)}.text">
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
                            <doc-id id-string="{get_id($photo)}" />
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
                                {format_date date=get_creation_date($photo) type="custom" format="yMMdd'T'HHmmssxxx"}
                              </story.date>
                            </dateline>
                          </body.head>
                          <body.content>
                            <p>
                              <![CDATA[{get_caption($photo)|htmlspecialchars_decode|trim}]]>
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
