{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_tpl_manager_config}" method="POST">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-database"></i>
              {t}Cache Manager{/t}
            </h4>
          </li>
          <li class="quicklins hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklins hidden-xs">
            <h5>{t}Settings{/t}</h5>
          </li>
        </ul>
      </div>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" href="{url name=admin_tpl_manager}" title="{t}Go back{/t}">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i>
              {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
    {render_messages}

    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak">
          <table class="table table-hover table-condensed">
            <thead>
              <tr>
                <th class="center" style="width:10px">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th>{t}Cache group{/t}</th>
                <th class="right" style="width:20%">{t}Expire time{/t}  <small>({t}seconds{/t})</small></th>
              </tr>
            </thead>
            <tbody>
             {foreach from=$config key="k" item="v"}
             <tr>
              <td>
                <div class="checkbox check-default">
                    <input id="caching[{$k|default:""}]" {if $v.caching}checked="checked"{/if} value="1" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                <img src="{$params.IMAGE_DIR}template_manager/elements/{$groupIcon.$k}" title="Caché de opinión interior" />
                {$groupName.$k|default:$k}
                <input type="hidden" name="group[]" value="{$k|default:""}" />
              </td>

              <td class="right">
                <input type="text" size="5" name="cache_lifetime[{$k}]" value="{$v.cache_lifetime|default:300}" style="text-align: right;" />
              </td>
            </tr>
            {foreachelse}
            <tr>
              <td class="empty" colspan=3>
               {t}There is no cache configuration available{/t}
             </td>
           </tr>
           {/foreach}
         </tbody>
       </table>
     </div>
   </div>
 </div>
</div>
</form>
{/block}
