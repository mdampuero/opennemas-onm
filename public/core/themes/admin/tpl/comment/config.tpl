{extends file="base/admin.tpl"}

{block name="content"}
<div ng-controller="CommentsConfigCtrl" ng-init="init({json_encode($configs)|clear_json}, {json_encode($extra)|clear_json})">
  <form action="{if $extra['handler'] == 'disqus'}{url name=backend_comments_disqus_config}{elseif $extra['handler'] == 'facebook'}{url name=backend_comments_facebook_config}{else}{url name=backend_comments_config}{/if}" method="POST" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-comment m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=backend_comments}" title="{t}Go back to list{/t}">
                  {t}Comments{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4><i class="fa fa-angle-right"></i></h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>{t}Settings{/t}</h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {include file="comment/partials/_config.tpl"}

      <div class="ng-cloak" ng-show="!configs.disable_comments">
        {if $extra['handler'] == 'onm'}
          {include file="comment/partials/_built_in.tpl"}
        {/if}

        {if $extra['handler'] == 'disqus'}
          {include file="comment/partials/_disqus.tpl"}
        {/if}

        {if $extra['handler'] == 'facebook'}
          {include file="comment/partials/_facebook.tpl"}
        {/if}
      </div>
    </div>
  </form>
</div>
{/block}

{block name="modals"}
<script type="text/ng-template" id="modal-comment-change">
  {include file="comment/modals/_modalChange.tpl"}
</script>
{/block}
