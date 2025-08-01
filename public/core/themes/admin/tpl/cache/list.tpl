{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Cache{/t}
{/block}

{block name="ngInit"}
  ng-controller="CacheListCtrl"
{/block}

{block name="icon"}
  <i class="fa fa-database m-r-10"></i>
{/block}

{block name="title"}
  {t}Cache{/t}
{/block}

{block name="filters"}{/block}

{block name="list"}
  <div class="ng-cloak row" ng-if="!http.flags.loading">
    <div class="col-lg-4">
      <div class="grid simple">
        <div class="grid-title">
          <h4>
            <img class="m-r-5" src="/core/themes/admin/images/redis.png" width="20">
            Redis
          </h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="redisKey">
              {t}Key or pattern{/t}
            </label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" id="redisKey" name="redisKey" ng-model="redisKey" type="text">
                <div class="input-group-btn">
                  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" ng-disabled="!redisKey" type="button">
                    <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right no-padding">
                    <li>
                      <a class="text-info" href="#" ng-click="getItem('redis', redisKey)">
                        <i class="fa fa-eye"></i>
                        {t}Preview{/t}
                      </a>
                    </li>
                    <li>
                      <a class="text-error" href="#" ng-click="deleteItem('redis', redisKey)">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="help">
                <i class="fa fa-info-circle m-l-3 m-r-5 m-t-10 text-info"></i>
                {t}Key or pattern will be automatically namespaced{/t}
              </div>
            </div>
          </div>
          <hr>
          <div class="form-group no-margin">
            <div class="controls">
              <button class="btn btn-block btn-loading btn-danger" ng-click="deleteList('redis')" type="button">
                <i class="fa fa-trash-o m-r-5"></i>
                {t}Delete{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="grid simple">
        <a class="btn btn-link m-r-10 m-t-10 pull-right" href="{url name="backend_cache_config" service="smarty"}">
          <i class="fa fa-cog fa-lg"></i>
        </a>
        <div class="grid-title">
          <h4>
            <img class="m-r-5" src="/core/themes/admin/images/smarty.png" width="20">
            Smarty
          </h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="smartyKey">
              {t}Key{/t}
            </label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" id="smartyKey" name="smartyKey" ng-model="smartyKey" type="text">
                <div class="input-group-btn">
                  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" ng-disabled="!smartyKey" type="button">
                    <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right no-padding">
                    <li>
                      <a class="text-error" href="#" ng-click="deleteItem('smarty', smartyKey)">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="help">
                <i class="fa fa-info-circle m-l-3 m-r-5 m-t-10 text-info"></i>
                {t}Key will be automatically namespaced{/t}
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-sm-6">
              <button class="btn btn-block btn-loading btn-danger" ng-click="deleteList('smarty')" type="button">
                <i class="fa fa-trash-o m-r-5"></i>
                {t}Delete{/t}
              </button>
            </div>
            <div class="col-sm-6">
              <button class="btn btn-block btn-loading" ng-click="deleteItem('smarty', 'compile')" type="button">
                <i class="fa fa-trash-o m-r-5"></i>
                {t}Delete compiles{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="grid simple">
        <div class="grid-title">
          <h4>
            <img class="m-r-5" src="/core/themes/admin/images/varnish.png" width="20">
            Varnish
          </h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="varnishKey">
              {t}Key{/t}
            </label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" id="varnishKey" name="varnishKey" ng-model="varnishKey" type="text">
                <div class="input-group-btn">
                  <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" ng-disabled="!varnishKey" type="button">
                    <i class="fa fa-caret-down"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right no-padding">
                    <li>
                      <a class="text-error" href="#" ng-click="deleteItem('varnish', varnishKey)">
                        <i class="fa fa-trash-o"></i>
                        {t}Delete{/t}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="help">
                <i class="fa fa-info-circle m-l-3 m-r-5 m-t-10 text-info"></i>
                {t}Key will be automatically namespaced{/t}
              </div>
            </div>
          </div>
          <hr>
          <div class="form-group no-margin">
            <div class="controls">
              <button class="btn btn-block btn-loading btn-danger" ng-click="deleteList('varnish')" type="button">
                <i class="fa fa-trash-o m-r-5"></i>
                {t}Delete{/t}
              </button>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-preview">
    {include file="cache/modal.preview.tpl"}
  </script>
{/block}
