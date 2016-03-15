{extends file="base/admin.tpl"}

{block name="content"}
<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-home fa-lg"></i>
            {t}Welcome to Opennemas{/t}
          </h4>
        </li>
      </ul>
    </div>
  </div>
</div>

<div class="content welcome-page">
  <div class="row">
    <div class="col-sm-6">
      <a href="{url name=admin_domain_management_add}">
        <div class="grid simple">
          <div class="grid-body text-center">
            <i class="block fa fa-retweet fa-2x m-b-15"></i>
            <h4 class="block uppercase">{t}Redirect your domain{/t}</h4>
            <h5 class="wrap">
              {t}I have an existing domain and I want to redirect it to my Opennemas digital newspaper.{/t}
            </h5>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6">
      <a href="{url name=admin_domain_management_add create=1}">
        <div class="grid simple">
          <div class="grid-body green text-center">
            <i class="block fa fa-plus fa-2x m-b-15 text-white"></i>
            <h4 class="block uppercase text-white">{t}Add new domain{/t}</h4>
            <h5 class="text-white wrap">
              {t}I DO NOT have my own domain and I want to create one and redirect it to my Opennemas digital newspaper{/t}
            </h5>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-6">
      <div class="grid simple add-contents">
        <div class="grid-title">
          <h4>{t}Add contents to your site{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="actions">
            {is_module_activated name="ARTICLE_MANAGER"}
            {acl isAllowed="ARTICLE_CREATE"}
            <div class="button">
              <a href="{url name=admin_article_create}" title="{t}New article{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-file-o"></i>{t}New article{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}
            {is_module_activated name="OPINION_MANAGER"}
            {acl isAllowed="OPINION_CREATE"}
            <div class="button">
              <a href="{url name=admin_opinion_create}" title="{t}New opinion{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-comment-o"></i>{t}New opinion{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}
            {is_module_activated name="IMAGE_MANAGER"}
            {acl isAllowed="PHOTO_CREATE"}
            <div class="button">
              <a href="{url name=admin_images}" title="{t}Media manager{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-image"></i>{t}Upload images{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}

            {is_module_activated name="ALBUM_MANAGER"}
            {acl isAllowed="ALBUM_CREATE"}
            <div class="button">
              <a href="{url name=admin_album_create}" title="{t}Media manager{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-stack-overflow"></i>{t}New Album{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}

            {is_module_activated name="VIDEO_MANAGER"}
            {acl isAllowed="VIDEO_CREATE"}
            <div class="button">
              <a href="{url name=admin_videos_create}" title="{t}Media manager{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-video-camera"></i>{t}Upload video{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}

            {is_module_activated name="STATIC_PAGES_MANAGER"}
            {acl isAllowed="STATIC_PAGE_CREATE"}
            <div class="button">
              <a href="{url name=admin_static_pages_create}" title="{t}Media manager{/t}" class="btn btn-white btn-large col-xs-12">
                <i class="fa fa-file-text-o"></i>{t}Static page{/t}
              </a>
            </div>
            {/acl}
            {/is_module_activated}
          </div>
        </div>
      </div>
      <div class="grid simple merchant">
        <div class="grid-title">
          <h4>{t}Want more features?{/t}</h4>
        </div>
        <div class="grid-body">
          <p>{t}We have two ways to add functionality to you site. Check our Opennemas Store:{/t}</p>

          <div class="row">
            <div class="col-sm-6 plans">
              <a href="{url name=admin_store_list}#/pack" class="thumbnail">
                <i class="fa fa-dropbox"></i>
                <div class="title">{t}Plans{/t}</div>
                <div class="description">{t}Bundles multiple functionality in a reduced price{/t}</div>
              </a>
            </div>
            <div class="col-sm-6 modules">
              <a href="{url name=admin_store_list}#/module" class="thumbnail">
                <i class="fa fa-archive"></i>
                <div class="title">{t}Modules{/t}</div>
                <div class="description">{t}Adds an specific feature in your site{/t}</div>
              </a>
            </div>
          </div>

          {t}If you have special needs or want some advice extending your site{/t}
          <a href="javascript:UserVoice.showPopupWidget();" class="btn btn-large contact">{t}Contact us{/t}</a>
          <i class="fa fa-shopping-cart background-icon"></i>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="grid simple">
        <div class="grid-body blue welcome-message">
          <h4 class="text-white">{t escape=off}<strong>Are you new in Opennemas?</strong>{/t}</h4>
          <h5 class="text-white">{t}If you need some help getting started to create awesome content, check out our online user documentation.{/t}</h5>
          <ul>
            <li><a class="text-white" href="http://help.opennemas.com/knowledgebase/articles/221740-primeros-pasos-en-opennemas" target="_blank">{t}First steps in opennemas{/t}</a></li>
            <li><a class="text-white" href="http://help.opennemas.com/knowledgebase">{t}Detailed documentation{/t}</a></li>
            <li><a class="text-white" href="javascript:UserVoice.showPopupWidget();">{t}Contact us for help{/t}</a></li>
          </ul>
          <br>
          <p class="text-white">{t escape=off 1="http://www.youtube.com/user/OpennemasPublishing"}Get more help from our videotutorials in <a href="%1" class="text-white bold">our YouTube channel</a> and subscribe to it.{/t}</p>

          <uib-carousel interval="10000" active="active">
            {foreach $youtube_videos as $videoId name=youtube_videos}
            <uib-slide index="{$smarty.foreach.youtube_videos.index}">
              <div class="video-container">
                <iframe width="420" height="315" src="//www.youtube.com/embed/{$videoId}" frameborder="0" type="text/html" allowfullscreen></iframe>
              </div>
            </uib-slide>
            {/foreach}
          </uib-carousel>
        </div>
      </div>
    </div>
  </div>
</div>
{/block}
