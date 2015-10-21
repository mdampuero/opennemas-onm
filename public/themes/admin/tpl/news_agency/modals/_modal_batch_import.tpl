<div class="modal-header">
  <button type="button" class="close" ng-click="close()" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Import items{/t}</h4>
</div>
<div class="modal-body" ng-if="!loading && !imported">
  <p>{t escape=off}Are you sure you want to import [% template.contents.length %] elements?{/t}</p>
  <ul class="no-style p-l-7">
    <li ng-repeat="content in template.contents">
      <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': content.type === 'text', 'fa-picture-o': content.type === 'photo', 'fa-film': content.type === 'video' }"></i>
      [% content.title %]
    </li>
  </ul>
  <div class="p-t-30">
    <span ng-show="template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text')">
      {t}Import{/t}
    </span>
    <span ng-show="template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text')">
      {t}as{/t}
      <select id="type" name="type" ng-init="template.type = 'article'" ng-model="template.type">
        {is_module_activated name="ARTICLE_MANAGER"}
          <option value="article">{t}Article{/t}</option>
        {/is_module_activated}
        {is_module_activated name="OPINION_MANAGER"}
          <option value="opinion">{t}Opinion{/t}</option>
        {/is_module_activated}
      </select>
    </span>
    <span ng-show="template.type === 'article' && (template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text'))">
      &nbsp;{t}in{/t}&nbsp;
      <select id="category" name="category" ng-model="template.category">
        <option value="">{t}Choose a category...{/t}</option>
        <option value="[% category.value %]" ng-repeat="category in template.categories">[% category.name %]</option>
      </select>
    </span>
    <span ng-show="template.type === 'opinion' && (template.contents.length > 1 || (template.contents.length == 1 && template.contents[0].type == 'text'))">
      &nbsp;{t}by{/t}&nbsp;
      <select id="category" name="author" ng-model="template.author">
        <option value="">{t}Choose an author...{/t}</option>
        <option value="[% author.value %]" ng-repeat="author in template.authors">[% author.name %]</option>
      </select>
    </span>
  </div>
</div>
<div class="modal-body" ng-if="loading">
  <div class="spinner-wrapper">
    <div class="loading-spinner"></div>
    <div class="spinner-text">{t}Loading{/t}...</div>
  </div>
</div>
<!-- FTW: font size -->
<div class="modal-body" ng-if="imported" style="font-size:14px">
  <div ng-repeat="message in template.messages">
    <p class="text-[% message.type %]" ng-bind-html="message.message"></p>
  </div>
  <span ng-show="template.type == 'article'">
    {t escape=off}Your articles have been published, check them in the <a href="{url name=admin_articles}">article list</a> or you can add them to one of your <a href="{url name=admin_frontpage_list}">frontpages</a>{/t}
  </span>
  <span ng-show="template.type == 'opinion'">
    {t escape=off}Your opinions have been published, check them in the <a href="{url name=admin_opinions}">opinions list</a>or you can add them to one of your <a href="{url name=admin_frontpage_list}">frontpages</a>{/t}
  </span>
  <span ng-show="!template.type">
    {t escape=off}Your photos have been published, check them in the <a href="{url name=admin_photos}">photo list</a>{/t}
  </span>
</div>
<div class="modal-footer">
  <button class="btn btn-link" ng-click="close(0)" ng-if="!imported" type="button">{t}No{/t}</button>
  <button class="btn btn-link" ng-click="close(1)" ng-if="imported" type="button">{t}Close{/t}</button>
  <button class="btn btn-white" ng-click="confirm()" ng-if="!imported" type="button">{t}Yes, import and publish{/t}</button>
  <button class="btn btn-success" ng-click="confirm(1)" ng-if="!imported" type="button" ng-if="template.contents.length === 1">{t}Yes, import and edit{/t}</button>
</div>
