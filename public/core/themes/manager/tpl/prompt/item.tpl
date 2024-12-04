<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_prompt_list') %]">
              <i class="fa fa-file-o"></i>
              {t}Prompts{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!item.id">{t}New prompt{/t}</span>
            <span ng-if="item.id">{t}Edit prompt{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_prompt_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="!item.id ? save() : update()" ng-disabled="adstxtForm.$invalid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="extra">
  <form name="adstxtForm" novalidate>
    <div class="grid simple">
      <div class="grid-body adstxt-form">
        <div class="row">
          <div class="col-sm-6 form-group">
            <label class="form-label">{t}Mode{/t}</label>
            <div class="controls">
              <select name="mode" id="mode" class="form-control" ng-model="item.mode">
                <option value='New'>{t}Create{/t}</option>
                <option value='Edit'>{t}Edit{/t}</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6 form-group" >
            <label class="form-label">{t}Field{/t}</label>
            <div class="controls">
              <select name="field" id="field" class="form-control" ng-model="item.field">
                <option value='titles'>{t}Titles{/t}</option>
                <option value='introductions'>{t}Introductions{/t}</option>
                <option value='bodies'>{t}Bodies{/t}</option>
              </select>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label class="form-label" for="name">{t}Name{/t}</label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="name" name="name" ng-model="item.name" ng-maxlength="50" required type="text"/>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label for="template" class="form-label">{t}Target{/t}</label>
            <div class="controls">
              <tags-input add-from-autocomplete-only="true" ng-model="item.instances" display-property="name">
                <auto-complete debounce-delay="500" source="autocomplete($query)" min-length="0" load-on-focus="true" load-on-empty="true" template="instance"></auto-complete>
              </tags-input>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script type="text/ng-template" id="instance">
  <span ng-bind-html="$highlight($getDisplayText())"></span>
  </div>
</script>
