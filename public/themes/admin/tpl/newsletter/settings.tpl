{extends file="base/admin.tpl"}

{block name="content"}
<form ng-controller="NewsletterSettingsCtrl">
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
            <h4>{t}Settings{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-loading btn-primary text-uppercase" ng-click="save()" type="button">
                <span class="fa" ng-class="{ 'fa-save': !saving, 'fa-circle-o-notch fa-spin': saving }"></span>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="listing-no-contents" ng-hide="!flags.loading">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
        <h3 class="spinner-text">{t}Loading{/t}...</h3>
      </div>
    </div>
    <div class="listing-no-contents ng-cloak" ng-if="!flags.loading && items.length == 0">
      <div class="text-center p-b-15 p-t-15">
        <i class="fa fa-4x fa-warning text-warning"></i>
        <h3>{t}Unable to find setting for newsletters.{/t}</h3>
      </div>
    </div>
    <div class="grid simple ng-cloak" ng-if="!flags.loading && settings">
      <div class="grid-title">
        <h4>
          <i class="fa fa-envelope"></i>
          {t}Newsletter handler{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <div class="help">{t}Opennemas allows you to use different methods to send your newsletters. Choose between those available below:{/t}</div>
          <div class="controls">
            <select name="newsletter_subscriptionType" id="newsletter_subscriptionType" ng-model="settings.newsletter_subscriptionType">
              <option value="submit">{t}External service{/t} - {t}Maillist{/t}</option>
              <option value="create_subscriptor">{t}Internal Send{/t} - {t}Subscriptions{/t}</option>
              <option value="acton">{t}External service{/t} - {t}Act-On lists{/t}</option>
            </select>
          </div>
        </div>
        <div class="external-config ng-cloak" ng-show="settings.newsletter_subscriptionType === 'submit'">
          <div class="form-group">
            <label for="email" class="form-label">{t}Mailing list address{/t}</label>
            <div class="controls">
              <input class="form-control" id="email" name="newsletter_maillist[email]" required type="email" ng-value="settings.newsletter_maillist.email"/>
              <div class="help">{t}If you have a mailing list service to deliver newsletters add the address here{/t}</div>
            </div>
          </div>
          <div class="form-group" >
            <label for="subscription" class="form-label">{t}Mail address to receive new subscriptions{/t}</label>
            <div class="controls">
              <input class="form-control" id="subscription" required type="email" ng-value="settings.newsletter_maillist.subscription"/>
            </div>
          </div>
        </div>

        <div class="ng-cloak" ng-show="settings.newsletter_subscriptionType === 'create_subscriptor'">
          {t escape=off 1=$smarty.capture.subscriptors}You've choosen to use the <a href="%1" target="_blank">internal subscription list</a> to send your newsletters.{/t}
        </div>

        {is_module_activated name="es.openhost.module.acton"}
        <div ng-if="settings.newsletter_subscriptionType == 'acton'">
          <h5>
            {t}Act-On marketing lists{/t}
          </h5>

          <div class="actonList ng-cloak" ng-repeat="list in settings['actOn.marketingLists']">
            <div class="row">
              <div class="form-group col-md-6">
                <label for="acton_marketing_list_name-[% $index %]" class="form-label">{t}List name{/t}</label>
                <div class="controls">
                  <input id="acton_marketing_list_name-[% $index %]" class="form-control" type="text" ng-model="list.name">
                </div>
              </div>
              <div class="form-group col-md-5">
                <label for="acton_marketing_list_id-[% $index %]" class="form-label">{t}List id{/t}</label>
                <div class="controls">
                  <input id="acton_marketing_list_id-[% $index %]"class="form-control" type="text" ng-model="list.id">
                </div>
              </div>
              <div class="form-group col-md-1 text-left" style="padding-top: 35px;">
                <button class="btn btn-danger btn-block" ng-click="removeList($index)">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
          </div>

          <a ng-click="addList()" class="btn btn-block">{t}Add new marketing list{/t}</a>
        </div>
        {/is_module_activated}
      </div>
    </div>

    <div class="grid simple ng-cloak" ng-if="!flags.loading && settings">
      <div class="grid-title">
        <h4>
          <i class="fa fa-pencil"></i>
          {t}Newsletter contents{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <label for="name" class="form-label">{t}Newsletter subject{/t}</label>
          <span class="help">{t}The subject of the emails in this newsletter{/t}</span>
          <div class="controls">
            <input type="text" required id="name" name="newsletter_maillist[name]" ng-value="settings.newsletter_maillist.name" class="form-control" placeholder="{t}Your newsletter subject{/t}"/>
          </div>
        </div>
        <div class="form-group">
          <label for="sender" class="form-label">{t}Email from{/t}</label>
          <span class="help">{t escape=off}Email sender{/t} (From)</span>
          <div class="controls">
            <input type="text" required id="sender" name="newsletter_maillist[sender]" ng-value="settings.newsletter_maillist.sender" class="form-control" placeholder="noreply@your_domain_name.com"/>
          </div>
        </div>
        <div class="form-group">
          <label for="newsletter_maillist_link" class="form-label">{t}Newsletter links points to{/t}</label>
          <div class="controls">
            <select name="newsletter_maillist[link]" id="newsletter_maillist_link" ng-model="settings.newsletter_maillist.link">
              <option value="inner">{t}Point to inner{/t}</option>
              <option value="front">{t}Point to frontpage{/t}</option>
            </select>
            <div class="help">{t}You can choose if you prefer that the links of the contents of the newsletter point within the content or contents on the frontpage{/t}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
