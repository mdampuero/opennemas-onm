{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}

    <script>
    jQuery(document).ready(function ($){
        $('[rel="tooltip"]').tooltip();
        $('.minput, #toggleallcheckbox').on('click', function() {
            checkbox = $(this).find('input[type="checkbox"]');
            checkbox.attr(
               'checked',
               !checkbox.is(':checked')
            );
            var checked_elements = $('input[type="checkbox"]:checked').length;
            if (checked_elements > 0) {
                $('.old-button .batch-actions').fadeIn('fast');
            } else {
                $('.old-button .batch-actions').fadeOut('fast');
            }
        });
    });
    </script>
{/block}

{block name="content"}
<form action="{url name=admin_articles}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Article Manager{/t} :: {t}Listing articles{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="ARTICLE_DELETE"}
                <li>
                    <button type="submit" class="batch-delete">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar"><br />{t}Delete{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="ARTICLE_AVAILABLE"}
                <li class="batch-actions">

                <a href="#">
                    <img src="{$params.IMAGE_DIR}/select.png" title="" alt="" />
                    <br/>{t}Batch actions{/t}
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <button type="submit" name="status" value="0" href="#" id="batch-publish">
                            {t}Batch publish{/t}
                        </button>
                    </li>
                    <li>
                        <button type="submit" name="status" value="1" id="batch-unpublish">
                            {t}Batch unpublish{/t}
                        </a>
                    </li>
                    <li>
                        <button type="submit" name="status" value="0" id="batch-inhome">
                            {t escape="off"}Batch in home{/t}
                        </a>
                    </li>
                    <li>
                        <button type="submit" name="status" value="1" id="batch-noinhome">
                            {t escape="off"}Batch drop from home{/t}
                        </a>
                    </li>
                    {acl isAllowed="OPINION_DELETE"}
                    <li>
                        <button type="submit" id="batch-delete" title="{t}Delete{/t}">
                            {t}Delete{/t}
                        </button>
                    </li>
                    {/acl}
                </ul>

            </li>
                {/acl}
                <li class="separator"></li>
                {acl isAllowed="ARTICLE_CREATE"}
                <li>
                    <a href="{url name=admin_article_create}">
                        <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New article{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <ul class="pills">
            {acl hasCategoryAccess=0}
            <li>
                <a href="{url name=admin_articles}"  {if $category=='all' || $category == 0}class="active"{/if}>{t}All categories{/t}</font></a>
            </li>
            {/acl}
            {include file="menu_categories.tpl" home="{url name=admin_articles l=1 status=$status}"}
        </ul>

        <table class="adminheading">
            <tr>
                <th class="left">
                    <span style="margin-left:5px;">{t 1=$totalArticles}%1 articles{/t}</span>
                </th>
                <th align="right">
                    <input type="text" placeholder="{t}Search by title:{/t}" name="title" value="{$title}"/>
                    {t}Status:{/t}
                    <div class="input-append">
                        <select name="status">
                            <option value="-1" {if $status === -1} selected {/if}> {t}-- All --{/t} </option>
                            <option value="1" {if  $status === 1} selected {/if}> {t}Published{/t} </option>
                            <option value="0" {if $status === 0} selected {/if}> {t}No published{/t} </option>
                        </select>
                        <button type="submit" class="btn">Search</button>
                    </div>
                </th>
            </tr>
        </table>
        <table class="listing-table">
            <thead>
                <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
                <th class="left" >{t}Title{/t}</th>
                {if $category eq 'all' || $category == 0}
                    <th class="left">{t}Section{/t}</th>
                {/if}
                <th  class="left">{t}Author{/t}</th>
                <th class="center" style="width:100px;">{t}Created{/t}</th>
                <th class="center" style="width:80px;">{t}Last Editor{/t}</th>
                <th class="center" style="width:10px;">{t}Available{/t}</th>
                <th class="center" style="width:90px;">{t}Actions{/t}</th>
            </thead>
            {foreach name=c from=$articles item=article}
            <tbody>
                <tr>
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$article->id}"  style="cursor:pointer;" />
                    </td>
                    <td class="left">
                        <span rel="tooltip" data-original-title="{t 1=$article->editor}Last author %1{/t}">{$article->title|clearslash}</span>
                    </td>
                    {if $category eq 'all' || $category == 0}
                    <td class="left">
                        {if $article->category_name == 'unknown'}
                            {t}Unasigned{/t}
                        {else}
                            {$article->category_name|upper|clearslash}
                        {/if}
                    </td>
                    {/if}
                    <td class="left">
                        {$article->agency}
                    </td>
                    <td class="center">
                        {$article->created}
                    </td>
                    <td class="center">
                        <span style="cursor:pointer;" title="editor" id="editor_{$article->id}">{$article->editor}</span>
                    </td>
                    <td class="center">
                        {if !empty($article->category) && $article->category != 20}
                        {acl isAllowed="ARTICLE_AVAILABLE"}
                        {if $article->content_status == 1}
                            <a href="{url name=admin_article_toggleavailable id=$article->id status=0 category=$category status=$status page=$page}" title="Publicado">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                            </a>
                        {else}
                            <a href="{url name=admin_article_toggleavailable id=$article->id status=1  category=$category status=$status page=$page}" title="Pendiente">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                            </a>
                        {/if}
                        {/acl}
                        {/if}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            <a class="btn" href="{url name=admin_article_show id=$article->id}" title="{t}Edit{/t}">
                                <i class="icon-pencil"></i>
                            </a>

                            <a class="del btn btn-danger"
                                data-title="{$article->title}"
                                data-url="{url name=admin_article_delete id=$article->id category=$category page=$page title=$title status=$status}"
                                href="{url name=admin_article_delete id=$article->id category=$category page=$page title=$title status=$status}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
            {foreachelse}
            <tbody>
                <tr>
                    <td class="empty" colspan="10">{t}No available articles.{/t}</td>
                </tr>
            </tbody>
            {/foreach}
            <tfoot>
                <tr>
                    <td colspan="10" class="pagination">
                        {$pagination->links}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
    <input type="hidden" name="category" value="{$category}">
</form>
{include file="article/modals/_modalDelete.tpl"}
{/block}
