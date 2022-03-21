{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" {block name="ngInit"}{/block}>
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                {block name="icon"}{/block}
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                {block name="title"}{/block}
              </h4>
            </li>
            {block name="extraTitle"}{/block}
            <li class="hidden-xs m-l-5 m-r-5 quicklinks">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="hidden-xs quicklinks">
              <h4>{if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}</h4>
            </li>
            {block name="translator"}
              <li class="hidden-xs quicklinks m-l-5 m-r-5 ng-cloak" ng-if="hasMultilanguage()">
                <h4>
                  <i class="fa fa-angle-right"></i>
                </h4>
              </li>
              <li class="hidden-xs ng-cloak quicklinks" ng-if="hasMultilanguage()">
                <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
              </li>
            {/block}
          </ul>
          <div class="pull-right">
            <ul class="quick-section">
              {block name="primaryActions"}
                <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
                  <h5>
                    <i class="p-r-15">
                      <i class="fa fa-check"></i>
                      {t}Draft saved at {/t}[% draftSaved %]
                    </i>
                  </h5>
                </li>
                <li class="quicklinks">
                  <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" type="button">
                    <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                    {t}Save{/t}
                  </button>
                </li>
              {/block}
            </ul>
          </div>
        </div>
      </div>
    </div>
    {block name="grid"}
      <div class="content">
        <div class="listing-no-contents" ng-hide="!flags.http.loading">
          <div class="text-center p-b-15 p-t-15">
            <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
            <h3 class="spinner-text">{t}Loading{/t}...</h3>
          </div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-show="!flags.http.loading && item === null">
          <div class="text-center p-b-15 p-t-15">
            <a href="[% routing.generate(routes.list) %]">
              <i class="fa fa-4x fa-warning text-warning"></i>
              <h3>{t}The item doesn't exists or you don't have permission to see it.{/t}</h3>
              <h4>{t}Click here to return to the list{/t}</h4>
            </a>
          </div>
        </div>
        <div class="row ng-cloak" ng-show="!flags.http.loading && flags.visible.grid && item">
          <div class="col-md-4 col-md-push-8">
            {block name="rightColumn"}{/block}
          </div>
          <div class="col-md-8 col-md-pull-4">

            {block name="leftColumn"}{/block}
          </div>
        </div>
      </div>
    {/block}
    {block name="modals"}{/block}
  </form>
{/block}
