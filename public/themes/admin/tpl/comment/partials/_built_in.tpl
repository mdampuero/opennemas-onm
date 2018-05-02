<div class="grid simple">
  <div class="grid-body">
    <h4>
      <i class="fa fa-eye m-r-5"></i>
      {t}Visualization{/t}
    </h4>
    <div class="form-group p-l-20">
      <label class="form-label" for="config[number_elements]">
        {t}Number of comments to show{/t}
      </label>
      <div class="controls">
        <input id="name" name="configs[number_elements]" min=3 max=100 type="number"  value="{$configs['number_elements']|default:10}">
        <div class="help">{t}Number of comments to show by page{/t}</div>
      </div>
    </div>
  </div>
</div>

<div class="grid simple">
  <div class="grid-body">
    <h4>{t}Comment moderation{/t}</h4>

      <div class="form-group">
        <div class="checkbox">
          <input id="moderation_manual" name="configs[moderation_manual]" type="checkbox" value="1" ng-model="configs.moderation_manual">
          <label class="form-label" for="moderation_manual">
            {t}Manually moderation of comments{/t}
          </label>
          <div class="help p-l-25">
            {t}An administrator must always approve a comment in order to make it publicly available.{/t}
          </div>
        </div>
      </div>

      <div class="row" ng-show="!configs.moderation_manual">
        <div class="col-sm-6">
          <h5>{t}Blacklist{/t}</h5>

          <div class="form-group">
            <div class="checkbox">
              <input id="moderation_autoaccept" name="configs[moderation_autoaccept]" type="checkbox" value="1" ng-model="configs.moderation_autoaccept">
              <label class="form-label" for="moderation_autoaccept">
                {t escape=off}<strong>Auto-accept comments</strong> if they pass the blacklist rules{/t}
              </label>
              <div class="help p-l-25">
                {t}When a comment is submitted and it passes the blacklist checks, then accept it without intervenction.{/t}
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="checkbox">
              <input id="moderation_autoreject" name="configs[moderation_autoreject]" type="checkbox" value="1" ng-model="configs.moderation_autoreject">
              <label class="form-label" for="moderation_autoreject">
                {t escape=off}<strong>Auto-reject comments</strong> if they don't pass the blacklist rules{/t}
              </label>
              <div class="help p-l-25">
                {t}When a comment is submitted and it doesn't pass the blacklist checks, then reject it without intervenction.{/t}
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <h5>{t}Words and rules prohibited{/t}</h5>
          <div class="form-group">
            <textarea name="configs[moderation_blacklist]" id="blacklist_rules" class="form-control" rows=10>{$configs['moderation_blacklist']}</textarea>
            <div class="help">{t}List of words or regular expressions that are prohibited on comments, one per line.{/t}</div>
          </div>
        </div>
      </div>
  </div>
</div>
