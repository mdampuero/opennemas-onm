 <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10">
    <h2 class="normal">Sign in to Opennemas</h2>
    <!-- <p>Use Facebook, Twitter or your email to sign in.<br></p> -->
</div>
<div class="tiles grey p-t-20 p-b-20 text-black">
    <form id="modal-login-form" class="animated fadeIn">
        <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="alert alert-[% message.type %]" ng-show="message">
                        [% message.text %]
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
            <div class="col-sm-6">
                <div class="form-group">
                    <input name="username" id="username" class="form-control" ng-model="user.username" placeholder="Username" type="text">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <input name="password" id="password" class="form-control" ng-model="user.password" placeholder="Password" type="password">
                </div>
            </div>
        </div>
        <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10" ng-if="attempts > 2">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="control-group clearfix">
                        <div vc-recaptcha theme="clean" lang="en" key="'6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66'"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
            <div class="col-sm-12 text-right">
                <button type="button" class="btn btn-primary btn-cons" id="login_toggle" ng-click="login()" ng-disabled="loading">
                    <i class="fa fa-circle-o-notch" ng-if="loading"></i>
                    {t}Login{/t}
                </button>
            </div>
        </div>
    </form>
</div>
