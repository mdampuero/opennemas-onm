{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Events{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="EventCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-calendar m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_events_list}">
    {t}Events{/t}
  </a>
{/block}

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
        <h5>
          <i class="p-r-15">
            <i class="fa fa-check"></i>
            {t}Draft saved at {/t}[% draftSaved %]
          </i>
        </h5>
      </li>
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Event')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving || isInvalidIframe" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>

{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {include file="ui/component/content-editor/accordion/published.tpl"}
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.organizer = !expanded.organizer">
        <i class="fa fa-user m-r-10"></i>
        {t}Organizer data{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.organizer }"></i>
        <span class="pull-right" ng-if="!expanded.organizer">
          {include file="common/component/icon/status.tpl" iFlag="event_organizer_name" iForm="form.event_organizer_name" iNgModel="item.event_organizer_name" iValidation=true}
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.organizer }">
        {include file="ui/component/input/text.tpl" iField="event_organizer_name" iRequired=true iTitle="{t}Organizer name{/t}" iValidation=true}
        {include file="ui/component/input/text.tpl" iField="event_organizer_url" iTitle="{t}Organizer website{/t}"}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.when = !expanded.when">
        <i class="fa fa-calendar m-r-10"></i>{t}Event date{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.when }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.when }">
        <div class="row">
          <div class="form-group col-sm-6">
            <label class="form-label" for="event_start_date">{t}Start date{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" datetime-picker datetime-picker-format="YYYY-MM-DD" datetime-picker-timezone="{$timezone}" datetime-picker-max="item.event_end_date" datetime-picker-use-current="true" id="event_start_date" name="event_start_date" ng-model="item.event_start_date" type="datetime">
                <span class="input-group-addon add-on">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group col-sm-6">
            <label class="form-label" for="event_start_hour">{t}Start hour{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" datetime-picker datetime-picker-format="HH:mm" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_end_date" datetime-picker-use-current="true" id="event_start_hour" name="event_start_hour" ng-model="item.event_start_hour" type="datetime">
                <span class="input-group-addon add-on">
                  <span class="fa fa-clock-o"></span>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-6">
            <label class="form-label" for="event_end_date">{t}End date{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" datetime-picker datetime-picker-format="YYYY-MM-DD" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_start_date" id="event_end_date" name="event_end_date" ng-model="item.event_end_date" type="datetime">
                <span class="input-group-addon add-on">
                  <span class="fa fa-calendar"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group col-sm-6">
            <label class="form-label" for="event_end_hour">{t}End hour{/t}</label>
            <div class="controls">
              <div class="input-group">
                <input class="form-control" datetime-picker datetime-picker-format="HH:mm" datetime-picker-timezone="{$timezone}" datetime-picker-min="item.event_start_hour" datetime-picker-use-current="true" id="event_end_hour" name="event_end_hour" ng-model="item.event_end_hour" type="datetime">
                <span class="input-group-addon add-on">
                  <span class="fa fa-clock-o"></span>
                </span>
              </div>
            </div>
          </div>
          <span class="help-block">
            {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"} ({$app.locale->getTimeZone()->getName()})
          </span>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.type = !expanded.type">
        <i class="fa fa-info m-r-10"></i>
        {t}Event type{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.type }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.type }">
        <div class="controls">
          <select class="block" id="event_type" name="event_type" ng-model="item.event_type">
            <option value="Música">Música</option>
            <option value="Formativo">Formativo</option>
          </select>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.where = !expanded.where">
        <i class="fa fa-map-marker m-r-10"></i>
        {t}Event location{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.where }"></i>
        <span class="pull-right" ng-if="!expanded.where">
          {include file="common/component/icon/status.tpl" iFlag="event_place" iForm="form.event_place" iNgModel="item.event_place" iValidation=true}
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.where }">
        {include file="ui/component/input/text.tpl" iField="event_place" iRequired=true iTitle="{t}Place name{/t}" iValidation=true}
        {include file="ui/component/input/text.tpl" iField="event_address" iTitle="{t}Place address{/t}" }
        {include file="ui/component/input/text.tpl" iField="event_map_latitude" iTitle="{t}Latitude{/t}" }
        {include file="ui/component/input/text.tpl" iField="event_map_longitude" iTitle="{t}Longitude{/t}" }
        <div class="controls">
          <label class="form-label" for="event_end_hour">{t}Event iframe map{/t}</label>
          <textarea name="event-map-iframe" id="event-map-iframe" ng-model="item.event_map_iframe" class="form-control"
            rows="15">
          </textarea>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.ticket = !expanded.ticket">
        <i class="fa fa-ticket m-r-10"></i>
        {t}Ticket data{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.ticket }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.ticket }">
        {include file="ui/component/input/text.tpl" iField="event_tickets_link" iTitle="{t}Ticket Selling URL{/t}"}
        {include file="ui/component/input/text.tpl" iField="event_tickets_price" iTitle="{t}Ticket Price{/t}"}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.external_website = !expanded.external_website">
        <i class="fa fa-external-link m-r-10"></i>
        {t}External website{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.external_website }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.external_website }">
        <div class="form-group no-padding">
          <label class="form-label" for="event_website">{t}Website URL{/t}</label>
          <div class="controls">
            <input class="form-control" id="event_website" name="event_website" ng-model="item.event_website" type="text">
          </div>
        </div>
      </div>
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredFrontpage" iTitle="{t}Featured in frontpage{/t}" types="photo,video,album"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Featured in inner{/t}" types="photo,video,album"}
      {include file="ui/component/content-editor/accordion/seo-input.tpl"}
    </div>
  </div>
{/block}

{block name="customFields"}
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('when')">
    <input id="checkbox-when" checklist-model="app.fields[contentKey].selected" checklist-value="'when'" type="checkbox">
    <label for="checkbox-when">
      {t}Event date{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('where')">
    <input id="checkbox-where" checklist-model="app.fields[contentKey].selected" checklist-value="'where'" type="checkbox">
    <label for="checkbox-where">
      {t}Event location{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox" ng-if="!isFieldHidden('external_website')">
    <input id="checkbox-external_website" checklist-model="app.fields[contentKey].selected" checklist-value="'external_website'" type="checkbox">
    <label for="checkbox-external_website">
      {t}External website{/t}
    </label>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true contentPicker=true}
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
