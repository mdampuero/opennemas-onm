<div class="modal-body">
  <button class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();" type="button">
    <i class="fa fa-times"></i>
  </button>
  <h3 class="text-center">{t}Translate content{/t}</h3>
  <h4 class="p-b-30 p-t-30 text-center">{t}Do you want to use an automatic translator?{/t}</h4>
  <div class="p-b-30 text-center">
    <select ng-model="translator" ng-change="template.translator = template.config.translators[translator]">
      <option value="">{t}Select a translator...{/t}</option>
      <option value="[% $index %]" ng-repeat="translator in template.config.translators">
      [% translator.translator %] ([% template.config.locales[translator.from] %] - [% template.config.locales[translator.to] %])
      </option>
    </select>
  </div>
</div>
<div class="modal-footer row">
  <div class="col-xs-6">
    <button class="btn btn-block btn-danger text-uppercase" data-dismiss="modal" ng-click="dismiss()" ng-disabled="loading" type="button">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-times m-r-5"></i>
        {t}No{/t}
      </h4>
    </button>
  </div>
  <div class="col-xs-6">
    <button type="button" class="btn btn-block btn-success btn-loading" ng-click="confirm()" ng-disabled="!template.translator || loading">
      <h4 class="bold text-uppercase text-white">
        <i class="fa fa-check m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t}
      </h4>
    </button>
  </div>
</div>
