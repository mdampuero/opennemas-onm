<div class="modal-body">
  <button aria-hidden="true" class="close" ng-click="close()" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h4 class="p-r-20 text-bold text-center">
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
  <img ng-if="template.item.type === 'photo'" ng-src="[% routing.generate(template.routes.getContent, { id: template.item.id }) %]" />
  <div ng-if="template.item.related && template.item.related.length > 0">
    <hr>
    <div class="content-related">
      <ul class="content-related-text no-style">
        <li class="nowrap" ng-class="{ 'm-b-15': $index === template.item.related.length - 1 }" ng-if="template.related[id] && template.related[id].type === 'text'" ng-repeat="id in template.item.related">
          <small>
            &angrt;
            <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': template.related[id].type === 'text', 'fa-picture-o': template.related[id].type === 'photo', 'fa-film': template.related[id].type === 'video' }"></i>
            [% template.related[id].title %]
          </small>
        </li>
      </ul>
      <div class="content-related-photo">
        <img class="img-thumbnail" ng-repeat="id in template.item.related" ng-if="template.related[id].type === 'photo'" ng-src="[% routing.generate(template.routes.getContent, { id: id }) %]" />
      </div>
    </div>
  </div>
</div>
