<div class="modal-body">
  <button aria-hidden="true" class="close" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h4 class="p-r-20 text-bold text-center">
    [% template.item.title %]
  </h4>
  <div class="block form-horizontal m-t-30">
    <div class="form-group no-margin">
      <label class="control-label col-sm-3 text-bold">
        {t}Date{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        [% template.item.created_time | moment : 'YYYY-MM-DD HH:mm:ss' %]
      </div>
    </div>
    <div class="form-group no-margin">
      <label class="control-label col-sm-3 text-bold">
        {t}Priority{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <span class="badge text-bold" ng-class="{ 'badge-danger': template.item.priority == 1, 'badge-warning': template.item.priority == 2, 'badge-info': template.item.priority == 3 }">
          [% template.item.priority %]
        </span>
      </div>
    </div>
    <div class="form-group no-margin">
      <label class="control-label col-sm-3 text-bold">
        {t}Category{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <span class="label label-default text-bold">
          [% template.item.category %]
        </span>
      </div>
    </div>
    <div class="form-group no-margin">
      <label class="control-label col-sm-3 text-bold">
        {t}Author{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        [% template.item.author %]
      </div>
    </div>
    <div class="form-group no-margin" ng-if="template.item.tags && template.item.tags.split(',').length > 0">
      <label class="control-label col-sm-3 text-bold">
        {t}Tags{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <span class="inline label label-info m-r-5 text-bold" ng-if="template.item.tags && template.item.tags.split(',').length > 0" ng-repeat="tag in template.item.tags.split(',')">
          [% tag %]
        </span>
      </div>
    </div>
    <div class="form-group no-margin" ng-if="template.item.related && template.item.related.length > 0">
      <label class="control-label col-sm-3 text-bold">
        {t}Related contents{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <div class="content-related">
          <ul class="content-related-text no-style">
            <li class="nowrap" ng-class="{ 'm-b-15': $index === template.item.related.length - 1 }" ng-if="template.related[id] && template.related[id].type === 'text'" ng-repeat="id in template.item.related">
              <small>
                <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': template.related[id].type === 'text', 'fa-picture-o': template.related[id].type === 'photo', 'fa-film': template.related[id].type === 'video' }"></i>
                [% template.related[id].title %]
              </small>
            </li>
          </ul>
          <div class="content-related-photo">
            <img class="img-thumbnail" ng-repeat="id in template.item.related track by $index" ng-if="template.related[id].type === 'photo'" ng-src="[% routing.generate(template.routes.getContent, { id: id }) %]" />
          </div>
        </div>
      </div>
    </div>
    <div class="form-group no-margin" ng-if="template.item.type === 'text'">
      <label class="control-label col-sm-3 text-bold">
        {t}Summary{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <p ng-bind-html="template.item.summary"></p>
      </div>
    </div>
    <div class="form-group no-margin" ng-if="template.item.type === 'text'">
      <label class="control-label col-sm-3 text-bold">
        {t}Body{/t}
      </label>
      <div class="col-sm-9 col-sm-offset-0 col-xs-offset-1 form-control-static">
        <div ng-bind-html="template.item.body"></div>
      </div>
    </div>
    <div class="form-group no-margin" ng-if="template.item.type === 'photo'">
      <div class="col-sm-9 col-sm-offset-3 col-xs-offset-1 form-control-static">
        <img ng-src="[% routing.generate(template.routes.getContent, { id: template.item.id }) %]" />
      </div>
    </div>
  </div>
</div>
