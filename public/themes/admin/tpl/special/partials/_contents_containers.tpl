<div class="grid simple">
  <div class="grid-title">
    <h4>{t}Special contents{/t}</h4>
  </div>
  <div class="grid-body">
    <div class="row">
      <div class="col-md-6" {if isset($contentsLeft)}ng-init="contentsLeft = {json_encode($contentsLeft)|clear_json}"{/if}>
        <h5>
          {t}Articles in left column{/t}
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="">
          <div class="related" ui-tree-nodes="" ng-model="contentsLeft">
            <div class="related-item" ng-repeat="content in contentsLeft" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': content.content_type_name == 'article', 'fa-quote-right': content.content_type_name == 'opinion', 'fa-pie-chart': content.content_type_name == 'poll', 'fa-file': content.content_type_name == 'static_page', 'fa-envelope': content.content_type_name == 'letter', 'fa-paperclip': content.content_type_name == 'attachment', 'fa-film': content.content_type_name == 'video', 'fa-stack-overflow': content.content_type_name == 'album'  }" uib-tooltip="[% content.content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% content.title %]
                </span>
                <span class="related-item-status" ng-if="content.content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('contentsLeft', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
          <div class="content-placeholder text-center">
            <button class="btn btn-default" content-picker content-picker-ignore="[% getContentIds() %]"content-picker-selection="true" content-picker-max-size="30" content-picker-target="tmp.contentsLeft" content-picker-type="album,article,opinion,poll,video" content-picker-view="list-item" type="button">
              <i class="fa fa-plus"></i>
              {t}Add{/t}
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-6" {if isset($contentsRight)}ng-init="contentsRight = {json_encode($contentsRight)|clear_json}"{/if}>
        <h5>{t}Articles in right column{/t}</h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="">
          <div class="related" ui-tree-nodes="" ng-model="contentsRight">
            <div class="related-item" ng-repeat="content in contentsRight" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': content.content_type_name == 'article', 'fa-quote-right': content.content_type_name == 'opinion', 'fa-pie-chart': content.content_type_name == 'poll', 'fa-file': content.content_type_name == 'static_page', 'fa-envelope': content.content_type_name == 'letter', 'fa-paperclip': content.content_type_name == 'attachment', 'fa-film': content.content_type_name == 'video', 'fa-stack-overflow': content.content_type_name == 'album'  }" uib-tooltip="[% content.content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% content.title %]
                </span>
                <span class="related-item-status" ng-if="content.content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('contentsRight', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
          <div class="content-placeholder text-center">
            <button class="btn btn-default" content-picker content-picker-ignore="[% getContentIds() %]" content-picker-selection="true" content-picker-max-size="30" content-picker-target="tmp.contentsRight" content-picker-type="album,article,opinion,poll,video" content-picker-view="list-item" type="button">
              <i class="fa fa-plus"></i>
              {t}Add{/t}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="noticias_right_input" name="noticias_right_input" ng-value="relatedRight">
<input type="hidden" id="noticias_left_input" name="noticias_left_input" ng-value="relatedLeft">
