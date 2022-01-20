{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Subscriptions{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="SubscriptionCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-list m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_subscriptions_list') %]">
    {t}Subscriptions{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <a class="btn btn-link" class="" ng-click="expansibleSettings()" title="{t 1=_('Subscription')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>
{/block}

{block name="rightColumn"}
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
          <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}If enabled, subscribers will not see this subscription while registering or editing profile{/t}
          </div>
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
          <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}If enabled, an administrator will have to accept new members manually one by one{/t}
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="commonFields"}
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('visibility')">
    <input id="checkbox-visibility" checklist-model="app.fields[contentKey].selected" checklist-value="'visibility'" type="checkbox">
    <label for="checkbox-visibility">
      {t}Visibility{/t}
    </label>
  </div>
    <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('request')">
    <input id="checkbox-request" checklist-model="app.fields[contentKey].selected" checklist-value="'request'" type="checkbox">
    <label for="checkbox-request">
      {t}Requests{/t}
    </label>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="form-group no-margin">
        <label for="name" class="form-label">{t}Name{/t}</label>
        <div class="controls controls-validation">
          <input class="form-control" id="name" name="name" ng-model="item.name" required type="text">
          {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iFlag="name" iForm="form.name" iNgModel="item.name" iRequired=true iValidation=true}
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple" ng-mouseenter="showNewsletter = true" ng-mouseleave="showNewsletter = false">
    {is_module_activated deactivated="1" name="NEWSLETTER_MANAGER"}
      <div class="overlay overlay-border overlay-white ng-cloak p-b-15 p-l-15 p-r-15 p-t-15 text-center" ng-class="{ 'open': showNewsletter }">
        <h4 class="semi-bold">
          {t}Do you want this feature?{/t}
        </h4>
        <a class="btn btn-info m-t-5" href="{url name="admin_store_list"}">
          <h5 class="bold text-uppercase text-white">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Go to Store{/t}
          </h5>
        </a>
      </div>
    {/is_module_activated}
    <div class="grid-title">
      <h4>
        <i class="fa fa-envelope"></i>
        {t}Newsletter{/t}
      </h4>
    </div>
    <div class="grid-body">
      <div class="form-group no-margin">
        <label class="pointer" for="member-newsletter">
          <div class="checkbox">
            <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_SEND_NEWSLETTER')" id="member-newsletter" {is_module_activated deactivated="1" name="NEWSLETTER_MANAGER"}ng-disabled="true"{/is_module_activated} type="checkbox">
            <label for="member-newsletter">{t}Send newsletter{/t}</label>
          </div>
          <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}If enabled, this subscription will be selectable when creating newsletters{/t}
          </div>
        </label>
      </div>
    </div>
  </div>
  <div class="grid simple" ng-mouseenter="showMember = true" ng-mouseleave="showMember = false">
    {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}
      <div class="overlay overlay-border overlay-white ng-cloak p-b-15 p-l-15 p-r-15 p-t-15 text-center" ng-class="{ 'open': showMember }">
        <h4 class="semi-bold m-t-50">
          {t}Do you want this feature?{/t}
        </h4>
        <a class="btn btn-info m-t-5" href="{url name="admin_store_list"}">
          <h5 class="bold text-uppercase text-white">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Go to Store{/t}
          </h5>
        </a>
      </div>
    {/is_module_activated}
    <div class="grid-title">
      <h4>
        <i class="fa fa-address-card"></i>
        {t}Restrictions for members{/t}
      </h4>
    </div>
    <div class="grid-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label class="pointer" for="member-print">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_PRINT')" id="member-print" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                <label for="member-print">{t}Hide print button{/t}</label>
              </div>
              <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, button to print contents will be hidden{/t}
              </div>
            </label>
          </div>
          <div class="form-group">
            <label class="pointer" for="member-social">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_SOCIAL')" id="member-social" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                <label for="member-social">{t}Hide social networks buttons{/t}</label>
              </div>
              <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, button to share contents in social networks will be hidden{/t}
              </div>
            </label>
          </div>
          <div class="form-group no-margin">
            <label class="pointer" for="member-edit">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_BLOCK_BROWSER')" id="member-edit" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                <label for="member-edit">{t}Block browser actions (cut, copy,...){/t}</label>
              </div>
              <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, some browser actions (e.g. cut, copy,...) will be blocked{/t}
              </div>
            </label>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label class="pointer" for="member-advertisement">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_HIDE_ADVERTISEMENTS')" id="member-advertisement" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                <label for="member-advertisement">{t}Hide advertisements{/t}</label>
              </div>
              <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, advertisements will be disabled when using safeframe{/t}
              </div>
            </label>
          </div>
          {*<div class="form-group no-margin">
            <label class="pointer" for="member-payment">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('MEMBER_REQUIRES_PAYMENT')" id="member-payment" ng-disabled="true" type="checkbox">
                <label for="member-payment">{t}Requires payment{/t}</label>
              </div>
              <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, this subscription will require a payment to become a member{/t}
              </span>
            </label>
          </div>*}
        </div>
      </div>
    </div>
  </div>
  <div class="grid simple" ng-mouseenter="showNonMember = true" ng-mouseleave="showNonMember = false">
    {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}
      <div class="overlay overlay-border overlay-white ng-cloak p-b-15 p-l-15 p-r-15 p-t-15 text-center" ng-class="{ 'open': showNonMember }">
        <h4 class="semi-bold m-t-50 p-t-50">
          {t}Do you want this feature?{/t}
        </h4>
        <a class="btn btn-info m-t-5" href="{url name="admin_store_list"}">
          <h5 class="bold text-uppercase text-white">
            <i class="fa fa-shopping-cart m-r-5"></i>
            {t}Go to Store{/t}
          </h5>
        </a>
      </div>
    {/is_module_activated}
    <div class="grid-title">
      <h4>
        <i class="fa fa-ban"></i>
        {t}Restrictions for non-members{/t}
      </h4>
    </div>
    <div class="grid-body">
      <div class="row">
        <div class="col-xs-6">
          <label class="form-label">{t}Indexation{/t}</label>
          <div class="form-group m-t-5">
            <label class="pointer" for="non-member-no-index">
              <div class="checkbox">
                <input checklist-model="item.privileges" checklist-value="getPermissionId('NON_MEMBER_NO_INDEX')" id="non-member-no-index" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                <label for="non-member-no-index">{t}Prevent search engine indexation{/t}</label>
              </div>
              <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                <i class="fa fa-info-circle m-r-5 text-info"></i>
                {t}If enabled, the noindex directive will be added to the HTML{/t}
              </div>
            </label>
          </div>
          <div class="form-group no-margin">
            <label class="form-label">{t}Block actions{/t}</label>
            <div class="form-group m-t-5">
              <label class="pointer" for="non-member-block-access">
                <div class="checkbox">
                  <input checklist-model="item.privileges" checklist-value="getPermissionId('NON_MEMBER_BLOCK_ACCESS')" id="non-member-block-access" {is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}ng-disabled="true"{/is_module_activated} type="checkbox">
                  <label for="non-member-block-access">{t}Block access to content{/t}</label>
                </div>
                <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                  <i class="fa fa-info-circle m-r-5 text-info"></i>
                  {t}If enabled, non-members will not be able to access to contents in this subscription{/t}
                </div>
                <div class="help m-l-3 m-t-5">
                  <i class="fa fa-warning m-r-5 text-warning"></i>
                  {t}The content will not be available in frontpages, widgets, RSS and sitemaps{/t}
                </div>
              </label>
            </div>
            <div class="form-group no-margin">
              <label class="pointer" for="non-member-block">
                <div class="checkbox">
                  <input checklist-model="item.privileges" checklist-value="getPermissionId('NON_MEMBER_BLOCK_BROWSER')" id="non-member-block" ng-disabled="{is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}true ||{/is_module_activated}item.privileges.indexOf(getPermissionId('NON_MEMBER_BLOCK_ACCESS')) !== -1" type="checkbox">
                  <label for="non-member-block">{t}Block browser actions (cut, copy,...){/t}</label>
                </div>
                <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                  <i class="fa fa-info-circle m-r-5 text-info"></i>
                  {t}If enabled, subscribers will not see this subscription while registering or editing profile{/t}.
                </div>
              </label>
            </div>
          </div>
        </div>
        <div class="col-xs-6">
          <label class="form-label">
            {t}Hide information{/t}
          </label>
          <div class="form-group no-margin">
            <div class="checkbox m-b-5" ng-repeat="permission in data.extra.modules.FRONTEND | filter: { name: 'NON_MEMBER_HIDE' }">
              <input checklist-model="item.privileges" checklist-value="permission.id" id="non-member-[% $index %]" ng-checked="item.privileges.indexOf(permission.id) !== -1 || item.privileges.indexOf(getPermissionId('NON_MEMBER_BLOCK_ACCESS')) !== -1" ng-disabled="{is_module_activated deactivated="1" name="es.openhost.module.advancedSubscription"}true ||{/is_module_activated}item.privileges.indexOf(getPermissionId('NON_MEMBER_BLOCK_ACCESS')) !== -1" type="checkbox">
              <label for="non-member-[% $index %]">[% permission.description %]</label>
            </div>
            <div class="help m-l-3" ng-show="isHelpEnabled()">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}Some information will be hidden for non-members when accessing contents in this subscription{/t}.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
