<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-code"></i>
            {t}Commands{/t}
          </h4>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple">
    <div class="grid-body no-padding">
      <table class="table no-margin no-padding">
        <thead>
          <tr>
            <th>{t}Command name{/t}</th>
            <th>{t}Description{/t}</th>
            <th>{t}Params{/t}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <form action="{url name=manager_framework_command_execute}" method="GET">
            <tr>
              <td>
                clean:smarty-cache
              </td>
              <td>
                {t}Cleans the cache for an specific instance{/t}

                <div class="help-block">
                  {t}Select the desired instance where clean cache and compile files in.
                  <br>Select 'All' for cleaning all the cache/compile files.{/t}
                </div>
              </td>
              <td>
                <div class="control-group">
                  <div class="control">
                    <select name="params[instance]" ng-model="theme">
                      <option value="">{t}All{/t}</option>
                      <option value="{$instance}" ng-repeat="instance in template.instances">[% instance %]</option>
                    </select>
                  </div>
                </div>
                <input type="hidden" name="command" value="clean:smarty-cache">
              </td>
              <td class="right">
                <a ng-href="[% routing.ngGenerate('manager_command_output', { command: 'clean:smarty-cache', data: [theme] }) %]" type="submit" class="btn btn-primary"><i class="fa fa-cog" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Execute{/t}</a>
              </td>
            </tr>
          </form>
          <tr ng-repeat="command in commands">
            <td>[% command.name %]</td>
            <td>[% command.description %]</td>
            <td></td>
            <td class="right">
              <div class="btn-group">
                <a class="btn btn-primary" ng-href="[% routing.ngGenerate('manager_command_output', { command: command.name, data: [] }) %]">
                  <i class="fa fa-cog" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
                  {t}Execute{/t}
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="wrapper-table-block">
  </div><!-- .wrapper-table-block -->
</div>
