<span style="display:block; width:100%; text-align:center;">
  <form action="{url name=admin_search_content_provider}" method="get" style="display:inline-block;" id="search-form-content-provider">
    <input type="hidden" name="related" value="{$related}">
    <div class="input-group">
      <input class="form-control" name="search_string" placeholder="{t}Write here the text for search...{/t}" type="text" value="{$search_string}">
      <span class="input-group-btn">
        <button type="submit" class="btn" id="search-content-provider-button">
          <i class="fa fa-search fa-lg"></i>
        </button>
      </span>
    </div>
  </form>
</span>

{include file="common/content_provider/_container-content-list.tpl" hidenoavailable=true}
<script>
(function($){
    makeContentProviderAndPlaceholdersSortable();
    $('#search-form-content-provider').on('submit', function(e, ui) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var tab = form.closest('.ui-tabs-panel');
        $.ajax({
            url: form.attr('action'),
            type: "GET",
            data: formData,
            cache: false,
            success : function(data){
                tab.html(data);
            },
            failure: function(data){
                tab.html('{t}There was an error while performing the search please reload this tab by changing to another one.{/t}');
            }
        });
    });
})(jQuery);

</script>
