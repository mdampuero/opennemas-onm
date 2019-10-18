<div class="modal-header">
  <button aria-hidden="true" class="close" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h4 class="modal-title">
    {t}Preview{/t}
  </h4>
</div>
<div class="modal-body">
  <div class="content-body" ng-class="{ 'content-body-related': template.related.length > 0 }">
    <h4 class="text-bold">
      [% template.item.title %]
    </h4>
    <div class="row m-t-20">
      <div class="col-sm-6">
        <p>
          <strong>{t}Date{/t}:</strong>
          [% template.item.created_time | moment : 'YYYY-MM-DD HH:mm:ss' %]
        </p>
        <p>
          <strong>{t}Category{/t}:</strong>
          <span class="label label-default text-bold">
            [% template.item.category %]
          </span>
        </p>
      </div>
      <div class="col-sm-6">
        <strong>{t}Priority{/t}:</strong>
        <span class="badge text-bold" ng-class="{ 'badge-danger': template.item.priority == 1, 'badge-warning': template.item.prority == 2, 'badge-info': template.item.priority == 3 }">
          [% template.item.priority %]
        </span>
      </div>
    </div>
    <p ng-bind-html="template.item.summary"></p>
    <hr>
    <div ng-bind-html="template.item.body" ng-if="template.item.type === 'text'"></div>
    <img ng-src="[% template.getImage(template.item.id) %]" ng-if="template.item.type === 'photo'"/>
  </div>
  <div class="content-related-wrapper" ng-if="template.item.related && template.item.related.length > 0">
    <div class="content-related">
      <img class="img-thumbnail" ng-repeat="id in template.item.related" ng-src="[% routing.generate(template.routes.getContent, { id: id }) %]" />
    </div>
  </div>
</div>
