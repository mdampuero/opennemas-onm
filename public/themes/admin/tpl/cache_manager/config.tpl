{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_tpl_manager_config}" method="POST" ng-controller="CacheConfigCtrl" ng-init='init({json_encode($config)})'>
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
    <div class="alert alert-block alert-error fade in">
      <h4 class="alert-heading"><i class="icon-warning-sign"></i> Dangerous action!</h4>
      <p>Clean internal template files generated for this instance by pushing buttons below. <br>These actions could take some time depending on the number of present cache/compiled files.</p>
      <div class="button-set">
        <a href="{url name=admin_tpl_manager_clearcache}" class="btn btn-white btn-cons">
          <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">{t}Remove cache{/t}</span>
        </a>
        <a href="{url name=admin_tpl_manager_clearcompiled}" class="btn btn-white btn-cons">
          <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">{t}Remove compiled templates{/t}</span>
        </a>
      </div>
    </div>
        <p>Activate/deactivate internal caches by section:</p>
    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="table-wrapper ng-cloak">

          <table class="table table-hover table-condensed">
            <thead>
              <tr>
                <th class="checkbox-cell">
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
            <tr ng-repeat="(group, item) in config">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" name="enabled[[% group %]]" checklist-model="selected.contents" checklist-value="group" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
                <input type="hidden" name="groups[[% group %]]" ng-value="group">
              </td>
              [% selected.groups %]
              <td>
                [% item.name %]
              </td>

              <td class="right">
                <input type="number" min="0" max="99999" size="5" name="lifetime[[% group %]]" ng-model="item.cache_lifetime" ng-value="item.cache_lifetime"/>
              </td>
            </tr>
         </tbody>
       </table>
     </div>
   </div>
 </div>
</div>
</form>
{/block}
