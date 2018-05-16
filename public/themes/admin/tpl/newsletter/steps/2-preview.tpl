{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=backend_newsletters_save_html id=$newsletter->id}" method="POST" id="newsletter-preview-form" ng-controller="NewsletterCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-envelope"></i>
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
                <a href="{url name=backend_newsletters_show_contents id=$newsletter->id}" class="btn btn-primary" title="{t}Previous{/t}" id="prev-button">
                  <span class="fa fa-chevron-left"></span>
                  <span class="hidden-xs">{t}Previous{/t}</span>
                </a>
                <a href="{url name=backend_newsletters_pick_recipients id=$newsletter->id}" class="btn btn-primary" title="{t}Next{/t}" id="next-button">
                  <span class="hidden-xs">{t}Next{/t}</span>
                  <span class="fa fa-chevron-right"></span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content" ng-init="step = 1">
      {include file="newsletter/partials/send_steps.tpl"}
      <div class="grid simple">
        <div class="grid-title">
          <i class="fa fa-envelope-o"></i>
          <h4>{t}Email subject{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
              <input type="text" name="subject" id="title" value="{$newsletter->title}" required class="form-control"/>
          </div>
        </div>
      </div>
      <div class="grid simple">
        <button class="btn btn-default pull-right" ng-click="edit = !edit; saveHtml('{url name=backend_newsletters_save_html id=$newsletter->id}', !edit)" style="margin: 7px 7px 0 0" type="button">
          <span ng-if="!edit">
            <span class="fa fa-pencil"></span>
            {t}Edit{/t}
          </span>
          <span class="ng-cloak" ng-if="edit">
            <span class="fa fa-save"></span>
            {t}Save{/t}
          </span>
        </button>
        <div class="grid-title">
          <h4>{t}Preview{/t}</h4>
        </div>
        <div class="grid-body">
          <input name="html" type="hidden" ng-value="html">
          <input name="hiddenHtml" type="hidden" value="{$newsletter->html|escape:'html'}">
          <div class="form-group" ng-show="edit">
            <textarea onm-editor class="form-control" ng-model="html" cols="100" rows="10"></textarea>
          </div>
          <div class="form-group" ng-show="!edit">
            <div class="controls">
              <div ng-bind-html="trustedHtml"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
