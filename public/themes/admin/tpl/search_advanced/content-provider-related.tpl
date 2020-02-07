<form action="{url name=admin_search_content_provider}" method="get" id="search-form-content-provider">
  <div class="form-group">
    <div class="controls">
      <div class="input-group">
        <input type="text" name="search_string" class="form-control" placeholder="{t}Write here the text for search...{/t}" value="{$search_string}">
        <span class="input-group-addon button">
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>
  </div>
</form>
{if !empty($results)}
  <div id="search_results_available" class="content-provider-block">
    {foreach from=$results item=content name=video_loop}
      {include file=$content->content_partial_path}
    {/foreach}
  </div>
  <div class="pagination-wrapper">
    {$pagination}
  </div>
{elseif (!empty($search_string))}
  {t}No results{/t}
{/if}
<script>
(function($){
    makeContentProviderAndPlaceholdersSortable();
    $('span.button').on('click', function(){
      $('#search-form-content-provider').submit();
    });
    $('#search-form-content-provider').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var tab = form.closest('.ui-tabs-panel');
        $.ajax({
            url: form.attr('action'),
            type: 'GET',
            data: formData,
            cache: false,
            success : function(data){
                tab.html(data);
            },
            failure: function(){
                tab.html('{t}There was an error while performing the search please reload this tab by changing to another one.{/t}');
            }
        });
    });
})(jQuery);
</script>
