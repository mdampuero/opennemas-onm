<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">Modal title</h4>
</div>
<div class="modal-body">
<form action="" method="POST" role="form">
    <div class="form-group">
        <label for="username">Username</label>
        <input class="form-control" id="usename" ng-model="username" placeholder="Input field" type="text">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input class="form-control" id="password" ng-model="password" placeholder="Input field" type="password">
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" ng-click="login();">Sign in</button>
</div>
