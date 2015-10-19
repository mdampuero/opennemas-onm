<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Import items{/t}</h4>
</div>
<div class="modal-body">
  <p>{t escape=off}Are you sure you want to import [% template.contents.length %] elements?{/t}</p>
  <ul class="no-style p-l-7">
    <li ng-repeat="content in template.contents">
      <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': content.type === 'text', 'fa-picture-o': content.type === 'photo', 'fa-film': content.type === 'video' }"></i>
      [% content.title %]
    </li>
  </ul>
  <div class="p-t-30" ng-if="!onlyPhotos()">
    <span ng-show="template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text')">
      {t}Import{/t}
    </span>
    <span ng-show="template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text')">
      {t}as{/t}
      <select id="type" name="type" ng-init="type = 'article'" ng-model="type">
        {is_module_activated name="ARTICLE_MANAGER"}
          <option value="article">{t}Article{/t}</option>
        {/is_module_activated}
        {is_module_activated name="OPINION_MANAGER"}
          <option value="opinion">{t}Opinion{/t}</option>
        {/is_module_activated}
      </select>
    </span>
    <span ng-show="type === 'article' && (template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text'))">
      &nbsp;{t}in{/t}&nbsp;
      <select id="category" name="category" ng-model="category">
        <option value="">{t}Choose a category...{/t}</option>
        <option value="[% category.value %]" ng-repeat="category in template.categories">[% category.name %]</option>
      </select>
    </span>
    <span ng-show="type === 'opinion' && (template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text'))">
      &nbsp;{t}by{/t}&nbsp;
      <select id="category" name="author" ng-model="author">
        <option value="">{t}Choose an author...{/t}</option>
        <option value="[% author.value %]" ng-repeat="author in template.authors">[% author.name %]</option>
      </select>
    </span>
  </div>
</div>
<div class="modal-footer">
  <button class="btn btn-link" ng-click="close()" type="button">{t}No{/t}</button>
  <button class="btn btn-white" ng-click="confirm()" type="button">{t}Yes, import and publish{/t}</button>
  <button class="btn btn-success" ng-click="confirm(1)" type="button" ng-if="isEditable()">{t}Yes, import and edit{/t}</button>
</div>
