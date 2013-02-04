<div class="form-horizontal">
    <fieldset>
        <legend>{t}Settings for frontpage{/t}</legend>

        <div class="control-group">
            <label for="params[titleSize]" class="control-label">{t}Text size for title{/t}</label>
            <div class="controls">
                {if isset($article->params['titleSize']) && !empty($article->params['titleSize'])}
                    {assign var=defaultValue value=$article->params['titleSize']}
                {else}
                    {assign var=defaultValue value=26}
                {/if}
                <select name="params[titleSize]">
                    {html_options values=$availableSizes options=$availableSizes selected=$defaultValue}
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="img_home_pos" class="control-label">{t}Image position{/t}</label>
            <div class="controls">
                <select name="params[imagePosition]" id="img_home_pos">
                    <option value="right" {if $article->params['imagePosition'] eq "right" || !$article->params['imagePosition']} selected{/if}>Derecha</option>
                    <option value="left" {if $article->params['imagePosition'] eq "left"} selected{/if}>Izquierda</option>
                    <option value="none" {if $article->params['imagePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>{t}Settings home frontpage{/t} </legend>
        <div class="control-group">
            <label for="titleHome" class="control-label">{t}Title for home frontpage{/t}</label>
            <div class="controls">
                <input  type="text" id="titleHome" name="params[titleHome]"
                    value="{$article->params['titleHome']|clearslash|escape:"html"}" class="input-xxlarge" />
            </div>
        </div>

        <div class="control-group">
            <label for="" class="control-label">{t}Subtitle{/t}</label>
            <div class="controls">
                <input  type="text" id="subtitleHome" name="params[subtitleHome]"
                        value="{$article->params['subtitleHome']|clearslash|escape:"html"}"  class="input-xxlarge"/>
            </div>
        </div>

        <div class="control-group">
            <label for="sumary_home" class="control-label">{t}Summary{/t}</label>
            <div class="controls">
                <textarea name="params[summaryHome]" class="input-xxlarge" id="sumary_home">{$article->params['summaryHome']|clearslash}</textarea>
            </div>
        </div>

        <div class="control-group">
            <label for="homesize" class="control-label">{t}Size{/t}</label>
            <div class="controls">
                {if isset($article->params['titleHomeSize']) && !empty($article->params['titleHomeSize'])}
                    {assign var=defaultValue value=$article->params['titleHomeSize']}
                {else}
                    {assign var=defaultValue value=26}
                {/if}
                <select name="params[titleHomeSize]" id="homesize">
                    {html_options values=$availableSizes options=$availableSizes selected=$defaultValue}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="img_pos" class="control-label">{t}Image position{/t}</label>
            <div class="controls">
                <select name="params[imageHomePosition]" id="img_pos" >
                    <option value="right" {if $article->params['imageHomePosition'] eq "right" || !$article->params['imageHomePosition']} selected{/if}>Derecha</option>
                    <option value="left" {if $article->params['imageHomePosition'] eq "left"} selected{/if}>Izquierda</option>
                    <option value="none" {if $article->params['imageHomePosition'] eq "none"} selected{/if}>Justificada(300px)</option>
                </select>
            </div>
        </div>
    </fieldset>
</div>