<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-list"></i>
      {t}Related contents{/t}
    </h4>
  </div>
  <div class="grid-body">
    <div class="m-b-40">
      <div class="clearfix">
        <h5 class="pull-left">{t}Related in frontpage{/t}</h5>
        <button class="btn btn-white btn-mini pull-right m-t-5" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="data.article.relatedFront" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </button>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="data.article.relatedFront">
        <div class="related-item" ng-repeat="content in data.article.relatedFront">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('data.article.relatedFront', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="m-b-40">
      <div class="clearfix">
        <h5 class="pull-left">{t}Related in inner{/t}</h5>
        <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="data.article.relatedInner" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </button>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="data.article.relatedInner">
        <div class="related-item" ng-repeat="content in data.article.relatedInner">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('data.article.relatedInner', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    {is_module_activated name="CRONICAS_MODULES"}
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Related in home{/t}</h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="data.article.relatedHome" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
            <i class="fa fa-plus"></i>
            {t}Add contents{/t}
          </button>
        </div>
        <div ui-sortable class="ng-cloak" ng-model="data.article.relatedHome">
          <div class="related-item" ng-repeat="content in data.article.relatedHome">
            <div class="related-item-info">
              <span class="sort-icon"></span>
              [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
            </div>
            <button class="btn btn-white" ng-click="removeItem('data.article.relatedHome', $index)">
              <i class="fa fa-trash-o text-danger"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for frontpage{/t} <small>*{t}Only one album{/t}</small></h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="article.params.withGallery" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
        <div class="related-item ng-cloak" ng-if="article.params.withGallery">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.params.withGallery.content_type_name %] - [% article.params.withGallery.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.params.withGallery')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for inner{/t} <small>*{t}Only one album{/t}</small></h5>
           <button class="btn btn-white btn-mini pull-right" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="article.params.withGalleryInt" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
          <div class="related-item ng-cloak" ng-if="article.params.withGalleryInt">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.params.withGalleryInt.content_type_name %] - [% article.params.withGalleryInt.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.params.withGalleryInt')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div>
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for Home{/t} <small>*{t}Only one album{/t}</small></h5>
          <div class="btn btn-white btn-mini pull-right" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="article.params.withGalleryHome" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </div>
        </div>
        <div class="related-item ng-cloak" ng-if="article.params.withGalleryHome">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.params.withGalleryHome.content_type_name %] - [% article.params.withGalleryHome.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.params.withGalleryHome')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    {/is_module_activated}
  </div>
</div>
