{include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
{include file="ui/component/content-editor/textarea.tpl" title="{t}description{/t}" field="description" rows=5 imagepicker=true}

<div class="form-group">
  <label for="preview" class="form-label">{t}Preview{/t}</label>
  <div class="controls">
    <div class="thumbnail center">
      <div ng-bind-html="item.information.embedHTML" style="max-width:600px; overflow:hidden; margin:0 auto"></div>
    </div>
  </div>
</div>
<div class="form-group">
  <label for="other_info" class="form-label">{t}Other information{/t}</label>
  <div class="controls">
    <table style="width:80%; margin:20xp;">
      <tr>
        <td width="200"><strong>{t}Original Title{/t}</strong></td>
        <td>[% item.information.title %]</td>
      </tr>
      <tr>
        <td><strong>{t}Service{/t}</strong></td>
        <td>[% item.information.service %]</td>
      </tr>
      <tr>
        <td><strong>{t}Duration{/t}</strong></td>
        <td>[% item.information.duration %]</td>
      </tr>
      <tr>
        <td><strong>{t}Embed Url{/t}</strong></td>
        <td><a href="[% item.information.embedUrl %]">[% item.information.embedUrl %]</a></td>
      </tr>
      <tr>
        <td><strong>{t}Thumbnail URL{/t}</strong></td>
        <td><a href="item.information.thumbnail" ng-if="item.information.thumbnail">[% item.information.thumbnail %]</a></td>
      </tr>
      <tr>
        <td><strong>{t}Thumbnail image{/t}</strong></td>
        <td><img ng-src="[% item.information.thumbnail %]" ng-if="item.information.thumbnail" width="100"></td>
      </tr>
    </table>
  </div>
</div>
<input type="hidden" id="author_name" name="author_name" title="author_name" required {if (!empty($video->author_name))} value="{$video->author_name|clearslash|escape:"html"|default:""}" {else} value="{$information['service']|clearslash|escape:"html"|default:""}" {/if} />
