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
        <div class="btn btn-white btn-mini pull-right m-t-5" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="article.relatedInFrontpage">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </div>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="article.relatedInFrontpage">
        <div class="related-item" ng-repeat="content in article.relatedInFrontpage">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.relatedInFrontpage', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="m-b-40">
      <div class="clearfix">
        <h5 class="pull-left">{t}Related in inner{/t}</h5>
        <div class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="article.relatedInInner">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </div>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="article.relatedInInner">
        <div class="related-item" ng-repeat="content in article.relatedInInner">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.relatedInInner', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    {is_module_activated name="CRONICAS_MODULES"}
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Related in home{/t}</h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="article.relatedInHome" type="button">
            <i class="fa fa-plus"></i>
            {t}Add contents{/t}
          </button>
        </div>
        <div ui-sortable class="ng-cloak" ng-model="article.relatedInHome">
          <div class="related-item" ng-repeat="content in article.relatedInHome">
            <div class="related-item-info">
              <span class="sort-icon"></span>
              [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
            </div>
            <button class="btn btn-white" ng-click="removeItem('article.relatedInHome', $index)">
              <i class="fa fa-trash-o text-danger"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for frontpage{/t} <small>*{t}Only one album{/t}</small></h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="article.galleryForFrontpage" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
        <div class="related-item ng-cloak" ng-if="article.galleryForFrontpage">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.galleryForFrontpage.content_type_name %] - [% article.galleryForFrontpage.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.galleryForFrontpage')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div class="m-b-40">
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for inner{/t} <small>*{t}Only one album{/t}</small></h5>
           <button class="btn btn-white btn-mini pull-right" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="article.galleryForInner" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
          <div class="related-item ng-cloak" ng-if="article.galleryForInner">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.galleryForInner.content_type_name %] - [% article.galleryForInner.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.galleryForInner')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div>
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for Home{/t} <small>*{t}Only one album{/t}</small></h5>
          <div class="btn btn-white btn-mini pull-right" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="article.params.galleryForHome" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </div>
        </div>
        <div class="related-item ng-cloak" ng-if="article.params.galleryForHome">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% article.params.galleryForHome.content_type_name %] - [% article.params.galleryForHome.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('article.params.galleryForHome')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    {/is_module_activated}
  </div>
</div>
