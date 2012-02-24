<div data-content-id="{$content->id}" data-class="Article" class="content-provider-element clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.article_loop.index}">
        <div class="title">
            <span class="type">{t}Article{/t}</span>
            {$content->title}
        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            {t}Actions{/t}
        <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="/admin/article.php?action=read&amp;id={$content->id}&amp;category={$smarty.request.category}">
                    <i class="icon-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                <a title="{t}Suggest to home{/t}" href="#" class="drop-element">
                    <i class="icon-ban"></i> {t}Remove{/t}
                </a>
            </li>
            <li>
                <a title="{t}Arquive{/t}" href="#" class="arquive">
                    <i class="icon-inbox"></i> {t}Arquive{/t}
                </a>
            </li>
            <li>
                {if !$params['home']}
                <a title="{t}Suggest to home{/t}" href="#" class="suggest-home">
                    <i class="icon-home"></i> {t}Suggest to home{/t}
                </a>
                {/if}
            </li>
            <li class="divider"></li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=delete&amp;id={$content->id}&amp;category={$category}" title="{t}Delete{/t}" class="send-to-trash">
                    <i class="icon-trash"></i> {t}Send to trash{/t}
                </a>
            </li>

            <!--
            <li class="divider"></li>
            <li>
                <a title="{t}Settings{/t}" href="#">
                    {t}Settings{/t}
                </a>
            </li> -->
        </ul>
    </div>
</div>

{*

<!-- <table id='tabla{$aux}' name='tabla{$aux}' value="{$suggestedArticles[d]->id}" data="Article" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
    <tr class="row1{schedule_class item=$suggestedArticles[d]}" style="cursor:pointer;">

        {if $category neq 'home'}
            <td align="center">
                <div class="inhome" style="display:inline;">
                    {if $suggestedArticles[d]->in_home == 1}
                    <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                          <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                          </a>
                    {elseif $suggestedArticles[d]->in_home == 2}
                        <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                        <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" title="No sugerir en home" alt="No sugerir en home"/></a>
                    {else}
                        <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
                    {/if}
                </div>
            </td>
        {else}
            <td align="center">
                {$suggestedArticles[d]->category_name}
            </td>
        {/if}
        <td style="width:80px; text-align:right; padding-right:10px;">

            <ul class="action-buttons">

                <li>
                    <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$suggestedArticles[d]->id}') "  title="Archivar">
                       <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" />
                    </a>
                </li>
                {if $category neq 'home'}
                    <li>
                        {if $suggestedArticles[d]->frontpage == 1}
                        <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                            <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" />
                        </a>
                        {else}
                        <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" />
                        </a>
                        {/if}
                    </li>
                    <li>
                        <a href="#" onClick="javascript:delete_article('{$suggestedArticles[d]->id}'','{$category}',0);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                    </li>
                {else}
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}'&action=inhome_status&status=0&category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                    </li>
                {/if}
            </ul>

        </td>

    </tr>
</table> -->
*}