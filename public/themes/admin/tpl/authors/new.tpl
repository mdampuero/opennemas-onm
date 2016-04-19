{extends file="base/admin.tpl"}

{block name="header-css" append}
  {stylesheets src="@Common/components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" filters="cssrewrite"}
  {/stylesheets}
{/block}

{block name="footer-js" append}
  {javascripts src="@AdminTheme/js/onm/video.js,
    @Common/components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"}
    <script>
      $(document).ready(function($){
        $('.fileinput').fileinput({
          name: 'avatar',
          uploadtype:'image'
        });

        $('.delete').on('click', function(){
          $('.file-input').val('0');
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if isset($user->id)}{url name=admin_author_update id=$user->id}{else}{url name=admin_author_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" autocomplete="off">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-user fa-lg page-navbar-icon"></i>
                {t}Authors{/t}
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if isset($user->id)}
                {t}Editing author{/t}
                {else}
                {t}Creating author{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_opinion_authors}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="grid simple">
        <div class="grid-title">
          <h4>{t}User info{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="row">
            <div class="col-sm-8">
              <div class="form-group">
                <label class="control-label" for="name">
                  {t}Display name{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="name" maxlength="50" name="name" required="required" type="text" value="{$user->name|default:""}"  />
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="email">
                  {t}Email{/t}
                </label>
                <div class="controls">
                  <input class="form-control" id="email" name="email" placeholder="test@example.com" required="required" type="email" value="{$user->email|default:""}">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="meta[twitter]">
                  {t}Twitter user{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-at"></i>
                    </span>
                    <input class="form-control" id="prependedInput" type="text" placeholder="{t}Username{/t}" id="meta[twitter]" name="meta[twitter]" value="{$user->meta['twitter']|default:""}">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="meta[facebook]">
                  {t}Facebook user{/t}
                </label>
                <div class="controls">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-at"></i>
                    </span>
                    <input class="form-control" id="prependedInput" type="text" placeholder="{t}Username{/t}" id="meta[facebook]" name="meta[facebook]" value="{$user->meta['facebook']|default:""}">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-4 text-center">
              <div class="fileinput {if $user->photo}fileinput-exists{else}fileinput-new{/if}" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 140px; height: 140px;">
                </div>
                {if $user->photo->name}
                <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;">
                  <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                </div>
                {else}
                <div class="fileinput-exists fileinput-preview thumbnail" style="width: 140px; height: 140px;" rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                  {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
                </div>
                {/if}
                <div>
                  <span class="btn btn-file">
                    <span class="fileinput-new">{t}Add new photo{/t}</span>
                    <span class="fileinput-exists">{t}Change{/t}</span>
                    <input type="file"/>
                    <input type="hidden" name="avatar" class="file-input" value="1">
                  </span>
                  <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                    <i class="fa fa-trash-o"></i>
                    {t}Remove{/t}
                  </a>
                </div>
              </div>
            </div>
          </div>
          {is_module_activated name="BLOG_MANAGER"}
            <div class="form-group">
              <label class="form-label">{t}View as Blog{/t}</label>
              <div class="controls">
                <div class="checkbox">
                  <input type="checkbox" name="meta[is_blog]" id="meta[is_blog]" {if $user->meta['is_blog'] eq 1}checked="checked"{/if}>
                  <label for="meta[is_blog]">
                    {t}If this option is activated page author will be showed as blog{/t}
                  </label>
                </div>
              </div>
            </div>
          {/is_module_activated}
          <div class="form-group">
            <label class="form-label" for="url">
              {t}Other Blog Url{/t}
            </label>
            <div class="controls">
              <input class="form-control" id="url" name="url" placeholder="http://" value="{$user->url|default:""}" type="text">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="bio">
              {t}Short Biography{/t}
            </label>
            <div class="controls">
              <textarea class="form-control" id="bio" name="bio" rows="3">{$user->bio|default:""}</textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="meta[bio_description]">
              {t}Biography{/t}
            </label>
            <div class="controls">
              <textarea class="form-control" id="meta[bio_description]" name="meta[bio_description]" rows="3">{$user->meta['bio_description']|default:""}</textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="meta[inrss]">
              {t}Show in RSS{/t}
            </label>
            <div class="controls">
              <div class="checkbox">
                <input type="checkbox" name="meta[inrss]" id="meta[inrss]" {if !isset($user->meta['inrss']) || $user->meta['inrss'] eq 'on' || $user->meta['inrss'] eq '1'} checked="checked"{/if}>
                <label for="meta[inrss]">
                  {t}If this option is activated this author will be showed in rss{/t}
                </label>
              </div>
            </div>
            <input type="hidden" name="username" value="{$user->username|default:""}">
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
