{if $has_auth_error}
<div class="output center">
  <h5>{t}Username or password are incorrect{/t}</h5>
  <p>{t}Please, check if the credentials are correct and try again.{/t}</p>
</div>
{elseif !empty($all_categories)}
<div class="output form-group">
  <label for="site_color" class="form-label">{t}Available categories for sync{/t}</label>
  <div class="controls">
    {foreach $all_categories as $category}
      <div class="col-sm-4">
        <div class="checkbox check-default">
          <input id="checkbox_{$category@index}" type="checkbox" name="categories[]" value="{$category->link}" {if array_key_exists('categories', $site) && is_array($site['categories']) && in_array($category->link, $site['categories'])}checked="checked"{/if} />
          <label for="checkbox_{$category@index}">
            {$category->title|ucfirst}
          </label>
        </div>
      </div>
    {/foreach}
  </div>
</div>
{else}
<div class="output center">
  <h5>{t}No elements to sync in this server{/t}</h5>
  <p>{t}The given url has no elements to sync or it is not an Opennemas server.{/t}</p>
  <p>{t}Also check if the credentials are correct.{/t}</p>
</div>
{/if}
