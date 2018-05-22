<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-eye m-r-5"></i>
      {t}Visualization{/t}
    </h4>
  </div>

  <div class="grid-body">
    <div class ="form-inline">
      <div class="form-group p-l-20">
        <label class="form-label" for="config[number_elements]">
          {t}Number of comments to show{/t}:
        </label>
        <input id="name" class="form-control" name="configs[number_elements]" min=3 max=100 type="number"  value="{$configs['number_elements']|default:10}">
        <div class="help help-block">{t}Number of comments to show by page{/t}</div>
      </div>
    </div>
  </div>
</div>

<div class="grid simple">
  <div class="grid-title">
    <h4>
      <i class="fa fa-black-tie m-r-5"></i>
      {t}Comment moderation{/t}
    </h4>
  </div>

  <div class="grid-body">
      <div class="form-group">
        <div class="checkbox">
          <input id="moderation_manual" name="configs[moderation_manual]" type="checkbox" value="1" ng-model="configs.moderation_manual">
          <label class="form-label" for="moderation_manual">
            <span class="checkbox-title">{t}Manual moderation of comments{/t}</span>
          </label>
          <div class="help p-l-25">
            {t}Comments must be manually approved by an administrator before is publicly available.{/t}
          </div>
        </div>
      </div>

      <div class="row" ng-show="!configs.moderation_manual">
        <div class="col-xs-12">
          <h4><i class="fa fa-commenting m-r-5"></i> {t}Blacklist{/t}</h4>
          <hr />
        </div>
        <div class="col-xs-12 col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <input id="moderation_autoaccept" name="configs[moderation_autoaccept]" type="checkbox" value="1" ng-model="configs.moderation_autoaccept">
              <label class="form-label" for="moderation_autoaccept">
                <span class="checkbox-title">{t escape=off}<strong>Auto-accept comments</strong> if they pass the blacklist rules{/t}</span>
              </label>
              <div class="help p-l-25">
                {t}When a comment is submitted and it passes the blacklist checks, then accept it without intervenction.{/t}
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-6">
          <div class="form-group">
            <div class="checkbox">
              <input id="moderation_autoreject" name="configs[moderation_autoreject]" type="checkbox" value="1" ng-model="configs.moderation_autoreject">
              <label class="form-label" for="moderation_autoreject">
                <span class="checkbox-title">{t escape=off}<strong>Auto-reject comments</strong> if they don't pass the blacklist rules{/t}</span>
              </label>
              <div class="help p-l-25">
                {t}When a comment is submitted and it doesn't pass the blacklist checks, then reject it without intervenction.{/t}
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12">
          <h5><i class="fa fa-fire m-r-5"></i> {t}Words and rules prohibited{/t}</h5>
          <div class="form-group">
            <textarea name="blacklist_comment" id="blacklist_comment" class="form-control" rows=10>{$extra['blacklist_comment']}</textarea>
            <div class="help">{t}List of words or regular expressions that are prohibited on comments, one per line.{/t}</div>
          </div>
        </div>
      </div>
  </div>
</div>
