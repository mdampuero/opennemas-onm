{extends file="base/admin.tpl"}

{block name="header-css" append}
<style>
  .accordion-option:first-child .grid-collapse-title {
    border-top: 0 none !important;
  }
</style>
{/block}

{block name="content"}
<form action="{url name=backend_newsletters_send id=$id}" method="POST" name="newsletterForm" id="pick-recipients-form" ng-controller="NewsletterCtrl" ng-init="initPickRecipients({json_encode($content)|clear_json}, {json_encode($extra)|clear_json})">
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
            <li class="quicklinks">
              <div class="btn-group">
                <a class="btn btn-primary" href="{url name=backend_newsletters_preview id=$id}" class="admin_add" title="{t}Previous{/t}" id="prev-button">
                  <span class="fa fa-chevron-left"></span>
                  <span class="hidden-xs">{t}Previous{/t}</span>
                </a>
                <a class="btn btn-danger text-white" ng-click="send()" {* disabled ng-disabled="target.items.length == 0" *} type="button">
                  <span class="fa fa-envelope"></span>
                  <span class="hidden-xs">{t}Send{/t}</span>
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager" ng-init="step=2">
    {include file="newsletter/partials/send_steps.tpl"}
    <div class="row m-t-10">
      <div class="col-sm-6">
        <div class="grid simple">
          <div class="grid-title">
            <h4>{t}Select your recipients{/t}</h4>
          </div>
          <div class="grid-body no-padding">
            <div id="providers-accordion">
              <div class="accordion-option" ng-if="newsletter_handler == 'external'">
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded == 'external' }" ng-click="expanded = 'external'">
                  <i class="fa fa-external-link m-r-10"></i>{t}External service{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded == 'external' }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak p-b-30" ng-class="{ 'expanded': expanded == 'external' }">
                  <div class="form-group">
                    <div class="help">
                      {t 1="[% routing.generate('backend_newsletters_config') %]" escape=off}External email address configured on the <a href="%1">newsletter configuration section</a> {/t}
                    </div>
                    <div class="m-t-15 m-b-10">
                      <div class="checkbox" ng-repeat="item in source.items|filter:{ type: 'external' }">
                        <input id="checkbox-left-[% item.uuid %]" checklist-model="source.selected" checklist-value="item" type="checkbox">
                        <label for="checkbox-left-[% item.uuid %]">
                          [% item.email %]
                        </label>
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-block" ng-click="addRecipients()" ng-disabled="source.selected.length == 0" type="button">
                    {t}Add to list{/t}
                    <i class="fa fa-chevron-right"></i>
                  </button>
                </div>
              </div>

              <div class="accordion-option" ng-if="newsletter_handler == 'lists'">
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded == 'lists' }" ng-click="expanded = 'lists'">
                  <i class="fa fa-address-book m-r-10"></i>{t}Subscription lists{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded == 'lists' }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="expanded != 'lists'">
                    <span>[% (source.items|filter:{ type: 'list' }).length %]</span>
                  </span>
                </div>

                <div class="grid-collapse-body clearfix ng-cloak" ng-class="{ 'expanded': expanded == 'lists' }">
                  <div class="form-group">
                    <div class="help">
                      {t 1="[% routing.generate('backend_subscriptions_list') %]" escape=off}Subscription lists available from <a href="%1">subscriptions module</a> {/t}
                    </div>
                    <div class="m-t-15 m-b-10" ng-repeat="item in source.items|filter:{ type: 'list' }">
                      <div class="checkbox">
                        <input id="checkbox-left-[% item.uuid %]" checklist-model="source.selected" checklist-value="item" type="checkbox">
                        <label for="checkbox-left-[% item.uuid %]">
                          <strong>[% item.name %]</strong> - {t 1="[% item.subscribers %]"}%1 subscriptors{/t}
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="m-b-20">
                    <button class="btn btn-block" ng-click="addRecipients()" ng-disabled="source.external.selected.length == 0" type="button">
                      {t}Add to list{/t}
                      <i class="fa fa-chevron-right"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="accordion-option">
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded == 'manual' }" ng-click="expanded = 'manual'">
                  <i class="fa fa-envelope m-r-10"></i>{t}{t}Add recipients manually{/t}{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded == 'manual' }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak p-b-30" ng-class="{ 'expanded': expanded == 'manual' }">
                  <div class="form-group" ng-class="{ 'has-error': moreEmailsError }">
                    <div class="controls m-b-10">
                      <textarea class="form-control" id="more-emails" ng-model="moreEmails" placeholder="{t}Write a list of email address writing one per line{/t}" rows=10></textarea>
                    </div>
                    <button class="btn btn-block" ng-click="addMoreEmails()" type="button">
                      {t}Parse list & add{/t}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="grid simple">
          <div style="position:absolute; top: 45%; left: -10px;">
            <i class="fa fa-angle-right" style="width:15px"></i>
          </div>
          <div class="grid-title clearfix">
            <h4 class="pull-left">{t}Receivers{/t} <span class="badge badge-default ng-cloak" ng-show="recipients.items.length > 0">[% recipients.items.length %]</span></h4>
            <button class="btn btn-danger btn-mini pull-right m-t-5 ng-cloak" ng-click="removeRecipients()" ng-disabled="recipients.selected.length == 0" type="button" ng-show="recipients.selected.length > 0">
              <i class="fa fa-trash fa-lg"></i>
              {t}Remove selected{/t}
            </button>
          </div>
          <div class="grid-body">
            <div ng-show="recipients.items.length == 0" class="text-center p-t-50 p-b-50">
              <i class="fa fa-address-book fa-4x m-b-30"></i>
              <p>{t escape=off}No recipients selected{/t}</p>
            </div>
            <div class="ng-cloak" ng-show="recipients.items.length > 0">
              <div class="m-b-15" ng-repeat="item in recipients.items">
                <div class="checkbox">
                  <input id="checkbox-right-[% $index %]" checklist-model="recipients.selected" checklist-value="item" type="checkbox">
                  <label for="checkbox-right-[% $index %]">
                    <span ng-if="item.type == 'external'"><i class="fa fa-external-link" uib-tooltip="{t}External service{/t}"></i></span>
                    <span ng-if="item.type == 'list'"><i class="fa fa-address-book" uib-tooltip="{t}Subscription list{/t}"></i> </span>
                    <span ng-if="item.type == 'email'"><i class="fa fa-envelope" uib-tooltip="{t}Email address{/t}"></i> </span>
                    [% item.name %]
                    <span class="badge badge-default m-l-5" ng-if="item.type == 'list'">{t 1="[% item.subscribers %]"}%1 subscriptors{/t}</span>
                  </label>
                </div>
              </div>

              {* <div class="clearfix ng-hide">
                <hr>
                <div class="checkbox checkbox-default pull-left">
                  <input id="select-all" ng-model="recipients.all" type="checkbox" ng-change="toggleAllRecipients()">
                  <label for="select-all">{t}Toggle all{/t}</label>
                </div>
                <button class="btn btn-danger btn-mini pull-right" ng-click="removeRecipients()" ng-disabled="recipients.selected.length == 0" type="button" ng-show="recipients.selected.length > 0">
                  <i class="fa fa-trash fa-lg"></i>
                  {t}Remove selected{/t}
                </button>
              </div> *}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <input type="hidden" id="recipients_hidden" name="recipients" ng-value="targetItems"/>

  <script type="text/ng-template" id="modal-confirm-send">
    {include file="newsletter/modals/_confirm_send.tpl"}
  </script>
</form>
{/block}
