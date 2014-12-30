<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
    <h4 class="modal-title">
        {t}Select modules{/t}
    </h4>
</div>
<div class="modal-body column-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="checkbox check-default">
                <input id="checkbox-modules" ng-model="selected.all" ng-change="selectAll()" ng-checked="allSelected()" type="checkbox">
                <label for="checkbox-modules">
                    {t}Select all{/t}
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4" ng-repeat="column in modules">
            <div class="checkbox check-default" ng-repeat="(key, value) in column">
                <input id="checkbox-[% key %]" checklist-model="selected.modules" checklist-value="key" type="checkbox">
                <label for="checkbox-[% key %]">
                    [% value %]
                </label>
            </div>
        </div>
    </div>


</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="close();" ng-disabled="deleting">
        {t}Cancel{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="accept();" ng-disabled="deleting">
        {t}Accept{/t}
    </button>
</div>
