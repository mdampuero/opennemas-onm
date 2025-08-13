{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Videos{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="VideoCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-film m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_videos_list}">
    {t}Videos{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
        <h5>
          <i class="p-r-15">
            <i class="fa fa-check"></i>
            {t}Draft saved at {/t}[% draftSaved %]
          </i>
        </h5>
      </li>
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Video')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()"
          ng-disabled="flags.http.loading || flags.http.saving || (item.pk_content == undefined && item.type === 'upload') || (item.type === 'upload' && item.path == '')"
          type="button">
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
        {acl isAllowed="VIDEO_AVAILABLE"}
        {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}"
        field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="VIDEO_FAVORITE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
        <div class="m-t-5">
          {acl isAllowed="VIDEO_HOME"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
          {/acl}
        </div>
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
      {include file="ui/component/content-editor/accordion/seo-input.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iRequired="item.type == 'script' || item.type == 'external'" 
        iTitle="{t}Featured in frontpage{/t}" types="photo"}
      {include file="ui/component/content-editor/accordion/additional-data.tpl"}
    </div>
  </div>
  {if !empty({setting name=seo_information})}
    <div class="grid simple" ng-if="!hasMultilanguage()">
      <div class="grid-body no-padding">
        <div class="grid-collapse-title">
          <i class="fa fa-search m-r-10"></i> {t}SEO Information{/t}
        </div>
        {include file="ui/component/content-editor/accordion/seo_info.tpl"}
      </div>
    {/if}
  {/block}

  {block name="leftColumn"}
    <div class="grid simple">
      <div class="grid-body">
        <div ng-if="!item.pk_content" class="video-type-selector form-group">
          <div class="row">
            {is_module_activated name="es.openhost.module.storage"}
            <div class="col-sm-3">
              <button ng-click="selectType('upload')" ng-class="{ 'selected' : item.type == 'upload'}"
                class="clearfix btn btn-white video-type-selector-button">
                <div class="video-selector-icon">
                  <i class="fa fa-upload fa-3x"></i>
                </div>
                <div class="video-selector-text">
                  {t}Upload video file{/t}
                </div>
              </button>
            </div>
            {/is_module_activated}
            <div ng-class="data.extra.storage_module ? 'col-sm-3' : 'col-sm-4'">
              <button ng-click="selectType('web-source')" ng-class="{ 'selected' : item.type == 'web-source' }"
                class="clearfix btn btn-white video-type-selector-button">
                <div class="video-selector-icon">
                  <i class="fa fa-youtube fa-3x"></i>
                </div>
                <div class="video-selector-text">
                  {t}Link video from other services{/t}
                </div>
              </button>
            </div>
            <div ng-class="data.extra.storage_module ? 'col-sm-3' : 'col-sm-4'">
              <button ng-click="selectType('script')" ng-class="{ 'selected' : item.type == 'script' }"
                class="clearfix btn btn-white video-type-selector-button">
                <div class="video-selector-icon">
                  <i class="fa fa-file-code-o fa-3x"></i>
                </div>
                <div class="video-selector-text">
                  {t}Use HTML code{/t}
                </div>
              </button>
            </div>
            <div ng-class="data.extra.storage_module ? 'col-sm-3' : 'col-sm-4'">
              <button ng-click="selectType('external')" ng-class="{ 'selected' : item.type == 'external' }"
                class="clearfix btn btn-white video-type-selector-button">
                <div class="video-selector-icon">
                  <i class="fa fa-film fa-3x"></i>
                </div>
                <div class="video-selector-text">
                  {t}Use file video URLs{/t}
                </div>
              </button>
            </div>
          </div>
        </div>
        <span ng-if="item.type !== 'external' && item.type !== 'script' && item.type !== 'upload'">
          <div class="form-group">
            <label for="video_url" class="form-label">{t}Video URL{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input type="text" id="video_url" name="video_url" ng-model="item.path" required class="form-control" />
                <span class="input-group-btn">
                  <button class="btn btn-primary" id="video_url_button" type="button" ng-click="getVideoData()"
                    ng-disabled="!item.path">
                    <span class="fa fa-refresh"></span>
                    <span class="hidden-xs">{t}Get information{/t}</span>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </span>
        <ng-container ng-if="item.type !== 'upload' || item.pk_content">
          {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}"
          iValidation=true AI=true AIFieldType="titles"}
          {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5
          imagepicker=true AI=true AIFieldType="descriptions"}
        </ng-container>
        <div class="row">
          <div class="col-lg-12" ng-if="item.type === 'upload'">
            <div class="m-t-0 m-b-30" ng-if="!item.pk_content">
              <div class="upload-dropzone" ng-class="{ 'dragover': isDragOver }" ng-click="triggerFileInput()"
                ng-drop="true" ondragover="angular.element(this).scope().onDragOver(event)"
                ondragleave="angular.element(this).scope().onDragLeave(event)"
                ondrop="angular.element(this).scope().onDrop(event)">
                <div class="text-center">
                  <i class="fa fa-folder-open fa-3x text-warning" ng-if="uploading === -1"></i>
                  <i class="fa fa-cloud-upload fa-3x text-info" ng-if="uploading === 0"></i>
                  <i class="fa fa-check-circle fa-3x text-success" ng-if="uploading === 1"></i>
                </div>
                <input type="file" id="fileInput" style="display: none"
                  onchange="angular.element(this).scope().setFile(this.files)">
                <p ng-if="uploading !== 1"><strong>{t}Click or drag a file here{/t}</strong></p>
                <p ng-if="uploading === 1"><strong>{t}Redirecting{/t}...</strong></p>
                <div class="m-t-15 m-b-30" ng-if="progress >= 0 && !uploadComplete && !uploadError">
                  <div class="progress" style="margin-top: 10px; height: 20px">
                    <div class="progress-bar" role="progressbar" aria-valuenow="[% progress %]" aria-valuemin="0"
                      aria-valuemax="100"
                      style="width: [% progress %]%; transition: none !important; -webkit-transition: none !important;">
                      <b>[% progress %]%</b>
                    </div>
                  </div>
                  <div class="text-center">
                    <p>{t}Uploading{/t} <b>[% uploadedSizeMB %] {t}to{/t} [% totalSizeMB %] MB</b> (
                      {t}Estimated time remaining:{/t} <b>[% estimatedTimeRemaining %]</b>)</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="m-t-0 m-b-30" ng-if="item.pk_content">
              <div class="upload-file-list">
                <div class="file-item">
                  <div class="file-info">
                    <i class="fa fa-video-camera fa-lg text-[% item.information.step.styleClass %]"></i>
                    <span class="file-name">[% item.information.fileName %]</span>

                    <input type="hidden" ng-model="item.path" class="form-control" />
                    <span class="file-size">[% item.information.fileSizeMB %] MB</span>
                  </div>
                  <span class="badge badge-[% item.information.step.styleClass %]">
                    [% item.information.step.label %] [% item.information.step.progress %]
                  </span>
                </div>
                <ng-container ng-if="item.path">
                  <div class="input-group m-t-20">
                    <input type="text" class="form-control" ng-model="item.path" readonly="readonly" />
                    <span class="input-group-btn">
                      <button class="btn btn-default" ng-click="copyPath()" tooltip="{t}Copy URL{/t}">
                        <span class="glyphicon glyphicon-copy"></span>
                      </button>
                    </span>
                  </div>
                  <div class="form-group" ng-if="getItemId(item) && (preview.webm || preview.ogg || preview.mp4)">
                    <div class="controls">
                      <div class="thumbnail inline w-100" style="line-height: 0;">
                        <video style="margin: 0 auto; width:100%" controls>
                          <source ng-src="[% item.path %]" type="video/mp4">
                        </video>
                      </div>
                    </div>
                  </div>
                </ng-container>
              </div>
            </div>
          </div>
        </div>
        <span ng-if="item.type === 'external'">
          <div class="form-group">
            <div class="controls">
              <div class="input-group">
                <span class="input-group-addon">{t}MP4 format{/t}</span>
                <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.mp4{/t}"
                  name="information[source][mp4]" ng-model="item.information.source.mp4"
                  aria-describedby="basic-addon-mp4">
              </div>
              <br>
              <div class="input-group">
                <span class="input-group-addon">{t}Ogg format{/t}</span>
                <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.ogg{/t}"
                  name="information[source][ogg]" ng-model="item.information.source.ogg"
                  aria-describedby="basic-addon-ogg">
              </div>
              <br>
              <div class="input-group">
                <span class="input-group-addon">{t}WebM format{/t}</span>
                <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.webm{/t}"
                  name="information[source][webm]" ng-model="item.information.source.webm"
                  aria-describedby="basic-addon-webm">
              </div>
            </div>
          </div>
          <div class="form-group" ng-if="getItemId(item) && (preview.webm || preview.ogg || preview.mp4)">
            <label class="form-label">{t}Preview{/t}</label>
            <div class="controls">
              <div class="thumbnail inline" style="line-height: 0;">
                <video style="margin: 0 auto; width:100%" controls>
                  <source ng-if="preview.webm" ng-src="[% preview.webm %]" type="video/webm">
                  <source ng-if="preview.ogg" ng-src="[% preview.ogg %]" type="video/ogg">
                  <source ng-if="preview.mp4" ng-src="[% preview.mp4 %]" type="video/mp4">
                </video>
              </div>
            </div>
          </div>
        </span>
        <span ng-if="item.type === 'script'">
          <div class="form-group">
            <label for="video-information" class="form-label">{t}Write HTML code{/t}</label>
            <div class="controls">
              <textarea name="body" id="body" ng-model="item.body" rows="8" class="form-control"></textarea>
            </div>
          </div>
          <div class="form-group m-t-10" ng-if="item">
            <label class="form-label">{t}Preview{/t}</label>
            <div ng-bind-html="trustHTML(item.body)" style="width:100%; text-align:center; margin:0 auto;"> </div>
          </div>
        </span>
        <span ng-if="item.type !== 'external' && item.type !== 'script'">
          <div id="video-information">
            <div class="listing-no-contents" ng-show="flags.http.fetch_video_info">
              <div class="text-center p-b-15 p-t-15">
                <i class="fa fa-3x fa-circle-o-notch fa-spin text-info"></i>
                <h5 class="spinner-text">{t}Loading video information{/t}...</h5>
              </div>
            </div>
            <div class="ng-cloak" ng-show="item.information.service && !flags.http.fetch_video_info">
              <label for="preview" class="form-label">
                {t}Video preview{/t}
              </label>
              <div class="row">
                <div class="col-md-6">
                  <div class="thumbnail center" ng-if="item">
                    <div class="video-preview" ng-bind-html="trustHTML(item.information.embedHTML)"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="form-label">
                      <strong>
                        {t}Original Title{/t}
                      </strong>
                    </div>
                    <div class="controls">
                      [% item.information.title %]
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="form-label">
                      <strong>
                        {t}Service{/t}
                      </strong>
                    </div>
                    <div class="controls">
                      [% item.information.service %]
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="form-label">
                      <strong>
                        {t}Embed Url{/t}
                      </strong>
                    </div>
                    <div class="controls">
                      <a href="[% item.information.embedUrl %]">
                        [% item.information.embedUrl %]
                      </a>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="form-label">
                      <strong>
                        {t}Thumbnail{/t}
                      </strong>
                    </div>
                    <div class="controls">
                      <img ng-src="[% item.information.thumbnail %]" ng-if="item.information.thumbnail" width="100">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </span>
      </div>
    </div>
  {/block}

  {block name="modals"}
    <script type="text/ng-template" id="modal-draft">
      {include file="common/modals/_draft.tpl"}
      </script>
    <script type="text/ng-template" id="modal-translate">
      {include file="common/modals/_translate.tpl"}
      </script>
    <script type="text/ng-template" id="modal-expansible-fields">
      {include file="common/modals/_modalExpansibleFields.tpl"}
      </script>
    <script type="text/ng-template" id="modal-onmai">
      {include file="common/modals/_modalOnmAI.tpl"}
      </script>
{/block}