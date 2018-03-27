{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="SubscriptionCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_subscriptions_list') %]">
                  <i class="fa fa-check-square-o"></i>
                  {t}Subscriptions{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <h5 class="ng-cloak">
                <strong ng-if="item.pk_user_group">{t}Edit{/t}</strong>
                <strong ng-if="!item.pk_user_group">{t}Create{/t}</strong>
              </h5>
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
            <h3>{t 1=$id}Unable to find any subscription with id "%1".{/t}</h3>
            <h4>{t}Click here to return to the list of subscriptions.{/t}</h4>
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
                  <i class="fa fa-eye m-r-5"></i>
                  {t}Visibility{/t}
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
                  <i class="fa fa-inbox m-r-5"></i>
                  {t}Requests{/t}
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
                    <label class="pointer" for="newsletter">
                      <div class="checkbox">
                        <input id="newsletter" name="newsletter" type="checkbox">
                        <label for="newsletter">{t}Send newsletter{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, this subscription will be selectable when creating newsletters{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">{t}Advertisement{/t}</label>
                    <label class="pointer" for="advertisement">
                      <div class="checkbox">
                        <input id="advertisement" name="advertisement" type="checkbox">
                        <label for="advertisement">{t}Hide advertisements{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, advertisements will be disabled when using safeframe{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">{t}Payment{/t}</label>
                    <label class="pointer" for="payment">
                      <div class="checkbox">
                        <input id="payment" name="payment" type="checkbox">
                        <label for="payment">{t}Requires payment{/t}</label>
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
                    <label class="pointer" for="print">
                      <div class="checkbox">
                        <input id="print" name="print" type="checkbox">
                        <label for="print">{t}Hide print button{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, button to print contents will be hidden{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">&nbsp</label>
                    <label class="pointer" for="social">
                      <div class="checkbox">
                        <input id="social" name="social" type="checkbox">
                        <label for="social">{t}Hide social buttons{/t}</label>
                      </div>
                      <span class="help m-l-3 m-t-6" ng-show="isHelpEnabled()">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If enabled, button to share contents in social networks will be hidden{/t}
                      </span>
                    </label>
                  </div>
                  <div class="form-group no-margin">
                    <label class="form-label m-t-15">&nbsp</label>
                    <label class="pointer" for="edit">
                      <div class="checkbox">
                        <input id="edit" name="edit" type="checkbox">
                        <label for="edit">{t}Block browser actions (cut, copy,...){/t}</label>
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
                <h4>{t}Restrictions for non members{/t}</h4>
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <label class="form-label">{t}Redirection{/t}</label>
                  <label class="pointer" for="redirection">
                    <div class="checkbox">
                      <input id="redirection" name="redirection" ng-model="redirection" type="checkbox">
                      <label for="redirection">{t}Redirect to frontpage{/t}</label>
                    </div>
                    <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, non-members will be redirect to frontpage when accessing contents in this subscription{/t}
                    </span>
                  </label>
                </div>
                <label class="form-label">{t}Hide information{/t}</label>
                <div class="form-group no-margin">
                  <div class="row">
                    <div class="col-xs-4">
                      <div class="checkbox p-b-10">
                        <input id="title" name="title" ng-disabled="redirection" type="checkbox">
                        <label for="title">{t}Hide title{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="summmary" name="summmary" ng-disabled="redirection" type="checkbox">
                        <label for="summmary">{t}Hide summary{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="body" name="body" ng-disabled="redirection" type="checkbox">
                        <label for="body">{t}Hide body{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="title" name="title" ng-disabled="redirection" type="checkbox">
                        <label for="title">{t}Hide pretitle{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="summmary" name="summmary" ng-disabled="redirection" type="checkbox">
                        <label for="summmary">{t}Hide media{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="body" name="body" ng-disabled="redirection" type="checkbox">
                        <label for="body">{t}Hide related contents{/t}</label>
                      </div>
                    </div>
                    <div class="col-xs-4">
                      <div class="checkbox p-b-10">
                        <input id="summmary" name="summmary" ng-disabled="redirection" type="checkbox">
                        <label for="summmary">{t}Hide content information{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="body" name="body" ng-disabled="redirection" type="checkbox">
                        <label for="body">{t}Hide tags{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="print" name="print" ng-disabled="redirection" type="checkbox">
                        <label for="print">{t}Hide print button{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="social" name="social" ng-disabled="redirection" type="checkbox">
                        <label for="social">{t}Hide social buttons{/t}</label>
                      </div>
                      <div class="checkbox p-b-10">
                        <input id="edit" name="edit" ng-disabled="redirection" type="checkbox">
                        <label for="edit">{t}Block browser actions (cut, copy,...){/t}</label>
                      </div>
                    </div>
                  </div>
                  <div class="help m-l-3" ng-show="isHelpEnabled()">
                    <i class="fa fa-info-circle m-r-5 text-info"></i>
                    {t}Some information will be hidden for non-members  when accessing contents in this subscription{/t}
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
