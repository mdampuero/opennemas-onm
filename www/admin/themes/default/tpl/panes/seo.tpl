{* *****************************************************************************
 * {pane_seo value=$content}
 * 
 * {include file="panes/seo.tpl" value=$value legend="SEO"}
 *
 * Pane name:
 *    panes/seo.tpl
 *    
 * Params:
 *    $value
 *    $route_slugit     Route to get a valid slug
 *    $route_keywords   Route to get a list of keywords to autocomplete widget
 ***************************************************************************** *}

<fieldset id="pane-seo" class="{$className}">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt>
            {$slugItRoute}
            <label for="slug">{t}Slug{/t}</label>
        </dt>
        <dd>
            <input type="text" name="slug" id="slug" value="{$value->slug}" title="{t}Slug{/t}" size="50"/>
        </dd>
        
        <dt>
            <label for="keywords">{t}Keywords{/t}</label>
        </dt>
        <dd>
            <input type="text" name="keywords" id="keywords" value="{$value->keywords}" size="50"/>
        </dd>
        
        <dt>
            <label for="description">{t}Description{/t}</label>
        </dt>
        <dd>
            <textarea name="description" id="description" cols="50" rows="6">{$value->description}</textarea>
        </dd>
        
    </dl>
</fieldset>

{if isset($value)}
    {assign var="filter" value=$value->getFilterStr()}
{/if}    

{literal}
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    $('#slug').change(function(event) {
        $.ajax({ {/literal}
            type: 'POST',
            url: '{baseurl}/{url route=$route_slugit}',            
            data: 'title=' + $('#slug').val() + '&{$filter}', {literal}            
            success: function(data) {
                $('#slug').val(data);
            }
        });
    });
    
    $("input#keywords").autocomplete({
        minLength: 3,
        
        source: function(request, response) {
            $.getJSON('{/literal}{baseurl}/{url route=$route_keywords}{literal}', {
                term: request.term.split(/,\s*/).pop()
            }, response);
        },
        
        search: function() {
            var term = this.value.split(/,\s*/).pop();
            if (term.length < 2) {
                return false;
            }
        },

        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        
        select: function(event, ui) {
            var terms = this.value.split(/,\s*/);
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push( ui.item.value );
            // add placeholder to get the comma-and-space at the end
            terms.push("");
            this.value = terms.join(", ");
            return false;
        }
    });

});
/* ]]> */
</script>
{/literal}
