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
            <li class="hidden-xs quicklinks m-l-5 m-r-5 ng-cloak" ng-if="hasMultilanguage()">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="hidden-xs ng-cloak quicklinks" ng-if="hasMultilanguage()">
              <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
            </li>
          </ul>
          <div class="pull-right">
            <ul class="quick-section">
              {block name="primaryActions"}
                <li class="quicklinks">
                  <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="button">
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
            <a href="[% routing.generate('backend_videos_list') %]">
              <i class="fa fa-4x fa-warning text-warning"></i>
              <h3>{t}Unable to find the item{/t}</h3>
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
    {block name="modals"}
    <div class="modal fade" id="modal-image-location">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">{t}Pick a location for the image{/t}</h4>
        </div>
        <div class="modal-body">
          <div id="geolocation" class="has-map">
            <div class="form-group">
              <div class="input-group">
                <input type="text" class="form-control address_search_input noentersubmit">
                <span class="input-group-btn">
                  <button class="geolocate_user_button btn btn-default" rel="tooltip" data-placement="left" data-original-title="{t}Geolocate photo with my position{/t}">
                    <i class="fa fa-location-arrow"></i>
                  </button>
                  <button class="btn btn-default" class="geocode_button"/>
                    <i class="fa fa-search"></i>
                  </button>
                </span>
              </div>
              <input type="hidden" class="final_address" value="">
            </div>
            <div class="map">
              <div id="map_canvas"></div>
            </div>
           </div><!-- /geolocation -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary accept" href="#" type="button">
            {t}Assign location{/t}
          </a>
        </div>
      </div>
    </div>
    </div>
    {/block}
  </form>
{/block}
