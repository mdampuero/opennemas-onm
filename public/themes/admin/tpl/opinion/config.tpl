{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_opinions_config}" method="POST" name="formulario" id="formulario">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-quote-right"></i>
              {t}Opinions{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Settings{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_opinions}" title="{t}Go back to list{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}...">
                <span class="fa fa-save"></span>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label" for="opinion_settings[total_director]">
                {t}Director opinions in Opinion frontpage{/t}
              </label>
              <span class="help">
                {t}How many director opinions will be shown in the opinion frontpage.{/t}
              </span>
              <div class="controls">
                <input id="opinion_settings[total_director]" name="opinion_settings[total_director]" required type="number" value="{$configs['opinion_settings']['total_director']|default:"1"}" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="opinion_settings[total_editorial]">
                {t}Editorial opinions in Opinion frontpage{/t}
              </label>
              <span class="help">
                {t}How many editorial opinions will be shown in the opinion frontpage.{/t}
              </span>
              <div class="controls">
                <input id="opinion_settings[total_editorial]" name="opinion_settings[total_editorial]" required type="number" value="{$configs['opinion_settings']['total_editorial']|default:"2"}" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="opinion_settings[total_opinions]">
                {t}Opinions in Opinion frontpage{/t}
              </label>
              <span class="help">
                {t}How many opinions opinions will be shown in the opinion frontpage.{/t}
              </span>
              <div class="controls">
                <input id="opinion_settings[total_opinions]" name="opinion_settings[total_opinions]" required type="number"  value="{$configs['opinion_settings']['total_opinions']|default:"16"}" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="opinion_settings[total_opinion_authors]">
                {t}Author opinions in frontpage opinion widget{/t}
              </label>
              <span class="help">
                {t}How many author opinions will be shown in the widget.{/t}
              </span>
              <div class="controls">
                <input id="opinion_settings[total_opinion_authors]" name="opinion_settings[total_opinion_authors]" required type="number" value="{$configs['opinion_settings']['total_opinion_authors']|default:"6"}" />
              </div>
            </div>
            {is_module_activated name="BLOG_MANAGER"}
              <div class="form-group">
                <label class="form-label" for="blog_orderFrontpage">
                  {t}Order blog's frontpage by{/t}
                </label>
                <span class="help">
                  {t}Select if order blogs's frontpages by created date or bloggers name.{/t}
                </span>
                <div class="controls">
                  <select id="blog_orderFrontpage" name="opinion_settings[blog_orderFrontpage]">
                    <option value="created" {if !isset($configs['opinion_settings']['blog_orderFrontpage']) || $configs['opinion_settings']['blog_orderFrontpage'] eq "created"} selected {/if}>{t}Created Date{/t}</option>
                    <option value="blogger" {if $configs['opinion_settings']['blog_orderFrontpage'] eq "blogger"} selected {/if}>{t}Blogger{/t}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="blog_itemsFrontpage]">
                  {t}Items per blog page{/t}
                </label>
                <div class="controls">
                  <input id="blog_itemsFrontpage" name="opinion_settings[blog_itemsFrontpage]" type="number" value="{$configs['opinion_settings']['blog_itemsFrontpage']|default:12}">
                </div>
              </div>
            {/is_module_activated}
          </div>
          {acl isAllowed="MASTER"}
            <div class="col-md-6" ng-init="extraFields = {json_encode($extra_fields)|escape:"html"}">
              <autoform-editor ng-model="extraFields"/>
            </div>
            <input id="extra-fields" name="extra-fields" type="hidden" value="[% extraFields %]">
          {/acl}
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
