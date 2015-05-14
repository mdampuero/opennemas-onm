  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">[% template.item.name %]</h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-sm-4">
        <img class="img-responsive" src="http://placehold.it/300x300" alt="">
      </div>
      <div class="col-sm-8">
        [% template.item.description %]
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">
      {t}Close{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="confirm()" ng-if="!template.inCart">
      <i class="fa fa-plus"></i>
      {t}Add to cart{/t}
    </button>
  </div>
