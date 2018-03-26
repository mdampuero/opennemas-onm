{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="AuthorCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_authors_list') %]">
                  <i class="fa fa-edit"></i>
                  {t}Authors{/t}
                  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                    <i class="fa fa-question"></i>
                  </a>
                </a>
              </h4>
            </li>
            <li class="quicklinks visible-xs">
              <a class="help-icon" href="http://help.opennemas.com/knowledgebase/articles/566184-opennemas-gesti%C3%B3n-de-autores" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
                <i class="fa fa-question fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.http.loading && item">
              <h5 class="ng-cloak">
                <strong ng-if="item.id">{t}Edit{/t}</strong>
                <strong ng-if="!item.id">{t}Create{/t}</strong>
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  <span class="text">{t}Save{/t}</span>
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
          <a href="[% routing.generate('backend_users_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t 1=$id}Unable to find any user with id "%1".{/t}</h3>
            <h4>{t}Click here to return to the list of users.{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-sm-8">
            <div class="grid simple">
              <div class="grid-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="thumbnail-wrapper">
                      <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.photo1 }"></div>
                      <div class="thumbnail-placeholder thumbnail-placeholder-small m-b-15">
                        <div class="img-thumbnail img-thumbnail-circle" ng-if="!item.avatar_img_id">
                          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" ng-click="form.$setDirty(true)">
                            <i class="fa fa-picture-o fa-3x"></i>
                          </div>
                        </div>
                        <div class="dynamic-image-placeholder ng-cloak" ng-show="item.avatar_img_id">
                          <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.avatar_img_id" only-image="true">
                            <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" media-picker-type="photo" ng-click="form.$setDirty(true)"></div>
                          </dynamic-image>
                        </div>
                      </div>
                      <div class="row text-center">
                        <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-8 col-xs-offset-2">
                          <button class="btn btn-block btn-white m-b-15" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="item.avatar_img_id" media-picker-type="photo" type="button">
                            <i class="fa fa-picture-o"></i>
                            {t}Change{/t}
                          </button>
                        </div>
                        <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-8 col-xs-offset-2">
                          <button class="btn btn-block btn-danger m-b-15" ng-click="item.avatar_img_id = null" ng-disabled="!item.avatar_img_id">
                            <i class="fa fa-trash-o"></i>
                            {t}Remove{/t}
                          </button>
                        </div>
                      </div>
                      <div ng-if="item.facebook_id || item.twitter_id || item.google_id">
                        <h4 class="text-center">
                          {t}Connections{/t}
                        </h4>
                        <div class="row">
                          <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.facebook_id">
                            <button class="btn btn-block btn-facebook m-b-15">
                              <i class="fa fa-facebook"></i>
                              {t}Disconnect{/t}
                            </button>
                          </div>
                          <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.twitter_id">
                            <button class="btn btn-block btn-twitter m-b-15">
                              <i class="fa fa-twitter"></i>
                              {t}Disconnect{/t}
                            </button>
                          </div>
                          <div class="col-lg-8 col-lg-offset-2 col-md-12 col-md-offset-0 col-xs-9 col-xs-offset-2" ng-if="item.google_id">
                            <button class="btn btn-block btn-google m-b-15">
                              <i class="fa fa-google-plus"></i>
                              {t}Disconnect{/t}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group" ng-class="{ 'has-error': form.name.$dirty && form.name.$invalid }">
                      <label class="form-label" for="name">{t}Name{/t}</label>
                      <div class="controls input-with-icon right">
                          <input class="form-control" id="name" name="name" ng-model="item.name" ng-maxlength="50" type="text"/>
                        <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                          <span class="fa fa-check text-success" ng-if="form.name.$dirty && form.name.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
                      </div>
                    </div>
                    <div class="form-group" ng-class="{ 'has-error': form.email.$dirty && form.email.$invalid }">
                      <label class="form-label" for="email">{t}Email{/t}</label>
                      <div class="controls input-with-icon right">
                        <span class="icon right" ng-if="!flags.http.loading">
                          <span class="fa fa-check text-success" ng-if="form.email.$dirty && form.email.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="form.email.$dirty && form.email.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
                        <input class="form-control" id="email" name="email" placeholder="johndoe@example.org"  ng-model="item.email" required type="email">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label" for="twitter">
                            {t}Twitter user{/t}
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-addon btn-twitter">
                                <i class="fa fa-twitter"></i>
                              </span>
                              <input class="form-control" id="twitter" name="twitter" ng-model="item.twitter" placeholder="{t}Username{/t}" type="text">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label" for="facebook">
                            {t}Facebook user{/t}
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-addon btn-facebook">
                                <i class="fa fa-facebook"></i>
                              </span>
                              <input class="form-control" id="facebook" name="facebook" ng-model="item.facebook" placeholder="{t}Username{/t}" type="text">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="url">
                        {t}Blog URL{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="url" name="url" ng-model="item.url" placeholder="http://" type="text">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="bio">
                        {t}Short Biography{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="bio" name="bio" ng-model="item.bio" type="text">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="bio_description">
                        {t}Biography{/t}
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="bio_description" name="bio_description" ng-model="item.bio_description" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="form-group">
                    <div class="checkbox">
                      <input id="inrss" name="inrss" ng-false-value="'0'" ng-model="item.inrss" ng-true-value="'1'" type="checkbox">
                      <label class="form-label" for="inrss">
                        {t}Show in RSS{/t}
                      </label>
                      <div class="help m-t-5">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If this option is activated this author will be showed in rss{/t}
                      </div>
                    </div>
                  </div>
                  {is_module_activated name="BLOG_MANAGER"}
                    <div class="form-group no-margin">
                      <div class="checkbox">
                        <input name="is_blog" id="is_blog" ng-false-value="'0'" ng-model="item.is_blog" ng-true-value="'1'" type="checkbox">
                        <label class="form-label" for="is_blog">
                          {t}View as Blog{/t}
                        </label>
                      </div>
                      <div class="help m-t-5">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t}If this option is activated page author will be showed as blog{/t}
                      </div>
                    </div>
                  {/is_module_activated}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
