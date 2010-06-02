{toolbar_button toolbar="toolbar-top"
    icon="save" type="submit" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="page-index" text="Cancel"}    
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Page Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<hr class="space" />

<div id="tabular">
    <ul>
		<li><a href="#page-form">{t}Page{/t}</a></li>
		<li><a href="#page-options">{t}Options{/t}</a></li>
	</ul>

    <div id="page-form">
        <div id="page-common">    
            <fieldset>
                <legend>Page</legend>
                
                <dl>
                    <dt>
                        <label for="title">{t}Title{/t}</label>
                    </dt>
                    <dd>
                        <input type="text" name="title" id="title" value="{$page->title}" />
                    </dd>
                </dl>
                
                <dl>
                    <dt>
                        <label for="type">{t}Type{/t}</label>
                    </dt>
                    <dd>
                        {* FIXME: create class to manage types of page *}
                        <select name="type" id="type">
                            {html_options options=$statuses selected=$page->type}                            
                        </select> 
                    </dd>
                </dl>    
                
                <dl>
                    <dt>
                        <label for="menu_title">{t}Title on menu{/t}</label>
                    </dt>
                    <dd>
                        <input type="text" name="menu_title" id="menu_title" value="{$page->menu_title}" />
                    </dd>
                </dl>
                
                <dl>
                    <dt>
                        <label for="fk_page">{t}Parent page{/t}</label>
                    </dt>
                    <dd>
                        <select name="fk_page" id="fk_page">
                            {if $page->fk_page == 0}
                            <option value="0">{t}ROOT{/t}</option>
                            {/if}
                            {page_select selected=$page->fk_page disableRecursive=$page->pk_page}
                        </select>
                        
                        {* FIXME: calculate weight *}
                        <input type="hidden" name="weight" value="0" />
                    </dd>
                </dl>        
            </fieldset>        
        </div>
        
        <div id="page-standard">
            {pane_themes value=$page}
            
            <fieldset>
                <legend>Standard</legend>
                
                <dl>
                    <dt>
                        <label for="inline_styles">{t}Style editor{/t}</label>
                    </dt>
                    <dd>
                        <textarea name="inline_styles" id="inline_styles" cols="30" rows="10">{$page->inline_styles}</textarea>
                    </dd>
                </dl>
            </fieldset>
        </div>
        
        <div id="page-shortcut">
            <fieldset>
                <legend>{t}Shortcut{/t}</legend>
                
                <dl>
                    <dt>
                        <label for="params-pk_page">{t}Shortcut{/t}</label>
                    </dt>
                    <dd>
                        {if isset($page)}
                            {assign var="shortcut" value=$page->getParam('pk_page')}
                        {/if}
                        <select name="params[pk_page]" id="params-pk_page">
                            {page_select selected=$shortcut disabled=$page->pk_page}
                        </select>                        
                    </dd>
                </dl>
            </fieldset>
        </div>
        
        <div id="page-external">
            <fieldset id="page-external">
                <legend>{t}External{/t}</legend>
                
                <dl>
                    <dt>
                        <label for="params-url">{t}URL{/t}</label>
                    </dt>
                    <dd>
                        {if isset($page)}
                            {assign var="url" value=$page->getParam('url')}
                        {/if}
                        <input type="text" name="params[url]" id="params-url" value="{$url}" />
                    </dd>
                </dl>
            </fieldset>
        </div>        
    </div><!-- #page-form -->

    <div id="page-options">
        {pane_publishing value=$page}
        
        {pane_seo value=$page}
    </div>  
</div><!-- #tabular -->


{if ($request->getActionName() eq "update")}
{assign var="filter" value=$page->getFilterStr()}
<input type="hidden" name="pk_page" value="{$page->pk_page}" />
<input type="hidden" name="version" value="{$page->version}" />
{/if}


{literal}
<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function() {
    var containers = ['page-standard', 'page-shortcut', 'page-external'];
    var showContainer = function(contName) {        
        $.each(containers, function() {
            $('#' + this).hide();
        });
        
        switch(contName) {
            case 'NOT_IN_MENU':
            case 'STANDARD':
                $('#page-standard').show();
            break;
            
            case 'EXTERNAL':
                $('#page-external').show();
            break;
            
            case 'SHORTCUT':
                $('#page-shortcut').show();
            break;
        }
    };
    
    $('select#type').change(function(event) {
        showContainer( $("select#type option:selected").val() );
    });
    
    showContainer( $("select#type option:selected").val() );
    
    // Slug & menu_title control changes
    $('input#title').change(function(event) {
        var title = $('input#title').val();
        var slug = $('input#slug').val();
        if(slug.length <= 0) {
            $.ajax({ {/literal}
                type: 'POST',
                url: '{baseurl}/page/slugit/',            
                data: 'title=' + title + '&{$filter}', {literal}            
                success: function(data) {
                    $('#slug').val(data);
                }
            });
        }
        
        var menu_title = $('input#menu_title').val();
        if(menu_title.length <= 0) {
            $('input#menu_title').val( title );
        }
    });
    
    $('#tabular').tabs();
});
/* ]]> */
</script>
{/literal}

