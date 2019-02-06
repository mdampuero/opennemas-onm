<div class="form-group">
  <label for="title" class="form-label">{t}Title{/t}</label>
  <div class="controls">
    <input  type="text" id="title" ng-blur="generate()" ng-model="title" ng-init="title = '{$video->title|clearslash|escape:"html"|default:$information['title']}'" name="title" required class="form-control"/>
  </div>
</div>
<div class="form-group">
  <label for="description" class="form-label">{t}Description{/t}</label>
  <div class="controls">
    <textarea onm-editor onm-editor-preset="simple" ng-model="description" name="description" id="description" required rows="6" class="form-control">{$video->description|clearslash|default:''}</textarea>
  </div>
</div>
{if (!empty($video->uri))}
  <div class="form-group">
    <label for="link" class="form-label">{t}Link{/t}</label>
    <div class="controls">
      <a href="{$smarty.const.INSTANCE_MAIN_DOMAIN}/{$video->uri}" target="_blank">{$smarty.const.INSTANCE_MAIN_DOMAIN}/{$video->uri}</a>
    </div>
  </div>
{/if}
<div class="form-group">
  <label for="preview" class="form-label">{t}Preview{/t}</label>
  <div class="controls">
    <div class="thumbnail center">
      <div ng-bind-html="information.embedHTML" style="max-width:600px; overflow:hidden; margin:0 auto"></div>
    </div>
    <input type="hidden" value="[% informationJson %]" name="information" />
  </div>
</div>
<div class="form-group">
  <label for="other_info" class="form-label">{t}Other information{/t}</label>
  <div class="controls">
    <table style="width:80%; margin:20xp;">
      <tr>
        <td width="200"><strong>{t}Original Title{/t}</strong></td>
        <td>[% information.title %]</td>
      </tr>
      <tr>
        <td><strong>{t}Service{/t}</strong></td>
        <td>[% information.service %]</td>
      </tr>
      <tr>
        <td><strong>{t}Duration{/t}</strong></td>
        <td>[% information.duration %]</td>
      </tr>
      <tr>
        <td><strong>{t}Embed Url{/t}</strong></td>
        <td><a href="[% information.embedUrl %]">[% information.embedUrl %]</a></td>
      </tr>
      <tr>
        <td><strong>{t}Thumbnail URL{/t}</strong></td>
        <td><a href="information.thumbnail" ng-if="information.thumbnail">[% information.thumbnail %]</a></td>
      </tr>
      <tr>
        <td><strong>{t}Thumbnail image{/t}</strong></td>
        <td><img ng-src="[% information.thumbnail %]" ng-if="information.thumbnail" width="100"></td>
      </tr>
    </table>
  </div>
</div>
<input type="hidden" id="author_name" name="author_name" title="author_name" required {if (!empty($video->author_name))} value="{$video->author_name|clearslash|escape:"html"|default:""}" {else} value="{$information['service']|clearslash|escape:"html"|default:""}" {/if} />
