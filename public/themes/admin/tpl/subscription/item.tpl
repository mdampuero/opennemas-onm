{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="SubscriptionCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-list m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_subscriptions_list') %]">
                  {t}Lists{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_subscribers_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="checkbox">
                    <input class="form-control" id="enabled" name="enabled" ng-false-value="0" ng-model="item.enabled" ng-true-value="1" type="checkbox">
                    <label for="enabled" class="form-label">
                      {t}Enabled{/t}
                    </label>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.visibility }" ng-click="expanded.visibility = !expanded.visibility">
                  <i class="fa fa-eye m-r-10"></i>{t}Visibility{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.visibility }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.visibility">
                    <span ng-show="item.private">{t}Private{/t}</span>
                    <span ng-show="!item.private">{t}Public{/t}</span>
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.visibility }">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input class="form-control" id="private" name="private" ng-false-value="0" ng-model="item.private" ng-true-value="1" type="checkbox">
                      <label for="private" class="form-label">
                        {t}Private{/t}
                      </label>
                    </div>
                    <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, subscribers will not see this subscription while registering or editing profile{/t}
                    </span>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.request }" ng-click="expanded.request = !expanded.request">
                  <i class="fa fa-inbox m-r-10"></i>{t}Requests{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.request }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.request">
                    <span ng-show="item.request">{t}Manual{/t}</span>
                    <span ng-show="!item.request">{t}Automatic{/t}</span>
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.request }">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input class="form-control" id="request" name="request" ng-false-value="0" ng-model="item.request" ng-true-value="1" type="checkbox">
                      <label for="request" class="form-label">
                        {t}Accept requests manually{/t}
                      </label>
                    </div>
                    <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, an administrator will have to accept new members manually one by one{/t}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-md-pull-4">
            <div class="grid simple">
              <div class="grid-body">
                <div class="form-group">
                  <label for="name" class="form-label">{t}Name{/t}</label>
                  <div class="controls">
                    <input class="form-control" id="name" name="name" ng-model="item.name" required type="text">
                  </div>
                </div>
              </div>
            </div>
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Features for members{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="col-sm-6">
                  <div class="form-group no-margin">
                    <label class="form-label">{t}Newsletter{/t}</label>
                    <label class="pointer" for="member-newsletter">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_SEND_NEWSLETTER')" id="member-newsletter" type="checkbox">
                        <label for="member-newsletter">{t}Send newsletter{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, this subscription will be selectable when creating newsletters{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">{t}Advertisement{/t}</label>
                    <label class="pointer" for="member-advertisement">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_ADVERTISEMENTS')"id="member-advertisement" type="checkbox">
                        <label for="member-advertisement">{t}Hide advertisements{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, advertisements will be disabled when using safeframe{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">{t}Payment{/t}</label>
                    <label class="pointer" for="member-payment">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_REQUIRES_PAYMENT')" id="member-payment" type="checkbox">
                        <label for="member-payment">{t}Requires payment{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, this subscription will require a payment to become a member{/t}
                      </span>
                    </label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group no-margin">
                    <label class="form-label">{t}Restrictions{/t}</label>
                    <label class="pointer" for="member-print">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_PRINT')" id="member-print" type="checkbox">
                        <label for="member-print">{t}Hide print button{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, button to print contents will be hidden{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">&nbsp</label>
                    <label class="pointer" for="member-social">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_SOCIAL')" id="member-social" type="checkbox">
                        <label for="member-social">{t}Hide social networks buttons{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-6" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, button to share contents in social networks will be hidden{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">&nbsp</label>
                    <label class="pointer" for="member-edit">
                      <div class="checkbox">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_BLOCK_BROWSER')" id="member-edit" type="checkbox">
                        <label for="member-edit">{t}Block browser actions (cut, copy,...){/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, some browser actions (e.g. cut, copy,...) will be blocked{/t}
                      </span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Restrictions for non-members{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="form-label">{t}Redirection{/t}</label>
                  <label class="pointer" for="redirection">
                    <div class="checkbox">
                      <input checklist-model="item.privileges" checklist-value="getPermissionId('NON_MEMBER_REDIRECT')" id="redirection" type="checkbox">
                      <label for="redirection">{t}Redirect to frontpage{/t}</label>
                    </div>
                    <span class="help m-l-3" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, non-members will be redirect to frontpage when accessing contents in this subscription{/t}
                    </span>
                  </label>
                </div>
                <div class="row">
                  <div class="col-xs-6">
                    <label class="form-label">{t}Hide information{/t}</label>
                    <div class="form-group no-margin">
                      <div class="checkbox m-b-5" ng-repeat="permission in data.extra.modules.FRONTEND | filter: { name: 'NON_MEMBER_HIDE' }">
                        <input checklist-model="item.privileges" checklist-value="permission.pk_privilege" id="non-member-[% $index %]" ng-disabled="item.privileges.indexOf(getPermissionId('NON_MEMBER_REDIRECT')) !== -1" type="checkbox">
                        <label for="non-member-[% $index %]">[% permission.description %]</label>
                      </div>
                      <div class="help m-l-3" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}Some information will be hidden for non-members when accessing contents in this subscription{/t}
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group no-margin">
                      <label class="form-label">{t}Block actions{/t}</label>
                      <div class="checkbox p-b-10">
                        <input checklist-model="item.privileges" checklist-value="getPermissionId('NON_MEMBER_BLOCK_BROWSER')" id="non-member-block" ng-disabled="item.privileges.indexOf(getPermissionId('NON_MEMBER_REDIRECT')) !== -1" type="checkbox">
                        <label for="non-member-block">{t}Block browser actions (cut, copy,...){/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, subscribers will not see this subscription while registering or editing profile{/t}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
