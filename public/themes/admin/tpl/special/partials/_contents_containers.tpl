<div class="grid simple">
  <div class="grid-title">
    <h4>{t}Special contents{/t}</h4>
  </div>
  <div class="grid-body">
    <div class="row">
      <div class="col-md-6" {if isset($contentsLeft)}ng-init="contentsLeft = {json_encode($contentsLeft)|replace:'"':'\''}"{/if}>
        <h5>{t}Articles in left column{/t}</h5>
        <div class="ng-cloak" ui-sortable ng-model="contentsLeft">
          <div class="related-item" ng-repeat="content in contentsLeft">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('contentsLeft', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="contentsLeft" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <button type="button" class="btn btn-primaty">{t}Click here to add contents{/t}</button>
        </div>
      </div>
      <div class="col-md-6" {if isset($contentsRight)}ng-init="contentsRight = {json_encode($contentsRight)|replace:'"':'\''}"{/if}>
        <h5>{t}Articles in right column{/t}</h5>
        <div class="ng-cloak" ui-sortable ng-model="contentsRight">
          <div class="related-item" ng-repeat="content in contentsRight">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('contentsRight', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="contentsRight" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <button type="button" class="btn btn-primaty">{t}Click here to add contents{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="noticias_right_input" name="noticias_right_input" ng-value="relatedRight">
<input type="hidden" id="noticias_left_input" name="noticias_left_input" ng-value="relatedLeft">
