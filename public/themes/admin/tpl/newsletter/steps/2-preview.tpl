{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_newsletter_save_html id=$newsletter->id}" method="POST" id="newsletter-preview-form" ng-controller="NewsletterCtrl">
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
              <h5>{t}Preview{/t}</h5>
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
                <a href="{url name=admin_newsletter_show_contents id=$newsletter->id}" class="btn btn-primary" title="{t}Previous{/t}" id="prev-button">
                  <span class="fa fa-chevron-left"></span>
                  <span class="hidden-xs">{t}Previous{/t}</span>
                </a>
                <a href="{url name=admin_newsletter_pick_recipients id=$newsletter->id}" class="btn btn-primary" title="{t}Next{/t}" id="next-button">
                  <span class="hidden-xs">{t}Next{/t}</span>
                  <span class="fa fa-chevron-right"></span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple">
        <div class="grid-body">
          <div class="form-group">
            <label for="name" class="form-label">{t}Email subject{/t}</label>
            <div class="controls">
              <input type="text" name="subject" id="title" value="{$newsletter->title}" required class="form-control"/>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple">
        <button class="btn btn-default pull-right" ng-click="edit = !edit" style="margin: 7px 7px 0 0" title="{t}Edit{/t}" type="button">
          <span ng-if="!edit">
            <span class="fa fa-pencil"></span>
            {t}Edit{/t}
          </span>
          <span class="ng-cloak" ng-click="saveHtml('{url name=admin_newsletter_save_html id=$newsletter->id}')" ng-if="edit">
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
            <textarea onm-editor class="form-control" ng-model="html" cols="30" rows="10"></textarea>
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
