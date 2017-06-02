<div class="grid simple">
  <div class="grid-body">
    <div class="form-group">
      <label class="form-label" for="with_comments">
        {t}Allow comments in contents by default{/t}
      </label>
      <div class="checkbox">
        <input id="with_comments" name="configs[with_comments]" type="checkbox" value="1" {if !isset($configs['with_comments']) || $configs['with_comments'] == true}checked="checked"{/if} >
        <label class="form-label" for="with_comments">
          {t}Whether to allow users to comment in comments by default for all contents (you can change this setting for specific contents){/t}
        </label>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="disable_comments">
        {t}Disable all comments on site{/t}
      </label>
      <div class="checkbox">
        <input id="disable_comments" name="configs[disable_comments]" type="checkbox" value="1" {if $configs['disable_comments'] == true}checked="checked"{/if} >
        <label class="form-label" for="disable_comments">
          {t}If set, users will not be able to comment on the site and comments already approved will not be displayed{/t}
        </label>
      </div>
    </div>
  </div>
</div>
