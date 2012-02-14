{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/bp/screen.css"}
    {css_tag href="/bp/print.css" media="print"}
    <!--[if IE]>{css_tag href="/bp/ie.css"}<![endif]-->
    {css_tag href="/frontpagemanager.css"}

{/block}

{block name="footer-js" append}
    {script_tag src="/jquery-onm/jquery.frontpagemanager.js"}
    <script defer="defer">
        jQuery(document).ready(function($) {
           jQuery('#frontpagemanager').frontpageManager();
        });


        // Make sortable elements

        // Check when some element is dragged to show the warnings-validation hit
        // remembering to save positions
        //

        // Add the tabs for content-providers

    // ]]>
    </script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Frontpage Manager{/t} :: {if $category eq 0}{t}HOME{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">

                {if $category!='home'}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_inhome_status', 2);" name="submit_mult" value="Frontpage" title="Sugerir Home">
                        <img border="0" src="{$params.IMAGE_DIR}gosuggest50.png" title="{t}Suggest to home{/t}" alt="{t}Suggest to home{/t}" ><br />{t}Suggest to home{/t}
                    </a>
                </li>
                {/if}
                <li>
                    <a title="More actions">
                        <img border="0" src="{$params.IMAGE_DIR}home_no50.png" title="{t}No home{/t}" alt="More actions" ><br />{t}More actions{/t}
                    </a>
                </li>
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" status="0" src="{$params.IMAGE_DIR}select_button.png" title="{t}Select all{/t}" alt="{t}Select all{/t}"  status="0">
                    </button>
                </li>
                <li>
                    <a id="get_ids" href="#" class="admin_add" title="Guardar Positions" alt="Guardar Cambios">
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="{t}Save changes{/t}" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:previewFrontpage('{$category}');return false;" title="Previsualizar posiciones en portada">
                        <img border="0" src="{$params.IMAGE_DIR}preview.png" title="{t}Preview{/t}" alt="{t}Preview{/t}" ><br />{t}Preview{/t}
                    </a>
                </li>
                <li>
                     <a href="#" onclick="clearcache('{$category}'); return false;" id="button_clearcache">
                         <img border="0" src="{$params.IMAGE_DIR}clearcache.png" title="{t}Clean cache{/t}" alt="" /><br />{t}Clean cache{/t}
                     </a>
                </li>
                <li>
                     <a href="#" id="button_addnewcontents">
                         <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}Add contents{/t}" alt="" /><br />{t}Add contents{/t}
                     </a>
                </li>
            </ul><!-- /old-button -->
        </div><!-- /wrapper-content -->
    </div><!-- /top-action-bar -->


    <div class="wrapper-content">

        <ul id="categories" class="pills">
            {acl hasCategoryAccess=0}
            <li>
                <a href="/admin/controllers/frontpagemanager/frontpagemanager.php?action=list&amp;category=home" id='link_home' {if $category=='home'}class="active"{/if}>{t}HOME{/t}</a>
            </li>
            {/acl}
            {include file="menu_categories.tpl" home="/admin/controllers/frontpagemanager/frontpagemanager.php?action=list"}
        </ul><!-- /categories -->

        <div id="warnings-validation"></div><!-- /warnings-validation -->

        <div id="frontpagemanager" data-category="{$category_id}" class="clearfix">
            {$layout}
        </div><!-- /frontpagemanager -->

        <div id="content-provider" class="clearfix" title="{t}Available contents{/t}">
            <div class="content-provider-block-wrapper wrapper-content clearfix">
                <ul>
                    <li>
                        <a href="#empty">{t}Out of frontpage{/t}</a>
                    </li>
                    {if $category neq 'home'}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/article/article.php?action=other-contents-in-category-provider">{t}Other articles in this category{/t}</a>
                    </li>
                    {else}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/article/article.php?action=suggested-content-provider&category={$category}">{t}Suggested articles{/t}</a>
                    </li>
                    {/if}
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/widget/widget.php?action=content-provider&category={$category}">{t}Widgets{/t}</a>
                    </li>
                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/opinion/opinion.php?action=content-provider&category={$category}">{t}Opinions{/t}</a>
                    </li>
                </ul>

                <div id="empty">
                    {t}Drop blocks here to get them out of frontpage{/t}
                </div><!-- /empty -->
            </div>

        </div><!-- /content-provider -->
    </div>

    <input type="hidden"  id="category" name="category" value="{$category}">
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default}" />
</form>
{/block}
