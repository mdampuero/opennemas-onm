{if $categories}
  <div class="form-group">
    <label for="site_color" class="form-label">{t}Available categories for sync{/t}</label>
    <div class="controls">
      {foreach name=d from=$categories item=category}
      <div class="col-sm-6">
        <div class="checkbox">
          <input id="categories_{$category->link}" name="categories[]" {if in_array($category->link, $categories_checked)}checked="checked"{/if} value="{$category->link}" type="checkbox"/>
          <label for="categories_{$category->link}">
            {$category->title|ucfirst}
          </label>
        </div>
      </div>
      {if !empty($category->submenu)}
      {foreach name=d from=$category->submenu item=subcategory}
      <div class="col-sm-6">
        <div class="checkbox" style="margin-left:10px">
          <input id="categories_{$category->link}" name="categories[]" {if in_array($subcategory->link, $categories_checked)}checked="checked"{/if} value="{$subcategory->link}" type="checkbox"/>
          <label for="categories_{$category->link}">
            {$subcategory->title|ucfirst}
          </label>
        </div>
      </div>
      {/foreach}
      {/if}
      {/foreach}
    </div>
{elseif $loading}
  <div class="center">
    <h5>{t}No elements to sync in this server{/t}</h5>
    <p>{t}The given url has no elements to sync or it is not an Opennemas server.{/t}</p>
  </div>
{else}

<div>
  <label>{t}Categories{/t}</label>
  <h5>{t}Click "Connect" to fetch the external site categories to sync.{/t}</h5>
</div>
{/if}
</div>
