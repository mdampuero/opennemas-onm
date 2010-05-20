{* panes/seo.tpl *}

<fieldset id="pane-seo">
    
    {if isset($legend)}<legend>{$legend}</legend>{/if}
    
    <dl>
        <dt>
            <label for="slug">{t}Slug{/t}</label>
        </dt>
        <dd>
            <input type="text" name="slug" id="slug" value="{$content->slug}" title="{t}Slug{/t}" size="50"/>
        </dd>
        
        <dt>
            <label for="keywords">{t}Keywords{/t}</label>
        </dt>
        <dd>
            <input type="text" name="keywords" id="keywords" value="{$content->keywords}" size="50"/>
        </dd>
        
        <dt>
            <label for="description">{t}Description{/t}</label>
        </dt>
        <dd>
            <textarea name="description" id="description" cols="50" rows="6">{$content->description}</textarea>
        </dd>
        
    </dl>
</fieldset>

{literal}
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    $('#slug').change(function(event) {
        $.ajax({
            type: 'POST',
            url: '{/literal}{baseurl}/{url route="content-slugit"}{literal}',            
            data: 'title=' + $('#slug').val(),
            success: function(data) {
                $('#slug').val(data);
            }
        });
    });
    
    
});
/* ]]> */
</script>
{/literal}