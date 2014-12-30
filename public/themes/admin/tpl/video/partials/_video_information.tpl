
<div class="control-group">
    <label for="title" class="control-label">{t}Title{/t}</label>
    <div class="controls">
        <input  type="text" id="title" name="title"
            {if (!empty($video->title))}
                value="{$video->title}"
            {else}
                value="{$information['title']}"
            {/if}
             required="required" class="input-xlarge"/>
    </div>
</div>

<div class="control-group">
    <label for="metadata" class="control-label">{t}Keywords{/t}</label>
    <div class="controls">
        <input type="text" id="metadata" name="metadata" value="{$video->metadata|default:""}" required="required" class="input-xlarge"/>
        <div class="help-block">{t}List of words separated by commas.{/t}</div>
    </div>
</div>
<div class="control-group">
    <label for="description" class="control-label">{t}Description{/t}</label>
    <div class="controls">
        <textarea name="description" id="description" required="required" rows="6" class="input-xxlarge">{$video->description|clearslash|default:""}</textarea>
    </div>
</div>
<div class="control-group">
    <label for="author_name" class="control-label">{t}Service:{/t}</label>
    <div class="controls">
        <input type="text" id="author_name" name="author_name" title="author_name" required="required"
                {if (!empty($video->author_name))} value="{$video->author_name|clearslash|escape:"html"|default:""}"
                {else} value="{$information['service']|clearslash|escape:"html"|default:""}" {/if} />
    </div>
</div>

{if (!empty($video->uri))}
<div class="control-group">
    <label for="link" class="control-label">{t}Link{/t}</label>
    <div class="controls">
        <a href="{$smarty.const.SITE_URL}{$video->uri}" target="_blank">{$smarty.const.SITE_URL}{$video->uri}</a>
    </div>
</div>
{/if}

<div class="control-group">
    <label for="preview" class="control-label">{t}Preview{/t}</label>
    <div class="controls">
        <div class="video_player" style="max-width:500px; overflow:hidden;">{$information['embedHTML']}</div>

        <input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
    </div>
</div>
<div class="control-group">
    <label for="other_info" class="control-label">{t}Other information{/t}</label>
    <div class="controls">
        <table style="width:80%; margin:20xp;">
            <tr>
                <td width="100px"><strong>{t}Original Title{/t}</strong></td>
                <td>{$information['title']}</td>
            </tr>
            <tr>
                <td><strong>{t}FLV{/t}</strong></td>
                <td><a href="{$information['FLV']}">{$information['FLV']}</a></td>
            </tr>
            <tr>
                <td><strong>{t}Download Url{/t}</strong></td>
                <td><a href="{$information['downloadUrl']}">{$information['downloadUrl']}</a></td>
            </tr>
            <tr>
                <td><strong>{t}Service{/t}</strong></td>
                <td>{$information['service']}</td>
            </tr>
            <tr>
                <td><strong>{t}Duration{/t}</strong></td>
                <td>{$information['duration']}</td>
            </tr>
            <tr>
                <td><strong>{t}Thumbnail URL{/t}</strong></td>
                <td><img src="{$information['thumbnail']}" alt="" width="100"> {$information['thumbnail']}</td>
            </tr>
            <tr>
                <td><strong>{t}Embed Url{/t}</strong></td>
                <td><a href="{$information['embedUrl']}">{$information['embedUrl']}</a></td>
            </tr>
        </table>
    </div>
</div>
