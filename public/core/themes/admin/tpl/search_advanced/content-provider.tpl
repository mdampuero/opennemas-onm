  <form action="{url name=admin_search_content_provider}" method="get" style="display:inline-block;" id="search-form-content-provider">
    <div class="form-group">
      <div class="controls">
        <div class="input-group">
          <input class="form-control" name="search_string" placeholder="{t}Write here the text for search...{/t}" type="text" value="{$search_string}">
          <span class="input-group-addon button">
            <i class="fa fa-search text-white"></i>
          </span>
        </div>
     </div>
    </div>
    <div class="form-group" id="loading-contents" style="display:none;">
      <span style="margin-left:45%;">{t}Loading{/t}...</spam>
    </div>
  </form>
{if !empty($results)}
  <div id="search_results_available" class="content-provider-block">
    {foreach from=$results item=content name=video_loop}
      {include file=$content->content_partial_path}
    {/foreach}
  </div>
  <div id="pagination-wrapper" class="pagination-wrapper">
    {$pagination}
  </div><!-- / -->
{elseif (!empty($search_string))}
  {t}No results{/t}
{/if}

<script>
  (function($){
    makeContentProviderAndPlaceholdersSortable();
    function getContentProviderItems(url, data, tab) {
      var action = $('#search-form-content-provider').attr('data-action');
      if (action == 'loading') {
        return;
      }

      $('#search-form-content-provider').attr('data-action', 'loading')
      $('#loading-contents').show();
      $('#search_results_available').hide();
      $('#pagination-wrapper').hide();

      $.ajax({
        url: url,
        type: "GET",
        data: data,
        cache: false,
        success : function(data){
            tab.html(data);
            $('#search-form-content-provider').removeAttr('data-action');
            $('#loading-contents').hide();
            $('#search_results_available').show();
            $('#pagination-wrapper').show();
        },
        failure: function(data){
          tab.html('{t}There was an error while performing the search please reload this tab by changing to another one.{/t}');
          $('#search-form-content-provider').removeAttr('data-action');
          $('#loading-contents').hide();
          $('#search_results_available').show();
          $('#pagination-wrapper').show();
        }
      });
    }

    $('span.button').on('click', function(){
      $('#search-form-content-provider').submit();
    });

    $('.pagination a').on('click', function(e){
      e.preventDefault();
      var route = $(this).attr('href');
      getContentProviderItems(route, [], $(this).closest('.ui-tabs-panel'));
    });

    $('#search-form-content-provider').on('submit', function(e, ui) {
      e.preventDefault();
      var form = $(this);
      var formData = form.serialize();
      var url = form.attr('action')
      getContentProviderItems(url, formData, $(this).closest('.ui-tabs-panel'));
    });
  })(jQuery);
</script>
