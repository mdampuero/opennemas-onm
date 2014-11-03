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
            <DateLineDate>{$article->created_datetime->format('Ymd\THisP')}</DateLineDate>
            <Property FormalName="Tesauro" Value="CAT:{$article->category_name|upper}"/>
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
                    <doc-id id-string="{$article->id}" />
                  </docdata>
                </head>
                <body>
                  <body.head>
                    <hedline>
                      <hl1><![CDATA[{$article->title}]]></hl1>
                      <hl2><![CDATA[{$article->subtitle}]]></hl2>
                    </hedline>
                    {if $article->author neq 'null'}
                    <rights>
                      <rights.owner>{$article->author|htmlspecialchars}</rights.owner>
                      {if $smarty.const.SITE neq 'console'}
                      <rights.owner.photo>http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$authorPhoto->path_img}</rights.owner.photo>
                      {/if}
                    </rights>
                    {/if}
                    <dateline>
                      <story.date norm="{$article->created_datetime->format('Ymd\THis')}">
                        <text>{$article->created_datetime->format('Ymd\THisP')}</text>
                      </story.date>
                    </dateline>
                    <abstract>
                      <p><![CDATA[{$article->summary|trim|substr:3:-4|unescape:"htmlall"}]]></p>
                    </abstract>
                  </body.head>
                  <body.content>
                    <![CDATA[{$article->body|replace:'<br />':"</p><p>"|unescape:"htmlall"|htmlspecialchars}]]>
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
              <HeadLine><![CDATA[{$article->title}]]></HeadLine>
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
              {if $smarty.const.SITE eq 'console'}
              <ContentItem Href="{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
              {else}
              <ContentItem Href="http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
              {/if}
                <MediaType FormalName="PhotoFront" />
                {*<MimeType FormalName="{$photo->media_type}/{$photo->type_img}" />*}
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
                      <title><![CDATA[{$article->title}]]></title>
                      <docdata management-status="usable">
                        <doc-id id-string="{$photo->id}" />
                      </docdata>
                    </head>
                    <body>
                      <body.head>
                        <hedline>
                          <hl1><![CDATA[{$article->title}]]></hl1>
                        </hedline>
                        <dateline>
                          <story.date norm="{$photo->created_datetime->format('Ymd\THisP')}">
                            {$photo->created_datetime->format('Ymd\THisP')}
                          </story.date>
                        </dateline>
                      </body.head>
                      <body.content>
                        <p><![CDATA[{$photo->description|htmlspecialchars_decode|trim}]]></p>
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
              <HeadLine><![CDATA[{$article->title}]]></HeadLine>
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
              {if $smarty.const.SITE eq 'console'}
              <ContentItem Href="{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photoInner->path_file}{$photoInner->name}">
              {else}
              <ContentItem Href="http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photoInner->path_file}{$photoInner->name}">
              {/if}
                <MediaType FormalName="PhotoInner" />
                {*<MimeType FormalName="{$photoInner->media_type}/{$photoInner->type_img}" />*}
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
                      <title><![CDATA[{$article->title}]]></title>
                      <docdata management-status="usable">
                        <doc-id id-string="{$photoInner->id}" />
                      </docdata>
                    </head>
                    <body>
                      <body.head>
                        <hedline>
                          <hl1><![CDATA[{$article->title}]]></hl1>
                        </hedline>
                        <dateline>
                          <story.date norm="{$photoInner->created_datetime->format('Ymd\THisP')}">
                            {$photoInner->created_datetime->format('Ymd\THisP')}
                          </story.date>
                        </dateline>
                      </body.head>
                      <body.content>
                        <p><![CDATA[{$photoInner->description|htmlspecialchars_decode|trim}]]></p>
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
            <HeadLine><![CDATA[{$article->title}]]></HeadLine>
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
            {if $smarty.const.SITE eq 'console'}
            <ContentItem Href="{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
            {else}
            <ContentItem Href="http://{$smarty.const.SITE}{$smarty.const.MEDIA_DIR_URL}{$smarty.const.IMG_DIR}{$photo->path_file}{$photo->name}">
            {/if}
              <MediaType FormalName="Photo" />
              {*<MimeType FormalName="{$photo->media_type}/{$photo->type_img}" />*}
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
                    <title><![CDATA[{$article->title}]]></title>
                    <docdata management-status="usable">
                      <doc-id id-string="{$photo->id}" />
                    </docdata>
                  </head>
                  <body>
                    <body.head>
                      <hedline>
                        <hl1><![CDATA[{$article->title}]]></hl1>
                      </hedline>
                      <dateline>
                        <story.date norm="{$photo->created_datetime->format('Ymd\THisP')}">
                          {$photo->created_datetime->format('Ymd\THisP')}
                        </story.date>
                      </dateline>
                    </body.head>
                    <body.content>
                      <p><![CDATA[{$photo->description|htmlspecialchars_decode|trim}]]></p>
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
