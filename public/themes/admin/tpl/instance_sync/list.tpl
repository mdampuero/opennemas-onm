{extends file="base/admin.tpl"}
{block name="header-css" append}
<style type="text/css">
  .colorpicker_viewer {
    width: 40px;
    height: 40px;
  }
</style>
{/block}

{block name="content"}
<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-exchange"></i>
            <span class="hidden-xs">{t}Instance Synchronization{/t}</span>
            <span class="visible-xs-inline-block">{t}Ins. Sync.{/t}</span>
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
            <a href="{url name=admin_instance_sync_create}" class="btn btn-primary" title="{t}Add site to sync{/t}" id="add_button">
              <i class="fa fa-plus"></i>
              {t}Add site{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="grid simple">
    <div class="grid-body {if count($elements) >0}no-padding{/if}">
      {if count($elements) >0}
      <table class="table table-hover table-condensed">
        <thead>
          <tr>
            <th>{t}Site Url{/t}</th>
            <th style='width:45% !important;' class="hidden-xs">{t}Categories to Sync{/t}</th>
            <th style="width:10% !important;" class="hidden-xs">{t}Color{/t}</th>
          </tr>
        </thead>
        <tbody>
          {foreach $elements as $siteUrl => $config}
          <tr>
            <td>
              <strong>{$siteUrl}</strong>
              <div class="visible-xs">{t}Categories to sync{/t}: {if !empty($config['categories'])}{$config['categories']|implode:", "}{/if}</div>
              <div class="visible-xs">
                <div class="colorpicker_viewer" style="background-color:#{$config['site_color']};"></div>
              </div>
              <div class="listing-inline-actions">
                <a class="link" href="{url name=admin_instance_sync_show site_url=$siteUrl}" title="{t}Edit{/t}" class="btn">
                  <i class="fa fa-pencil"></i> {t}Edit{/t}
                </a>
                <a class="link link-danger" href="{url name=admin_instance_sync_delete site_url=$siteUrl}" title="{t}Delete{/t}" class="btn btn-danger">
                  <i class="fa fa-trash-o"></i> {t}Remove{/t}
                </a>
              </div>
            </td>
            <td class="hidden-xs">
              {if !empty($config['categories'])}{$config['categories']|implode:", "}{/if}
            </td>
            <td class="hidden-xs">
              <div class="colorpicker_viewer" style="background-color:{$config['site_color']};">&nbsp;&nbsp;&nbsp;&nbsp;</div>
            </td>
          </tr>
          {/foreach}
        </tbody>
      </table>
      {else}
      <div class="center">
        <h4>{t}There are no synchronize settings available{/t}</h4>
        <p>{t}Try adding one site to synchronize on the config button above.{/t}</p>
      </div>
      {/if}
    </div>
  </div>
</div>
{/block}
