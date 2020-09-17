{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=backend_newsletters_save_contents}" method="POST" name="newsletterForm" id="newsletter-pick-elements-form" ng-controller="NewsletterCtrl" ng-init="stepOne({json_encode($newsletterContent)|clear_json})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-envelope m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                  {t}Newsletters{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>{t}Send{/t}</h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
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
    <div class="content newsletter-manager" ng-init="step = 0">
      {include file="newsletter/partials/send_steps.tpl"}
      <div class="grid simple">
        <div class="grid-title">
          <i class="fa fa-envelope-o"></i>
          <h4>{t}Email subject{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
              <input type="text" name="title" id="title" value="{$newsletter->title|default:$name|escape:"html"}" required class="form-control" placeholder="{$name}"/>
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
          <div class="grid-body">
            <div ui-tree="options" id="newsletter-contents">
              <ol ui-tree-nodes ng-model="newsletterContents" type="container">
                <li class="newsletter-container ng-cloak" ui-tree-node ng-repeat="container in newsletterContents" collapsed="false">
                  <div class="newsletter-container-title clearfix" ui-tree-handle>
                    <div class="input-group title pull-left" data-nodrag>
                      <input ng-model="container.title" type="text" class="form-control">
                      <div class="input-group-addon" id="basic-addon1"><i class="fa fa-pencil"></i></div>
                    </div>
                    <div class="container-actions pull-right">
                      <button type="button" class="btn btn-white" data-nodrag content-picker content-picker-section="newsletter" content-picker-selection="true" content-picker-max-size="50" content-picker-target="container.items" content-picker-type="album,article,attachment,opinion,poll,video,special">
                        <i class="fa fa-plus"></i>
                        {t}Add contents{/t}
                      </button>
                      <button type="button" data-nodrag ng-click="container.hide = !container.hide" class="btn btn-white">
                        <i class="fa" ng-class="{ 'fa-plus-square-o': container.hide, 'fa-minus-square-o': !container.hide }"></i>
                      </button>
                      <button type="button" data-nodrag ng-click="moveContainerUp(container)" class="btn btn-white" ng-if="$index !== 0">
                        <i class="fa fa-chevron-up"></i>
                      </button>
                      <button type="button" data-nodrag ng-click="moveContainerDown(container)" class="btn btn-white" ng-if="$index+1 < newsletterContents.length">
                        <i class="fa fa-chevron-down"></i>
                      </button>
                      <button class="btn btn-white" data-nodrag ng-click="removeContainer(container)" type="button">
                        <i class="fa fa-trash-o text-danger"></i>
                      </button>
                    </div>
                  </div>
                  <div class="newsletter-container-contents clearfix" ng-if="!container.hide" ui-tree-handle>
                    <div class="hint-message p-b-15" ng-if="container.items.length == 0">
                      {t}Click in "Add contents" button above or drop contents from other containers{/t}
                    </div>
                    <ol ui-tree-nodes="" ng-model="container.items" type="content">
                      <li ng-repeat="content in container.items" ui-tree-node ng-include="'item'"></li>
                    </ol>
                  </div>
                </li>

              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="content_ids" ng-value="contents">
    <input type="hidden" name="id" value="{$newsletter->id}">
    <script type="text/ng-template" id="item">
      <div class="newsletter-item clearfix" ui-tree-handle>
        <span></span>
        <span class="newsletter-item-title">[% content.content_type_l10n_name %]</span>
        <span class="h-seperate"></span>
        <span class="item-title">[% content.title %]</span>
        <button class="btn btn-white pull-right" data-nodrag ng-click="removeContent(container, content)" type="button">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      </div>
    </script>
  </form>
{/block}
