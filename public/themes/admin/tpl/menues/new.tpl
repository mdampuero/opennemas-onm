{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery.nestedSortable.js"}
    {script_tag src="/onm/menues.js"}
    <script type="text/javascript">
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
    $('[rel=tooltip]').tooltip();
    </script>
{/block}

{block name="header-css" append}
    {css_tag href="/managerMenu.css" media="screen,projection"}
{/block}

{block name="content"}
<form action="{if isset($menu->pk_menu)}{url name=admin_menu_update id=$menu->pk_menu}{else}{url name=admin_menu_create}{/if}" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menu manager{/t} :: {if isset($menu->id)}{t 1=$menu->name}Editing menu "%1"{/t}{else}{t}Creating new menu{/t}{/if}</h2></div>
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
                    <h3 href="#listado">{t}Global Categories{/t}</h3>
                    <div id="listado">
                        <ul id='availableCategories' class="elementsContainer">
                            {section name=as loop=$categories}
                                <li id="cat_{$categories[as]->pk_content_category}"
                                    data-title="{$categories[as]->title}"
                                    data-type="category"
                                    data-link="{$categories[as]->name}"
                                    data-item-id="{$categories[as]->pk_content_category}"
                                    class="drag-category"
                                    pk_menu="">
                                    <div>
                                        {$categories[as]->title}
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

                    {if count($categories) > 0}
                    <h3 href="#subcategories">{t}Subcategories{/t}</h3>
                    <div id="subcategories" style="border:1px solid #CCCCCC;padding: 4px;">
                        {section name=as loop=$categories}
                            {if !empty($subcat[as])}
                                <strong>{$categories[as]->title}</strong>
                                <ul  class="elementsContainer" id="subCategories{$categories[as]->pk_content_category}">
                                {section name=su loop=$subcat[as]}
                                     <li id="subcat_{$subcat[as][su]->pk_content_category}"
                                        data-title="{$subcat[as][su]->title}"
                                        data-type="category"
                                        data-link="{$subcat[as][su]->name}"
                                        data-item-id="{$subcat[as][su]->pk_content_category}"
                                        class="drag-category"
                                        pk_menu="">
                                        <div>
                                            {$subcat[as][su]->title}
                                            <div class="btn-group actions" style="float:right;">
                                                <a href="#" class="add-item"><i class="icon-plus"></i></a>
                                                <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
                                                <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                {/section}
                                </ul>
                            {/if}
                        {/section}
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
                                    {$albumCategories[as]->title}
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
                                    {$videoCategories[as]->title}
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
                                    {$pollCategories[as]->title}
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
                                    data-item-id="{$value}"
                                    data-title="{$page}"
                                    data-link={if $page eq 'frontpage'}"home"
                                            {elseif $page eq 'poll'}"encuesta"
                                            {elseif $page eq 'letter'}"cartas-al-director"
                                            {elseif $page eq 'kiosko'}"portadas-papel"
                                            {elseif $page eq 'letter'}"cartas-al-director"
                                            {elseif $page eq 'boletin'}"newsletter"
                                            {else}{$page}{/if}
                                    data-type="internal"
                                    class="drag-category"
                                    pk_menu="">
                                    <div>
                                        {if $page eq 'frontpage'}home
                                            {elseif $page eq 'poll'}Encuesta
                                            {elseif $page eq 'letter'}Cartas Al Director
                                             {elseif $page eq 'kiosko'}Portadas Papel
                                             {elseif $page eq 'boletin'}Bolet&iacute;n
                                            {else}{$page}{/if}
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
                                    data-item-id=""
                                    data-type="static"
                                    data-link="{$staticPages[k]->slug}"
                                    pk_menu="{$staticPages[k]->id}"
                                    class="drag-category">
                                    <div>
                                        {$staticPages[k]->title}
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

                    {if count($elements) > 0}
                    <h3 href="#syncCategories">{t}Sync Categories{/t}</h3>
                    <div id="syncCategories" style="border:1px solid #CCCCCC;padding: 4px;">
                        {foreach $elements as $config name=colors}
                            {foreach from=$config key=site item=categories}
                            <strong>{$site}                    </strong>
                            <ul id='availableSync' class="elementsContainer">
                                {foreach $categories as $category}
                                <li id="sync_category"
                                    data-title="{$category|capitalize}"
                                    data-type="syncCategory"
                                    data-link="{$category}"
                                    class="drag-category"
                                    pk_menu=""
                                    style="background-color: #{$colors[$site]}">
                                    <div>
                                        {$category|capitalize}
                                        <img src="{$params.IMAGE_DIR}sync-icon.png"
                                             alt="{t}Sync{/t}" >
                                        <div class="btn-group actions" style="float:right;">
                                            <a href="#" class="add-item" rel="tooltip" data-original-title="{t}Add to menu{/t}"><i class="icon-plus"></i></a>
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



            </div>
        </div><!-- fin -->

        <input type="hidden" name="items" id="items" value="" />
        <input type="hidden" name="items-hierarchy" id="items-hierarchy" value="" />
    </div><!--fin wrapper-content-->
</form>

{include file="menues/modals/_modalAddItem.tpl"}
{/block}
