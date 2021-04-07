<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <div ng-hide="template.selectedTranslator" class="text-center m-t-50 p-t-30">
    <i class="fa fa-4x fa-warning text-warning"></i>
    <h4>{t escape=off 1="[% template.config.locale.available[template.selectedTranslator.from] %]" 2="[% template.config.locale.available[template.selectedTranslator.to] %]"}No available selectedTranslators for "%1&rarr;%2"{/t}</h4>
    <p class="m-b-50">{t escape=off}Please go to the <a href="{url name=admin_system_settings}">Settings page</a> and configure your <br> selectedTranslators for all the languages.{/t}</p>
  </div>
  <div ng-hide="!template.confirm">
    <div class="text-center m-t-50 p-t-30">
      <i class="fa fa-4x fa-globe"></i>
      <h4>{t 1="[% template.config.locale.available[template.selectedTranslator.to] %]"}Do you want to translate to "%1"?{/t}</h4>
      <button class="btn btn-success btn-block m-t-50" data-dismiss="modal" aria-hidden="true" ng-click="confirm();" type="button">
        <h4 class="text-uppercase text-white">
          <i class="fa fa-check"></i>
          <strong>{t}Yes{/t}</strong>
        </h4>
      </button>
      <button class="btn btn-danger btn-block m-t-50" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
        <h4 class="text-uppercase text-white">
          <i class="fa fa-times"></i>
          <strong>{t}No{/t}</strong>
        </h4>
      </button>
    </div>
  </div>
  <div ng-show="template.translating">
    <div class="spinner-wrapper" class="text-center m-t-50 p-t-30">
      <div class="loading-spinner"></div>
      <h4 class="text-center">{t 1="[% template.config.locale.available[template.selectedTranslator.to] %]"}Translating content into "%1"{/t}</h4>
    </div>
  </div>
  <div ng-show="template.translation_done">
      <div class="text-center m-t-50 p-t-30">
        <i class="fa fa-4x fa-globe"></i>
        <h4>{t 1="[% template.config.locale.available[template.selectedTranslator.to] %]"}Content translated properly into "%1".{/t}</h4>
      </div>
      <button class="btn btn-success btn-block m-t-50" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
        <h4 class="text-uppercase text-white">
          <i class="fa fa-check"></i>
          <strong>{t}Ok{/t}</strong>
        </h4>
      </button>
  </div>
</div>
