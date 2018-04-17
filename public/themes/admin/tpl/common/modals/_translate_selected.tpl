<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <div ng-hide="template.config.translators.length > 0">
    <p class="m-t-10 text-center">{t escape=off 1="[% template.config.locales[template.config.translateFrom] %]" 2="[% template.config.locales[template.config.translateTo] %]"}No available translators for "%1&rarr;%2"{/t}</p>
    <p class="text-center">{t escape=off}Please go to the <a href="{url name=admin_system_settings}">Settings page</a> and configure your translators for all the languages.{/t}</p>
  </div>
  <div ng-show="template.translating">
    <div class="spinner-wrapper">
      <div class="loading-spinner"></div>
      <div class="spinner-text">{t 1="[% template.config.locales[template.config.translateTo] %]"}Translating selected contents into "%1"{/t}</div>
    </div>
  </div>
</div>
