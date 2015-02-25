<div class="grid simple">
  <div class="grid-title">
    <h4>{t}Related contents{/t}</h4>
  </div>
  <div class="grid-body">
    <div class="related-row row">
      <div class="col-md-6" {if isset($orderFront)}ng-init="relatedInFrontpage = {json_encode($orderFront)|replace:'"':'\''}"{/if}>
        <h5>{t}Related in frontpage{/t}</h5>
        <div ui-sortable ng-model="relatedInFrontpage">
          <div class="related-item" ng-repeat="content in relatedInFrontpage">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('relatedInFrontpage', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="relatedInFrontpage" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add contents{/t}</h5>
        </div>
      </div>
      <div class="col-md-6" {if isset($orderInner)}ng-init="relatedInInner = {json_encode($orderInner)|replace:'"':'\''}"{/if}>
        <h5>{t}Related in inner{/t}</h5>
        <div ui-sortable ng-model="relatedInInner">
          <div class="related-item" ng-repeat="content in relatedInInner">
            <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
            <button class="btn btn-white" ng-click="removeItem('relatedInInner', $index)">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
        </div>
        <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="relatedInInner" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
          <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add contents{/t}</h5>
        </div>
      </div>
    </div>
    {is_module_activated name="CRONICAS_MODULES"}
      <div class="related-row row">
        <div class="col-md-6" {if isset($orderHome)}ng-init="relatedInHome = {json_encode($orderHome)|replace:'"':'\''}"{/if}>
          <h5>{t}Related in home{/t}</h5>
          <div ui-sortable ng-model="relatedInHome">
            <div class="related-item" ng-repeat="content in relatedInHome">
              <div class="related-item-info">[% content.content_type_name %] - [% content.title %]</div>
              <button class="btn btn-white" ng-click="removeItem('relatedInHome', $index)">
                <i class="fa fa-times text-danger"></i>
              </button>
            </div>
          </div>
          <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="10" media-picker-target="relatedInHome" media-picker-type="album,article,opinion,poll,video" media-picker-view="list-item">
            <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add contents{/t}</h5>
          </div>
        </div>
        <div class="col-md-6" {if isset($galleries['front']) && $galleries['front']->title}ng-init="galleryForFrontpage = { id: '{$galleries['front']->id}', content_type_name: '{$galleries['front']->content_type_name}', title: '{$galleries['front']->title}' }"{/if}>
          <h5>{t}Gallery for frontpage{/t} <small>*{t}Only one album{/t}</small></h5>
          <div class="related-item" ng-if="galleryForFrontpage">
            <div class="related-item-info">[% galleryForFrontpage.content_type_name %] - [% galleryForFrontpage.title %]</div>
            <button class="btn btn-white" ng-click="removeAlbum('galleryForFrontpage')" type="album">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
          <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="galleryForFrontpage" media-picker-type="album" media-picker-view="list-item">
            <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add gallery{/t}</h5>
          </div>
        </div>
      </div>
      <div class="related-row row">
        <div class="col-md-6" {if isset($galleries['inner']) && $galleries['inner']->title}ng-init="galleryForInner = { id: '{$galleries['inner']->id}', content_type_name: '{$galleries['inner']->content_type_name}', title: '{$galleries['inner']->title}' }"{/if}>
          <h5>{t}Gallery for inner{/t} <small>*{t}Only one album{/t}</small></h5>
            <div class="related-item" ng-if="galleryForInner">
            <div class="related-item-info">[% galleryForInner.content_type_name %] - [% galleryForInner.title %]</div>
            <button class="btn btn-white" ng-click="removeAlbum('galleryForInner')" type="album">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
          <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="galleryForInner" media-picker-type="album" media-picker-view="list-item">
            <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add gallery{/t}</h5>
          </div>
        </div>
        <div class="col-md-6" {if isset($galleries['home']) && $galleries['home']->title}ng-init="galleryForHome = { id: '{$galleries['home']->id}', content_type_name: '{$galleries['home']->content_type_name}', title: '{$galleries['home']->title}' }"{/if}>
          <h5>{t}Gallery for Home{/t} <small>*{t}Only one album{/t}</small></h5>
          <div class="related-item" ng-if="galleryForHome">
            <div class="related-item-info">[% galleryForHome.content_type_name %] - [% galleryForHome.title %]</div>
            <button class="btn btn-white" ng-click="removeAlbum('galleryForHome')" type="album">
              <i class="fa fa-times text-danger"></i>
            </button>
          </div>
          <div class="content-placeholder" media-picker media-picker-selection="true" media-picker-max-size="1" media-picker-target="galleryForHome" media-picker-type="album" media-picker-view="list-item">
            <h5 style="cursor: pointer; margin: 0; padding: 20px 0; text-align: center;">{t}Add gallery{/t}</h5>
          </div>
        </div>
      </div>
    {/is_module_activated}
  </div>
</div>
