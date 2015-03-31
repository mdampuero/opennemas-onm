{if $has_auth_error}
<div class="output center">
  <h5>{t}Username or password are incorrect{/t}</h5>
  <p>{t}Please, check if the credentials are correct and try again.{/t}</p>
</div>
{elseif !empty($all_categories)}
<div class="output">
  <label for="site_color" class="form-label">{t}Available categories for sync{/t}</label>
  <div class="controls">
    <table class="table table-hover table-condensed">
      <tbody>
        {foreach $all_categories as $category}
        <tr>
          <td>
            <input type="checkbox" name="categories[]" value="{$category->link}"
            {if array_key_exists('categories', $site) && in_array($category->link, $site['categories'])}checked="checked"{/if} />
            {$category->title}
          </td>
        </tr>
        {/foreach}
      </tbody>
    </table>
  </div>
</div>
{else}
<div class="output center">
  <h5>{t}No elements to sync in this server{/t}</h5>
  <p>{t}The given url has no elements to sync or it is not an Opennemas server.{/t}</p>
  <p>{t}Also check if the credentials are correct.{/t}</p>
</div>
{/if}
<div class="spinner-wrapper" id="loading">
  <div class="loading-spinner"></div>
  <div class="spinner-text">{t}Loading{/t}...</div>
</div>