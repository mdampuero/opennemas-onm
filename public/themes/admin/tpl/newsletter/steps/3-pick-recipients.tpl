{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_newsletter_send id=$id}" method="POST" name="newsletterForm" id="pick-recipients-form" ng-controller="NewsletterCtrl">
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
            <h5>{t}Recipient selection{/t}</h5>
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
              <a class="btn btn-primary" href="{url name=admin_newsletter_preview id=$id}" class="admin_add" title="{t}Previous{/t}" id="prev-button">
                <span class="fa fa-chevron-left"></span>
                <span class="hidden-xs">{t}Previous{/t}</span>
              </a>
              <button class="btn btn-primary" ng-click="send()" ng-disabled="target.items.length == 0" type="button">
                <span class="fa fa-envelope"></span>
                <span class="hidden-xs">{t}Send{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager">
    <div class="grid simple">
      <div class="grid-title">
        {t}Please select your desired persons to sent the newsletter to.{/t}
      </div>
      <div class="grid-body" ng-init="source.items = {json_encode($accounts)|clear_json};init(source.items)">
        <div class="row">
          <div class="col-sm-5">
            <button class="btn btn-default btn-mini pull-right" ng-click="addRecipients()" ng-disabled="source.selected.length == 0" type="button">
              <i class="fa fa-plus"></i>
              {t}Add to list{/t}
            </button>
            <button class="btn btn-default btn-mini pull-right m-r-10" ng-click="toggleAllRecipients(source)" type="button">
              {t}Toggle all{/t}
            </button>
            <div class="form-group">
              <label class="control-label">
                {t}Select receivers{/t} - <span class="ng-cloak">([% source.items.length %])</span>
              </label>
              <div class="controls ng-cloak">
                <div class="recipients-box">
                  <div class="m-b-10" ng-repeat="option in sourcePagedItems">
                    <div class="checkbox">
                      <input id="checkbox-left-[% $index %]" checklist-model="source.selected" checklist-value="option" type="checkbox">
                      <label for="checkbox-left-[% $index %]">
                        [% option.email %]
                      </label>
                    </div>
                  </div>
                </div>
                <pagination ng-model="sourceCurrentPage" total-items="source.items.length" max-size="maxSize" boundary-links="true" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
              </div>
            </div>
          </div>
          <div class="col-sm-5 col-sm-offset-2">
            <button class="btn btn-default btn-mini pull-right" ng-click="removeRecipients()" ng-disabled="target.selected.length == 0" type="button">
              <i class="fa fa-times fa-lg"></i>
              {t}Remove from list{/t}
            </button>
            <button class="btn btn-default btn-mini pull-right m-r-10" ng-click="toggleAllRecipients(target)" type="button">
              {t}Toggle all{/t}
            </button>
            <div class="form-group">
              <label class="control-label">
                {t}Receivers{/t} - <span class="ng-cloak">([% target.items.length %])</span>
              </label>
              <div class="controls ng-cloak">
                <div class="recipients-box">
                  <div class="m-b-10" ng-repeat="item in targetPagedItems">
                    <div class="checkbox">
                      <input id="checkbox-right-[% $index %]" checklist-model="target.selected" checklist-value="item" type="checkbox">
                      <label for="checkbox-right-[% $index %]">
                        [% item.email %]
                      </label>
                    </div>
                  </div>
                </div>
                <pagination ng-model="targetCurrentPage" total-items="target.items.length" max-size="maxSize" boundary-links="true" class="pagination-sm" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></pagination>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="form-group" ng-class="{ 'has-error': moreEmailsError }">
              <label class="control-label" for="more-emails">
                {t}Add more emails{/t}
              </label>
              <div class="controls m-b-10">
                <textarea class="form-control" id="more-emails" ng-model="moreEmails" placeholder="{t}Write a list of email address writing one per line{/t}"></textarea>
              </div>
              <div class="text-center">
                <button class="btn btn-default" ng-click="addMoreEmails()" type="button">
                  {t}Parse list & add{/t}
                </button>
              </div>
            </div>
          </div>
        </div>
        <input type="hidden" id="recipients_hidden" name="recipients" ng-value="targetItems"/>
      </div>
    </div>
    <script type="text/ng-template" id="modal-confirm-send">
      {include file="newsletter/modals/_confirm_send.tpl"}
    </script>
  </div>
</form>
{/block}
