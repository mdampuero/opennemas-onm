{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_newsletter_save_contents}" method="POST" name="newsletterForm" id="newsletter-pick-elements-form" ng-controller="NewsletterCtrl" ng-init="stepOne({json_encode($newsletterContent)|replace:'"':'\''})">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              {t}Newsletters{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Pick contents{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a href="{url name=admin_newsletters}" class="btn btn-link" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks btn-group">
              <button class="btn btn-primary" type="submit" title="{t}Next{/t}" id="next-button">
                <span class="hidden-xs">{t}Next{/t}</span>
                <span class="fa fa-chevron-right"></span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="content newsletter-manager">

    {render_messages}

    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label for="name" class="form-label">{t}Email subject{/t}</label>
          <div class="controls">
            <input type="text" name="title" id="title" value="{$newsletter->title|default:$name}" required class="form-control"/>
          </div>
        </div>
      </div>
    </div>

    <div class="newsletter-contents">

      <div class="grid simple" >
        <div class="grid-title clearfix">
          <div class="pull-left">{t}Add contents to groups by using the "Add contents" button{/t}</div>
          <div class="pull-right">
            <button type="button" class="btn btn-mini" ng-click="addContainer()">
              <span class="fa fa-plus"></span> {t}Add Container{/t}
            </button>
            <button type="button" title="{t}Clean containers{/t}" class="btn btn-mini" ng-click="cleanContainers()">
              <i class="fa fa-trash-o"></i> {t}Clean contents{/t}
            </button>
          </div>
        </div>
        <div class="grid-body" id="newsletter-contents">

          <div class="newsletter-container ng-cloak" ng-repeat="container in newsletterContents| orderBy : id">
            <div class="newsletter-container-title">
              <input ng-model="container.title" type="text">

              <div class="container-actions pull-right">
                <button type="button" class="btn btn-white" content-picker content-picker-selection="true" content-picker-max-size="30" content-picker-target="container.id" media-picker-type="album,article,opinion,poll,video">
                  <span class="fa fa-add"></span>
                  {t}Add contents{/t}
                </button>
                <button type="button" ng-click="moveContainerUp(container)" class="btn btn-white" ng-if="$index !== 0">
                  <i class="fa fa-chevron-up"></i>
                </button>
                <button type="button" ng-click="moveContainerDown(container)" class="btn btn-white" ng-if="$index < newsletterContents.length">
                  <i class="fa fa-chevron-down"></i>
                </button>
                <button class="btn btn-white" ng-click="removeContainer(container)" type="button">
                  <i class="fa fa-trash-o text-danger"></i>
                </button>
              </div>
            </div>
            <div class="newsletter-container-contents clearfix">
              <ul ui-sortable="sortableOptions" ng-model="container.items" class="newsletter-container-contents-sortable">
                <li class="newsletter-content clearfix" ng-repeat="content in container.items">
                  <span class="content-type">[% content.content_type %]</span> [% content.title %]

                  <button class="btn btn-white pull-right" ng-click="removeContent(content)" type="button">
                    <i class="fa fa-trash-o text-danger"></i>
                  </button>

                </li>
              </ul>
            </div>
          </div>


        </div>

      </div>
    </div>
  </div>

  <input type="hidden" id="content_ids" name="content_ids">
  <input type="hidden" name="id" value="{$newsletter->pk_newsletter}">

</form>
{/block}

{block name="footer-js" append}
  {script_tag src="/onm/newsletter.js"}
  <script type="text/javascript">
    {if $with_html}
    var has_contents = true;
    {else}
    var has_contents = false;
    {/if}
  </script>

  {include file="newsletter/modals/_back_contents_accept.tpl"}
{/block}
