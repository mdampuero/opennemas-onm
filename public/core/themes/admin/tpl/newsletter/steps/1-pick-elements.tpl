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
          <i class="fa fa-envelope-o m-r-10"></i>
          <h4>{t}Subject{/t}</h4>
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
            <h4>
              {t}Contents{/t}
            </h4>
          </div>
          <div class="grid-body">
            <div ui-tree="treeOptions">
              <div ng-model="containers" type="container" ui-tree-nodes="">
                <div class="newsletter-container ng-cloak" ng-repeat="container in containers" ui-tree-node>
                  <span ui-tree-handle>
                    <span class="angular-ui-tree-icon"></span>
                  </span>
                  <div class="newsletter-container-title">
                    <div class="row">
                      <div class="col-sm-6 col-lg-4 m-t-15">
                        <input class="form-control" ng-model="container.title" type="text">
                      </div>
                      <div class="col-sm-6 col-lg-8 m-b-10 m-t-15 text-right">
                        <button class="btn btn-default m-b-5" ng-click="markContainer($index)" content-picker content-picker-ignore="[% getItemIds(container.items) %]" content-picker-intime="true" content-picker-section="newsletter" content-picker-selection="true" content-picker-max-size="50" content-picker-target="target" content-picker-type="album,article,attachment,obituary,opinion,poll,video,special" type="button">
                          <i class="fa fa-plus m-r-5"></i>
                          {t}Add{/t}
                        </button>
                        <button class="btn btn-danger m-b-5 " ng-click="removeContainer($index)" type="button">
                          <i class="fa fa-trash-o m-r-5"></i>
                          {t}Delete{/t}
                        </button>
                        <button class="btn btn-white m-b-5 " ng-click="emptyContainer($index)" type="button">
                          <i class="fa fa-fire m-r-5"></i>
                          {t}Empty{/t}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="newsletter-container-items" ui-tree="treeOptions">
                    <div class="newsletter-container-items-placeholder" ng-if="container.items.length == 0">
                      {t}Click on "Add" or drop contents from other containers{/t}
                    </div>
                    <div ng-model="container.items" type="content" ui-tree-nodes="">
                      <div class="newsletter-item" ng-repeat="content in container.items" ui-tree-node>
                        <span ui-tree-handle>
                          <span class="angular-ui-tree-icon"></span>
                        </span>
                        <span class="newsletter-item-type">
                          <span class="fa" ng-class="{ 'fa-file-text-o': content.content_type_name == 'article', 'fa-shield fa-flip-vertical': content.content_type_name == 'obituary', 'fa-quote-right': content.content_type_name == 'opinion', 'fa-pie-chart': content.content_type_name == 'poll', 'fa-file': content.content_type_name == 'static_page', 'fa-envelope': content.content_type_name == 'letter', 'fa-paperclip': content.content_type_name == 'attachment', 'fa-film': content.content_type_name == 'video', 'fa-camera': content.content_type_name == 'album' }" tooltip-placement="right" uib-tooltip="[% content.content_type_l10n_name %]"></span>
                        </span>
                        <span class="newsletter-item-title">
                          [% content.title %]
                        </span>
                        <button class="btn btn-danger" ng-click="removeContent(container, $index)" type="button">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-center">
              <button type="button" class="btn btn-default" ng-click="addContainer()">
                <i class="fa fa-plus m-r-5"></i>
                {t}Add{/t}
              </button>
              <button class="btn btn-danger" ng-click="removeContainer()" type="button">
                <i class="fa fa-trash-o m-r-5"></i>
                {t}Delete{/t}
              </button>
              <button class="btn btn-white" ng-click="emptyContainer()" type="button">
                <i class="fa fa-fire m-r-5"></i>
                {t}Empty{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="content_ids" ng-value="contents">
    <input type="hidden" name="id" value="{$newsletter->id}">
  </form>
{/block}
