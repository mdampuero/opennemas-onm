<div class="grid simple">
  <div class="grid-title">
    <h4>{t}Related contents{/t}</h4>
  </div>
  <div class="grid-body">
    <div class="m-b-40" {if isset($orderFront)}ng-init="relatedInFrontpage = {json_encode($orderFront)|clear_json}"{/if}>
      <div class="clearfix">
        <h5 class="pull-left">{t}Related in frontpage{/t}</h5>
        <div class="btn btn-white btn-mini pull-right m-t-5" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedInFrontpage">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </div>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="relatedInFrontpage">
        <div class="related-item" ng-repeat="content in relatedInFrontpage">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('relatedInFrontpage', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="m-b-40" {if isset($orderInner)}ng-init="relatedInInner = {json_encode($orderInner)|clear_json}"{/if}>
      <div class="clearfix">
        <h5 class="pull-left">{t}Related in inner{/t}</h5>
        <div class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedInInner">
          <i class="fa fa-plus"></i>
          {t}Add contents{/t}
        </div>
      </div>
      <div ui-sortable class="ng-cloak" ng-model="relatedInInner">
        <div class="related-item" ng-repeat="content in relatedInInner">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
          </div>
          <button class="btn btn-white" ng-click="removeItem('relatedInInner', $index)">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    </div>
    {is_module_activated name="CRONICAS_MODULES"}
      <div class="m-b-40" {if isset($orderHome)}ng-init="relatedInHome = {json_encode($orderHome)|clear_json}"{/if}>
        <div class="clearfix">
          <h5 class="pull-left">{t}Related in home{/t}</h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedInHome" type="button">
            <i class="fa fa-plus"></i>
            {t}Add contents{/t}
          </button>
        </div>
        <div ui-sortable class="ng-cloak" ng-model="relatedInHome">
          <div class="related-item" ng-repeat="content in relatedInHome">
            <div class="related-item-info">
              <span class="sort-icon"></span>
              [% content.content_type_l10n_name %] - [% content.title %] <span class="status" ng-if="content.content_status == 0">({t}No published{/t})</span>
            </div>
            <button class="btn btn-white" ng-click="removeItem('relatedInHome', $index)">
              <i class="fa fa-trash-o text-danger"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="m-b-40" {if isset($galleries['front']) && $galleries['front']->title}ng-init="galleryForFrontpage = { id: '{$galleries['front']->id}', content_type_name: '{$galleries['front']->content_type_name}', title: '{$galleries['front']->title}' }"{/if}>
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for frontpage{/t} <small>*{t}Only one album{/t}</small></h5>
          <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="galleryForFrontpage" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
        <div class="related-item ng-cloak" ng-if="galleryForFrontpage">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% galleryForFrontpage.content_type_name %] - [% galleryForFrontpage.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('galleryForFrontpage')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div class="m-b-40" {if isset($galleries['inner']) && $galleries['inner']->title}ng-init="galleryForInner = { id: '{$galleries['inner']->id}', content_type_name: '{$galleries['inner']->content_type_name}', title: '{$galleries['inner']->title}' }"{/if}>
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for inner{/t} <small>*{t}Only one album{/t}</small></h5>
           <button class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="galleryForInner" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </button>
        </div>
          <div class="related-item ng-cloak" ng-if="galleryForInner">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% galleryForInner.content_type_name %] - [% galleryForInner.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('galleryForInner')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
      <div {if isset($galleries['home']) && $galleries['home']->title}ng-init="galleryForHome = { id: '{$galleries['home']->id}', content_type_name: '{$galleries['home']->content_type_name}', title: '{$galleries['home']->title}' }"{/if}>
        <div class="clearfix">
          <h5 class="pull-left">{t}Gallery for Home{/t} <small>*{t}Only one album{/t}</small></h5>
          <div class="btn btn-white btn-mini pull-right" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="galleryForHome" content-picker-type="album" type="button">
            <i class="fa fa-plus"></i>
            {t}Add gallery{/t}
          </div>
        </div>
        <div class="related-item ng-cloak" ng-if="galleryForHome">
          <div class="related-item-info">
            <span class="sort-icon"></span>
            [% galleryForHome.content_type_name %] - [% galleryForHome.title %]
          </div>
          <button class="btn btn-white" ng-click="removeItem('galleryForHome')" type="album">
            <i class="fa fa-trash-o text-danger"></i>
          </button>
        </div>
      </div>
    {/is_module_activated}
  </div>
</div>
