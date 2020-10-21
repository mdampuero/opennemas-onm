<div class="row">
  <div class="col-md-12">
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-magic"></i>
          {t}Customize{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="col-md-4">
          <h5>{t}Settings for frontpage{/t}</h5>
          <div class="form-group">
            <label class="form-label" for="params[titleSize]">
              {t}Text size for title{/t}
            </label>
            <div class="controls">
              {if isset($article->params['titleSize']) && !empty($article->params['titleSize'])}
                {assign var=defaultValue value=$article->params['titleSize']}
              {else}
                {assign var=defaultValue value=26}
              {/if}
              <select name="params[titleSize]" ng-model="article.params.titleSize">
                <option value="">{t}Select a size...{/t}</option>
                <option value="16">16</option>
                <option value="18">18</option>
                <option value="20">20</option>
                <option value="22">24</option>
                <option value="26">26</option>
                <option value="28">28</option>
                <option value="30">30</option>
                <option value="32">32</option>
                <option value="34">34</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="img_home_pos" >
              {t}Image position{/t}
            </label>
              <div class="controls">
                <select name="params[imagePosition]" id="img_home_pos" ng-model="article.params.imagePosition">
                  <option value="">{t}Select a position...{/t}</option>
                  <option value="right" {if $article->params['imagePosition'] eq "right" || !$article->params['imagePosition']} selected{/if}>Derecha</option>
                  <option value="left" {if $article->params['imagePosition'] eq "left"} selected{/if}>Izquierda</option>
                  <option value="none" {if $article->params['imagePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                </select>
              </div>
          </div>
        </div>
        <div class="col-md-8">
          <h5>{t}Settings home frontpage{/t}</h5>
          <div class="form-group">
            <label class="form-label" for="titleHome">
              {t}Title for home frontpage{/t}
            </label>
            <div class="controls">
              <input class="form-control" id="titleHome" name="params[titleHome]" ng-model="article.params.titleHome" type="text" value="{$article->params['titleHome']|clearslash|escape:"html"}"/>
            </div>
          </div>
          <div class="form-group">
            <class="form-label" for="subtitle-home">
            {t}Subtitle{/t}
            </label>
            <div class="controls">
              <input class="form-control" id="subtitle-home" name="params[subtitleHome]" ng-model="article.params.subtitleHome" type="text" value="{$article->params['subtitleHome']|clearslash|escape:"html"}"/>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="sumary_home">
              {t}Summary{/t}
            </label>
            <div class="controls">
              <textarea id="sumary_home" name="params[summaryHome]" ng-model="article.params.summaryHome" class="form-control">{$article->params['summaryHome']|clearslash}</textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="homesize">
              {t}Size{/t}
            </label>
            <div class="controls">
              {if isset($article->params['titleHomeSize']) && !empty($article->params['titleHomeSize'])}
                {assign var=defaultValue value=$article->params['titleHomeSize']}
              {else}
                {assign var=defaultValue value=26}
              {/if}
              <select id="homesize" name="params[titleHomeSize]" ng-model="article.params.titleHomeSize">
                {html_options values=$availableSizes options=$availableSizes selected=$defaultValue}
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="img_pos">
              {t}Image position{/t}
            </label>
            <div class="controls">
              <select id="img_pos" name="params[imageHomePosition]" ng-model="article.params.imageHomePosition">
                <option value="right" {if $article->params['imageHomePosition'] eq "right" || !$article->params['imageHomePosition']} selected{/if}>Derecha</option>
                <option value="left" {if $article->params['imageHomePosition'] eq "left"} selected{/if}>Izquierda</option>
                <option value="none" {if $article->params['imageHomePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
