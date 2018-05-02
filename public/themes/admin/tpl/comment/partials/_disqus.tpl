<div class="grid simple">
  <div class="grid-body">
    <h4>
      <i class="fa fa-cog"></i>
      {t}Configuration{/t}
    </h4>
    <div class="form-group m-l-20">
      <label class="form-label" for="shortname">
        Disqus Id (shortname)
      </label>
      <div class="controls">
        <input class="form-control" id="shortname" name="shortname" required type="text" value="{$extra['shortname']|default:""}"/>
        <div class="help">
          {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
        </div>
      </div>
    </div>
    <div class="form-group m-l-20">
      <label class="form-label" for="secret_key">
        Disqus API Secret Key
      </label>
      <div class="controls">
        <input class="form-control" id="secret_key" name="secret_key"required type="text" value="{$extra['secretKey']|default:""}"/>
        <div class="help">
          {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
        </div>
      </div>
    </div>
  </div>
</div>
