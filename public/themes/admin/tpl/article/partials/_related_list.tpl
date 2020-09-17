<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-list"></i>
      {t}Related contents{/t}
    </h4>
  </div>
  <div class="grid-body">
    <div class="row">
      <div class="col-sm-6">
        <h5>
          {t}Related in frontpage{/t}
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.relatedFrontpage">
            <div class="related-item" ng-repeat="r in data.relatedFrontpage" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.relatedFrontpage', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-default" content-picker content-picker-ignore="[% getRelatedIds(data.relatedFrontpage) %]" content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedFrontpage" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
        </div>
      </div>
      <div class="col-sm-6">
        <h5>
          {t}Related in inner{/t}
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.relatedInner">
            <div class="related-item" ng-repeat="r in data.relatedInner" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.relatedInner', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-default" content-picker content-picker-ignore="[% getRelatedIds(data.relatedInner) %]" content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedInner" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
        </div>
      </div>
    </div>
    {is_module_activated name="CRONICAS_MODULES"}
    <div class="row m-t-50">
      <div class="col-sm-6">
        <h5>
          {t}Related in home{/t}
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.relatedHome">
            <div class="related-item" ng-repeat="r in data.relatedHome" ui-tree-node>
              <span ui-tree-handle>
                <span class="angular-ui-tree-icon"></span>
              </span>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.relatedHome', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-default" content-picker content-picker-ignore="[% getRelatedIds(data.relatedHome) %]" content-picker-selection="true" content-picker-max-size="10" content-picker-target="relatedHome" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
        </div>
      </div>
      <div class="col-sm-6">
        <h5>
          {t}Album for frontpage{/t}
          <small>
            ({t}Only one album{/t})
          </small>
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.albumFrontpage">
            <div class="related-item" ng-repeat="r in data.albumFrontpage" ui-tree-node>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.albumFrontpage', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-default" content-picker content-picker-selection="true" content-picker-max-size="1" content-picker-target="albumFrontpage" content-picker-type="album" ng-if="!data.albumFrontpage || data.albumFrontpage.length === 0" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
        </div>
        <h5>
          {t}Album for inner{/t}
          <small>
            ({t}Only one album{/t})
          </small>
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.albumInner">
            <div class="related-item" ng-repeat="r in data.albumInner" ui-tree-node>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.albumInner', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-default" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="albumInner" content-picker-type="album" ng-if="!data.albumInner || data.albumInner.length === 0" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </button>
        </div>
        <h5>
          {t}Gallery for Home{/t}
          <small>
            ({t}Only one album{/t})
          </small>
        </h5>
        <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
          <div class="related" ui-tree-nodes="" ng-model="data.albumHome">
            <div class="related-item" ng-repeat="r in data.albumHome" ui-tree-node>
              <div class="related-item-info">
                <span class="related-item-type">
                  <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-stack-overflow': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
                </span>
                <span class="related-item-title">
                  [% related[r.target_id].title %]
                </span>
                <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
                  ({t}No published{/t})
                </span>
              </div>
              <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.albumHome', $index)">
                <i class="fa fa-trash-o"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <div class="btn btn-default" content-picker content-picker-max-size="1" content-picker-selection="true" content-picker-target="albumHome" content-picker-type="album" ng-if="!data.albumHome || data.albumHome.length === 0" type="button">
            <i class="fa fa-plus m-r-5"></i>
            {t}Add{/t}
          </div>
        </div>
      </div>
    </div>
  </div>
  {/is_module_activated}
</div>
