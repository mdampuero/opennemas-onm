<div class="grid simple">
  <div class="grid-body">
    <h4>
      <i class="fa fa-cog"></i>
      {t}Configuration{/t}
    </h4>
    <div class="form-group m-l-25">
      <label class="form-label" for="facebook_api_key">
        Facebook App Id
      </label>
      <div class="controls">
        <input class="form-control" id="facebook_api_key" name="facebook[api_key]" type="text" value="{$extra['fb_app_id']|default:""}"/>
        <div class="help">
          {t escape=off}To be able to moderate comments of your site in Facebook you must create and set here your <strong>Facebook App Id</strong>.{/t}
          <br>
          {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
        </div>
      </div>
    </div>
  </div>
</div>
