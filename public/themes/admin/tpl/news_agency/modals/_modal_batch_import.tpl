<div class="modal-header">
  <button type="button" class="close" ng-click="close({ success: imported })" aria-hidden="true">×</button>
  <h4 class="modal-title">{t}Import items{/t}</h4>
</div>
<div class="modal-body" ng-if="!loading && !imported" ng-init="init()">
  <p>{t escape=off 1="[% template.contents.length %]"}Are you sure you want to import %1 elements?{/t}</p>
  <ul class="no-style p-l-7">
    <li ng-repeat="content in template.contents track by $index">
      <i class="fa m-r-5" ng-class="{ 'fa-file-text-o': content.type === 'text', 'fa-picture-o': content.type === 'photo', 'fa-film': content.type === 'video' }"></i>
      [% content.title %]
    </li>
  </ul>
  <div class="p-t-30">
    <span ng-show="texts > 0">
      {t}Import{/t}
    </span>
    <span ng-show="texts > 0">
      {t}as{/t}
      <select id="type" name="type" ng-model="template.type">
        {is_module_activated name="ARTICLE_MANAGER"}
          <option value="Article">{t}Article{/t}</option>
        {/is_module_activated}
        {is_module_activated name="OPINION_MANAGER"}
          <option value="Opinion">{t}Opinion{/t}</option>
        {/is_module_activated}
      </select>
    </span>
    <span ng-show="template.type === 'Article' && texts > 0">
      &nbsp;{t}in{/t}&nbsp;
      <select id="category" name="category" ng-model="template.category">
        <option value="">{t}Choose a category...{/t}</option>
        <option value="[% category.value %]" ng-repeat="category in template.categories">[% category.name %]</option>
      </select>
    </span>
    <span ng-show="template.type === 'Opinion' && texts > 0">
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
  <span ng-show="template.type == 'Article'">
    {capture name=article_list_url}{url name=admin_articles}{/capture}
    {capture name=frontpage_list_url}{url name=admin_frontpage_list}{/capture}
    {t 1=$smarty.capture.article_list_url 2=$smarty.capture.frontpage_list_url escape=off}Your articles have been published, check them in the <a href="%1">article list</a> or you can add them to one of your <a href="%2">frontpages</a>{/t}
  </span>
  <span ng-show="template.type == 'Opinion'">
    {capture name=opinion_list_url}{url name=admin_opinions}{/capture}
    {t 1=$smarty.capture.opinion_list_url 2=$smarty.capture.frontpage_list_url escape=off}Your opinions have been published, check them in the <a href="%1">opinions list</a>or you can add them to one of your <a href="%2">frontpages</a>{/t}
  </span>
  <span ng-show="!template.type || template.type == 'photo'">
    {capture name=images_list_url}{url name=admin_images}{/capture}
    {t escape=off 1=$smarty.capture.images_list_url}Your photos have been published, check them in the <a href="%1">photo list</a>{/t}
  </span>
</div>
<div class="modal-footer">
  <span ng-if="saving && !imported" class="btn btn-white">
    <span class="fa fa-circle-o-notch fa-spin"></span> {t}Saving...{/t}
  </span>
  <button class="btn btn-link" ng-click="close(1)" ng-if="!saving && imported" type="button">{t}Close{/t}</button>
  <span ng-if="!saving">
    <button class="btn btn-link" ng-click="close(0)" ng-if="!imported" type="button">{t}No{/t}</button>
    <button class="btn" ng-class="{ 'btn-success': photos === template.contents.length || texts > 1, 'btn-white': photos !== template.contents.length && texts <= 1 }" ng-click="confirm()" ng-disabled="(template.type === 'Article' && !template.category) || (template.type === 'Opinion' && !template.author) || saving" ng-if="!imported" type="button">
        <span ng-if="photos === template.contents.length">{t}Yes, import them{/t}</span>
        <span ng-if="photos !== template.contents.length">{t}Yes, import and publish{/t}</span>
    </button>
    <button class="btn btn-success" ng-click="confirm(1)" ng-disabled="(template.type === 'Article' && !template.category) || (template.type === 'Opinion' && !template.author) || saving" ng-if="texts === 1 && !imported" type="button" ng-if="template.contents.length === 1">
    {t}Yes, import and edit{/t}
    </button>
  </span>
</div>
