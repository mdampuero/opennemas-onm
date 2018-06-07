<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-cog"></i>
      {t}Configuration{/t}
    </h4>
  </div>
  <div class="grid-body">
    <div class="alert alert-danger m-l-20" role="alert" ng-show="!extra.shortname || !extra.secretKey"> {t}Please enter your Disqus settings{/t} </div>
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <div class="form-group m-l-20" ng-class="{ 'has-error' : !extra.shortname }">
          <label class="form-label" for="shortname">
            Disqus Id (shortname)
          </label>
          <div class="controls">
            <input class="form-control" id="shortname" name="shortname" required type="text" value="{$extra['shortname']|default:""}" ng-model="extra.shortname"/>
            <div class="help">
              {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="form-group m-l-20" ng-class="{ 'has-error' : !extra.secretKey }">
          <label class="form-label" for="secret_key">
            Disqus API Secret Key
          </label>
          <div class="controls">
            <input class="form-control" id="secret_key" name="secret_key" required type="text" value="{$extra['secretKey']|default:""}"  ng-model="extra.secretKey"/>
            <div class="help">
              {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
