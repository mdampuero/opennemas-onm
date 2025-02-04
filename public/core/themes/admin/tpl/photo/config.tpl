{extends file="base/admin.tpl"}
{block name="content"}
  <form ng-controller="PhotoConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-picture-o m-r-10"></i>
                {t}Photos{/t}
              </h4>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li class="quicklinks">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=backend_photos_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="button" ng-click="save()">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple settings">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-md-6">
              <h4>
                <i class="fa fa-object-group"></i>
                {t}Optimize images{/t}
              </h4>
              <div class="form-group">
                <div class="checkbox">
                  <input id="optimize_images" name="optimize-images" type="checkbox" ng-model="config.optimize_images">
                  <label class="form-label" for="optimize_images">
                    <span class="checkbox-title">{t}If set, images will be automaticaly optimized when uploaded and imported{/t}</span>
                  </label>
                </div>
              </div>
              <div class="form-group m-t-10 m-l-20">
                <label class="form-label" for="image_quality">
                  <div>
                    {t}Image quality{/t}
                  </div>
                  <div class="help">
                    {t}Only images with .jpeg, .jpg or .pjpeg format{/t}
                  </div>
                </label>
                <div class="controls">
                  <div class="input-group">
                    <select id="image_quality" name="image_quality" ng-model="config.image_quality">
                      <option value="50">50%</option>
                      <option value="55">55%</option>
                      <option value="60">60%</option>
                      <option value="65">65%</option>
                      <option value="70">70%</option>
                      <option value="75">75%</option>
                      <option value="80">80%</option>
                      <option value="85">85%</option>
                      <option value="90">90%</option>
                      <option value="95">95%</option>
                      <option value="100">100%</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group m-t-20 m-l-20">
                <label class="form-label" for="image_resolution">
                  <div>
                    {t}Image resolution{/t}
                  </div>
                  <div class="help">
                    {t}Regardless of resolution, images will keep its original aspect ratio{/t}
                  </div>
                </label>
                <div class="controls">
                  <div class="input-group">
                    <select id="image_resolution" name="image_resolution" ng-model="config.image_resolution">
                      <option value="1920x1080">1920x1080px (16:9)</option>
                      <option value="1600x900">1600x900px (16:9)</option>
                      <option value="1366x768">1366x768px (16:9)</option>
                      <option value="1280x720">1280x720px (16:9)</option>
                      <option value="1600x1200">1600x1200px (4:3)</option>
                      <option value="1400x1050">1400x1050px (4:3)</option>
                      <option value="1280x960">1280x960px (4:3)</option>
                      <option value="1024x768">1024x768px (4:3)</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group m-t-20 m-l-20">
                <label class="form-label" for="image_type">
                  <div>
                    {t}Image type{/t}
                  </div>
                  <div class="help">
                    {t}This will convert all png images with a height greater than 120px to jpg.{/t}
                  </div>
                </label>
                <div class="checkbox">
                  <input id="convert-png" name="convert-png" type="checkbox" ng-model="config.convert_png">
                  <label class="form-label" for="convert-png">
                    <span class="checkbox-title">{t}Convert png to jpg{/t}</span>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-md-6">
            <h4>
              <i class="fa fa-magic"></i>
              {t}Image format{/t}
            </h4>
              <div class="form-group">
                <div class="checkbox">
                  <input id="image_transform" name="image-transform" type="checkbox" ng-model="config.image_transform">
                  <label class="form-label" for="image_transform">
                    <span class="checkbox-title">{t escape=off}If set, images will be automaticaly transformed{/t}</span>
                    <div class="help">
                      {t}Only images with .jpeg, .jpg or .pjpeg format{/t}
                    </div>
                  </label>
                </div>
              </div>
              <div class="form-group m-l-20">
                <label class="form-label" for="cmp-type">
                  {t}Choose format{/t}
                </label>
                <div class="controls">
                  <div class="radio">
                    <input class="form-control" id="format-webp" ng-model="config.image_format" ng-value="'webp'" type="radio"/>
                    <label for="format-webp">
                      WEBP
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="format-avif" ng-model="config.image_format" ng-value="'avif'" type="radio"/>
                    <label for="format-avif">
                      AVIF
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group m-l-20">
                <label class="form-label" for="cmp-type">
                  {t}More file types{/t}
                </label>
                <div class="controls">
                  <div class="checkbox">
                    <input id="transform-png" name="transform-png" type="checkbox" ng-model="config.transform_png">
                    <label class="form-label" for="transform-png">
                      <span class="checkbox-title">{t}Transform png{/t}</span>
                    </label>
                  </div>
                  {* <div class="checkbox">
                    <input id="transform-gif" name="transform-gif" type="checkbox" ng-model="config.transform_gif">
                    <label class="form-label" for="transform-gif">
                      <span class="checkbox-title">{t}Transform gif{/t}</span>
                    </label>
                  </div> *}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
