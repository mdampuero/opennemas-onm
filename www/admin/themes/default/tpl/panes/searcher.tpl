<div id="searcher">    
    <div id="searcher-controls">
        <input type="text" name="q[text]" id="q-text" value="{$smarty.request.q.text}" />        
        
        <select name="q[category]" id="q-category">
            <option value="">«{t}Any category{/t}»</option>
            {category_select selected=$smarty.request.q.category}
        </select>
        
        <select name="q[content_type]" id="q-content_type">
            <option value="">«{t}Any type{/t}»</option>
            {content_type_select selected=$smarty.request.q.content_type}
        </select>
        
        <select name="q[status]" id="q-status">
            <option value="">«{t}Any status{/t}»</option>
            <option value="AVAILABLE">{t}AVAILABLE{/t}</option>
            <option value="PENDING">{t}PENDING{/t}</option>
            <option value="REMOVED">{t}REMOVED{/t}</option>
        </select>
        
        <button type="button" id="searcher-submit">{t}Search{/t}</button>
        <button type="button" id="searcher-reset">{t}Reset{/t}</button>
    </div>
    
    <div id="searcher-results"></div>
</div>


<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    // Attach events
    $('div#searcher button#searcher-submit').click(function(event) {
        var data = {};
        $('div#searcher input, div#searcher select').each(function(i, v) {
            data[ $(v).attr('id') ] = $(v).val();
            $.cookie($(v).attr('id'), $(v).val());
        });
        
        console.log(data);
        
        $.ajax({
            'url': '{baseurl}/{url route="content-search"}',
            'data': data,
            'type': 'post',
            'success': function(responseText, textStatus, xhr) {
                console.log(responseText);
                $('#searcher-results').html(responseText);
            }
        });
    });
    
    $('div#searcher button#searcher-reset').click(function(event) {
        $.cookie('q-text', null);
        $.cookie('q-content_type', null);
        $.cookie('q-category', null);
        $.cookie('q-status', null);
    });
    
    
    // Initialize form
    if($.cookie('q-text') != null) {
        $('div#searcher input#q-text').val($.cookie('q-text'));
    }
    
    if($.cookie('q-content_type') != null) {
        $('div#searcher select#q-content_type').val($.cookie('q-content_type'));
    }
    
    if($.cookie('q-category') != null) {
        $('div#searcher select#q-category').val($.cookie('q-category'));
    }    

    if($.cookie('q-status') != null) {
        $('div#searcher select#q-status').val($.cookie('q-status'));
    }  
});
/* ]]> */
</script>
