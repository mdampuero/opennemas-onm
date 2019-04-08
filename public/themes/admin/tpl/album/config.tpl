{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="AlbumConfigCtrl" ng-init="init()" ng-submit="save()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="[% routing.generate('backend_albums_list') %]">
                <i class="fa fa-home"></i>
                {t}Albums{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>{t}Configuration{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="save($event)" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                {t}Save{/t}
              </button>
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
          <label for="album_settings_total_widget" class="form-label">{t}Total in widget home{/t}</label>
          <span class="help">{t}Use  total in widget album for define how many albums can see in widgets in frontpage{/t}</span>
          <div class="controls">
              <input type="number" name="album_settings_total_widget" id="album_settings_total_widget" ng-model="settings.total_widget" required/>
          </div>
        </div>

        <div class="form-group">
          <label for="album_settings_crop_width" class="form-label">{t}Cover size in widget album (width x height){/t}</label>
          <div class="controls">
            <div class="form-inline-block">
              <input type="number" id="name" name="album_settings_crop_width" ng-model="settings.crop_width" required />
              x
              <input type="number" id="name" name="album_settings_crop_height" ng-model="settings.crop_height" required />
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="album_settings_orderFrontpage" class="form-label">{t}Order album's frontpage by{/t}</label>
          <span class="help">{t}Select if order album's frontpage by most views or albums checked as favorites.{/t}</span>
          <div class="controls">
            <select name="album_settings_orderFrontpage" id="album_setting_orderFrontpage" ng-model="settings.orderFrontpage" required >
              <option value="created" >{t}Created Date{/t}</option>
              <option value="views">{t}Most views{/t}</option>
              <option value="favorite">{t}Favorites{/t}</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="album_time_last" class="form-label">{t}Time of the last album most viewed (days){/t}</label>
          <div class="controls">
            <input type="number" id="name" name="album_settings_time_last" ng-model="settings.time_last" required />
          </div>
        </div>

        <div class="form-group">
          <label for="album_settings_total_front" class="form-label">{t}Total in album frontpage{/t}</label>
          <span class="help">{t}Use this to define how many albums can see in the album frontpage.{/t}</span>
          <div class="controls">
            <input type="number" id="name" name="album_settings_total_front" ng-model="settings.total_front" required />
          </div>
        </div>

        <div class="form-group">
          <label for="album_settings_total_front_more" class="form-label">{t}Total in album frontpage/widget more albums{/t}</label>
          <span class="help">{t}Total number of album on more albums section in album home frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="album_settings_total_front_more" ng-model="settings.total_front_more" required />
          </div>
        </div>
      </div>
    </div>
</div>
</form>
{/block}
