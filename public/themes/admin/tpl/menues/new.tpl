{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery.nestedSortable.js"}
    {script_tag src="/onm/menues.js"}
    <script type="text/javascript">
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
    $('[rel=tooltip]').tooltip();
    var menu_messages = {
        remember_save: "{t}Please, remember save changes after finish.{/t}"
    }
    </script>
{/block}

{block name="header-css" append}
    {css_tag href="/managerMenu.css" media="screen,projection"}
{/block}

{block name="content"}
<form action="{if isset($menu->pk_menu)}{url name=admin_menu_update id=$menu->pk_menu}{else}{url name=admin_menu_create}{/if}" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if isset($menu->name)}{t}Editing menu{/t}{else}{t}Creating menu{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a  href="{url name=admin_menus}"  title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>

    </div>
    <div class="wrapper-content">

        <div id="warnings-validation"></div><!-- /warnings-validation -->
        {render_messages}

        <div class="form-vertical panel">
            <div class="form-inline-block">
                <div class="control-group">
                    <label for="name" class="control-label">{t}Name{/t}</label>
                    <div class="controls">
                        <input type="text" id="name" name="name" value="{$menu->name|default:""}"
                               maxlength="120" tabindex="1" required="required" class="input-xxlarge"
                               {if (!empty($menu) && $menu->type neq 'user')} readonly="readonly" {/if} />
                    </div>
                </div>

                {if count($menu_positions) > 1}
                <div class="control-group">
                    <label for="name" class="control-label">{t}Position{/t}</label>
                    <div class="controls">
                        {html_options options=$menu_positions selected=$menu->position name=position}
                    </div>
                </div>
                {/if}

            </div>

            <div class="control-group clearfix">

                <label>{t}Menu components{/t}</label>
                <p>{t}Pick elements from the right column and drag them to the left column to include them as elements of this menu.{/t}</p>

                <div class="wrapper-menu-items pull-left" style="width:57%" >
                    <ol id="menuelements" class="nested-sortable">
                    {if isset($menu) && is_array($menu->items) && count($menu->items) > 0}
                        {foreach from=$menu->items item=menuItem}
                            {include file="menues/partials/_menu_item.tpl" menuItem=$menuItem}
                        {/foreach}
                    {/if}
                    </ol>
                </div>


                <div id="elements-provider"  style="width:39%" class="pull-right">

                    <h3 href="#external-link">{t}External link{/t}</h3>
                    <div id="external-link" style="border:1px solid #CCCCCC;padding: 14px;">
                        <form action="#" name="external-link">
                            <p>{t}Fill the below form with the title and the external URL you want to add to the menu.{/t}</p>
                            <p>
                                <label>{t}Title:{/t}</label>
                                <input type="text" name="external-link-title" value="" id="external-link-title" size="60">
                            </p>
                            <p>
                                <label>{t}URL:{/t}</label>
                                <input type="text" name="external-link-link" value="" id="external-link-link" size="60"> <br>
                            </p>
                            <a class="onm-button" id="add-external-link">{t}Add{/t}</a>
                        </form>
                    </div>


                    {if count($categories) > 0}
                    <h3 href="#listado">{t}Frontpages{/t}</h3>
                    <div id="listado">
                        <ul id='availableCategories' class="elementsContainer">
                            {foreach $categories as $category}
                                <li id="cat_{$category->pk_content_category}"
                                    data-title="{$category->title}"
                                    data-type="category"
                                    data-link="{$category->name}"
                                    data-item-id="{$category->pk_content_category}"
                                    class="drag-category"
                                    pk_menu="">
                                    <div>
                                        <span class="type">{t}Frontpage{/t}:</span>
                                        <span class="menu-title">{$category->title}</span>
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}

                    {is_module_activated name="ALBUM_MANAGER"}
                    {if count($albumCategories) > 0}
                    <h3 href="#listadoAlbum">{t}Album Categories{/t}</h3>
                    <div id="listadoAlbum">
                        <ul id='albumCategories' class="elementsContainer">
                            {section name=as loop=$albumCategories}
                            <li id="album_{$albumCategories[as]->pk_content_category}"
                                data-title="{$albumCategories[as]->title}"
                                data-type="albumCategory"
                                data-link="{$albumCategories[as]->name}"
                                data-item-id="{$albumCategories[as]->pk_content_category}"
                                class="drag-category"
                                pk_menu="">
                                <div>
                                    <span class="type">{t}Album category{/t}:</span>
                                    <span class="menu-title">{$albumCategories[as]->title}</span>
                                    <div class="btn-group actions" style="float:right;">
                                        <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                        <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                        <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            {/section}
                        </ul>
                     </div>
                    {/if}
                    {/is_module_activated}

                    {is_module_activated name="VIDEO_MANAGER"}
                    {if count($videoCategories) > 0}
                    <h3 href="#listadoVideo">{t}Video Categories{/t}</h3>
                    <div id="listadoVideo">
                        <ul id='videoCategories' class="elementsContainer">
                            {section name=as loop=$videoCategories}
                            <li id="video_{$videoCategories[as]->pk_content_category}"
                                data-title="{$videoCategories[as]->title}"
                                data-type="videoCategory"
                                data-link="{$videoCategories[as]->name}"
                                data-item-id="{$videoCategories[as]->pk_content_category}"
                                class="drag-category"
                                pk_menu="">
                                <div>
                                    <span class="type">{t}Video category{/t}:</span>
                                    <span class="menu-title">{$videoCategories[as]->title}</span>
                                    <div class="btn-group actions" style="float:right;">
                                        <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                        <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                        <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            {/section}
                        </ul>
                    </div>
                    {/if}
                    {/is_module_activated}

                    {is_module_activated name="POLL_MANAGER"}
                    {if count($pollCategories) > 0}
                    <h3 href="#listadoPoll">{t}Poll Categories{/t}</h3>
                    <div id="listadoPoll">
                        <ul id='pollCategories' class="elementsContainer">
                            {section name=as loop=$pollCategories}
                            <li id="video_{$pollCategories[as]->pk_content_category}"
                                data-title="{$pollCategories[as]->title}"
                                data-type="pollCategory"
                                data-link="{$pollCategories[as]->name}"
                                data-item-id="{$pollCategories[as]->pk_content_category}"
                                class="drag-category" pk_menu="">
                                <div>
                                    <span class="type">{t}Poll category{/t}:</span>
                                    <span class="menu-title">{$pollCategories[as]->title}</span>
                                    <div class="btn-group actions" style="float:right;">
                                        <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                        <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                        <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            {/section}
                        </ul>
                    </div>
                    {/if}
                    {/is_module_activated}

                    {if count($pages) > 0}
                    <h3 href="#frontpages">{t}Modules{/t}</h3>
                    <div id="frontpages">
                        <ul id='availablePages' class="elementsContainer">
                            {foreach from=$pages item=value key=page}
                                <li id="page_{$page}"
                                    data-item-id="{$page}"
                                    data-title={if $page eq 'frontpage'}"Portada"
                                            {elseif $page eq 'poll'}"Encuesta"
                                            {elseif $page eq 'kiosko'}"Portadas Papel"
                                            {elseif $page eq 'letter'}"Textos Al Director"
                                            {elseif $page eq 'boletin'}"Bolet&iacute;n"
                                            {else}{$page}{/if}
                                    data-link={if $page eq 'frontpage'}"/"
                                            {elseif $page eq 'poll'}"encuesta/"
                                            {elseif $page eq 'kiosko'}"portadas-papel/"
                                            {elseif $page eq 'letter'}"participa/"
                                            {elseif $page eq 'boletin'}"newsletter/"
                                            {else}"{$page}/"{/if}
                                    data-type="internal"
                                    class="drag-category"
                                    pk_menu="">
                                    <div>
                                        <span class="type">{t}Module{/t}:</span>
                                        <span class="menu-title">
                                            {if $page eq 'frontpage'}
                                                Portada
                                            {elseif $page eq 'poll'}
                                                Encuesta
                                            {elseif $page eq 'letter'}
                                                Textos Al Director
                                            {elseif $page eq 'kiosko'}
                                                Portadas Papel
                                            {elseif $page eq 'boletin'}
                                                Bolet&iacute;n
                                            {else}
                                                {$page}
                                            {/if}
                                        </span>
                                        <div class="btn-group actions" style="float:right;">
                                        <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}

                    {if count($staticPages) > 0}
                    <h3 href="#staticPages">{t}Static Pages{/t}</h3>
                    <div id="staticPages">
                          <ul id='availableStatics' class="elementsContainer">
                             {section name=k loop=$staticPages}
                                 <li id="static_{$staticPages[k]->id}"
                                    data-title="{$staticPages[k]->title}"
                                    data-item-id="{$staticPages[k]->id}"
                                    data-type="static"
                                    data-link="{$staticPages[k]->slug}"
                                    pk_menu="{$staticPages[k]->id}"
                                    class="drag-category">
                                    <div>
                                        <span class="type">{t}Static page{/t}:</span>
                                        <span class="menu-title">{$staticPages[k]->title}</span>
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                    </div>
                                 </li>
                             {/section}
                         </ul>
                    </div>
                    {/if}

                    {is_module_activated name="SYNC_MANAGER"}
                    {if count($elements) > 0}
                    <h3 href="#syncCategories">{t}Sync Categories{/t}</h3>
                    <div id="syncCategories" style="border:1px solid #CCCCCC;padding: 4px;">
                        {foreach $elements as $config name=colors}
                            {foreach from=$config key=site item=syncCategories}
                            <strong>{$site}                    </strong>
                            <ul id='availableSync' class="elementsContainer">
                                {foreach $syncCategories as $category}
                                <li id="sync_category"
                                    data-title="{$category|capitalize}"
                                    data-type="syncCategory"
                                    data-link="{$category}"
                                    class="drag-category"
                                    pk_menu=""
                                    style="background-color: #{$colors[$site]}">
                                    <div>
                                        <span class="type">{t}Sync category{/t}:</span>
                                        <span class="menu-title">{$category|capitalize}</span>
                                        <img src="{$params.IMAGE_DIR}sync-icon.png"
                                             alt="{t}Sync{/t}" >
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                        </div>
                                </li>
                                {/foreach}
                            </ul>
                            {/foreach}
                        {/foreach}
                    </div>
                    {/if}
                    {/is_module_activated}

                    {is_module_activated name="FRONTPAGES_LAYOUT"}
                    {if count($categories) > 0}
                    <h3 href="#listado">{t}Automatic Categories{/t}</h3>
                    <div id="listado">
                        <ul id='availableCategories' class="elementsContainer">
                            {foreach $categories as $blog}
                                <li id="cat_{$blog->pk_content_category}"
                                    data-title="{$blog->title}"
                                    data-type="blog-category"
                                    data-link="{$blog->name}"
                                    data-item-id="{$blog->pk_content_category}"
                                    class="drag-category"
                                    pk_menu="">
                                    <div>
                                        <span class="type">{t}Automatic Categories{/t}:</span>
                                        <span class="menu-title">{$blog->title}</span>
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}
                    {/is_module_activated}

                    {is_module_activated name="FRONTPAGES_LAYOUT"}
                    {is_module_activated name="SYNC_MANAGER"}
                    {if count($elements) > 0}
                    <h3 href="#syncBlogCategories">{t}Sync Blog Categories{/t}</h3>
                    <div id="syncBlogCategories"  class="elementsContainer">
                        {foreach $elements as $config name=colors}
                            {foreach from=$config key=site item=syncBlogCategories}
                            <strong>{$site}     </strong>
                            <ul id='availableSync' class="elementsContainer">
                                {foreach $syncBlogCategories as $category}
                                <li id="sync_category"
                                    data-title="{$category|capitalize}"
                                    data-type="syncBlogCategory"
                                    data-link="{$category}"
                                    class="drag-category"
                                    pk_menu=""
                                    style="background-color: #{$colors[$site]}">
                                    <div>
                                        <span class="type">{t}Sync blog category{/t}:</span>
                                        <span class="menu-title">{$category|capitalize}</span>
                                        <img src="{$params.IMAGE_DIR}sync-icon.png"
                                             alt="{t}Sync category blog{/t}" >
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                        </div>
                                        </div>
                                </li>
                                {/foreach}
                            </ul>
                            {/foreach}
                        {/foreach}
                    </div>
                    {/if}
                    {/is_module_activated}
                    {/is_module_activated}
                </div>
            </div>
        </div><!-- fin -->

        <input type="hidden" name="items" id="items" value="" />
        <input type="hidden" name="items-hierarchy" id="items-hierarchy" value="" />
    </div><!--fin wrapper-content-->
</form>

{include file="menues/modals/_modalAddItem.tpl"}
{/block}
