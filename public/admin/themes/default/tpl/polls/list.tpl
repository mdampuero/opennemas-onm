{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="GET" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Polls manager{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="POLL_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-poll-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="POLL_AVAILABLE"}
                <li>
                     <button value="batchnoFrontpage" name="buton-batchnoFrontpage" id="buton-batchnoFrontpage" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </button>
                </li>
                {/acl}
                 {acl isAllowed="POLL_AVAILABLE"}
                <li>
                    <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                    </button>
                </li>
                {/acl}
                   {acl isAllowed="POLL_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" onmouseover="return escape('<u>N</u>ew');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/poll-new.png" title="{t}New poll{/t}" alt="{t}New poll{/t}"><br />{t}New poll{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="POLL_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config album module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        <ul class="pills" style="margin-bottom: 28px;">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=home" {if $category=='home'}class="active"{/if}>{t}Widget Home{/t}</a>
            </li>
            {include file="menu_categories.tpl" home="poll.php?action=list"}
             <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=all" {if $category==='all'}class="active"{/if} >{t}All categories{/t}</a>
            </li>
        </ul>

        {render_messages}

        <table class="listing-table">

            <thead>
               <tr>
                    {if count($polls) > 0}
                        <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
                        <th>{t}Title{/t}</th>
                        <th>{t}Subtitle{/t}</th>
                        {if $category=='widget' || $category=='all'}
                            <th style="width:65px;" class="center">{t}Section{/t}</th>
                        {/if}
                        <th class="center" style="width:40px">{t}Votes{/t}</th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                        <th style="width:110px;" class="center">{t}Date{/t}</th>
                        <th style="width:40px;" class="center">{t}Published{/t}</th>
                        {if $category!='widget' && $category!='all'}
                            <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                        {/if}
                        <th style="width:40px;" class="center" onmouseover="Tip('Favorite= widgets <br> Home= view in pollfrontpage & widget home', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()">{t}Home{/t}</th>
                        <th style="width:70px;" class="center">{t}Actions{/t}</th>
                    {else}
                        <th scope="col" colspan="10">&nbsp;</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$polls}
                <tr >
                    <td>
                        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$polls[c]->id}"  style="cursor:pointer;" >
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >

                            {$polls[c]->title|clearslash}

                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();"  >
                        {$polls[c]->subtitle|clearslash}
                    </td>
                    {if $category=='widget' || $category=='all'}
                    <td class="center">
                        {$polls[c]->category_title}
                    </td>
                    {/if}
                    <td class="center">
                        {$polls[c]->total_votes}
                    </td>
                    <td class="center">
                        {$polls[c]->views}
                    </td>
                    <td class="center">
                        {$polls[c]->created}
                    </td>
                    <td class="center">
                        {acl isAllowed="POLL_AVAILABLE"}
                        {if $polls[c]->available == 1}
                        <a href="?id={$polls[c]->id}&amp;action=changeAvailable&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                        </a>
                        {else}
                        <a href="?id={$polls[c]->id}&amp;action=changeAvailable&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                        </a>
                        {/if}
                        {/acl}
                    </td>
                    {if $category!='widget' && $category!='all'}
                    <td class="center">
                        {acl isAllowed="POLL_FAVORITE"}
                        {if $polls[c]->favorite == 1}
                        <a href="?id={$polls[c]->id}&amp;action=changeFavorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="{t}Favorite{/t}">
                            &nbsp;
                        </a>
                        {else}
                        <a href="?id={$polls[c]->id}&amp;action=changeFavorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="{t}NoFavorite{/t}">
                            &nbsp;
                        </a>
                        {/if}
                        {/acl}
                    </td>
                    {/if}
                    <td class="center">
                    {acl isAllowed="POLL_HOME"}
                        {if $polls[c]->in_home == 1}
                           <a href="?id={$polls[c]->id}&amp;action=changeInHome&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="?id={$polls[c]->id}&amp;action=changeInHome&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    <td class="center">
                        <ul class="action-buttons">
                           {acl isAllowed="POLL_UPDATE"}
                           <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&id={$polls[c]->id}"
                                   title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                </a>
                           </li>
                           {/acl}
                           {acl isAllowed="POLL_DELETE"}
                            <a class="del" data-controls-modal="modal-from-dom"
                               data-id="{$polls[c]->id}"
                               data-title="{$polls[c]->title|capitalize}"  href="#" >
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                            </a>
                            {/acl}
                       </ul>
                    </td>
                </tr>

               {sectionelse}
               <tr>
                   <td class="empty" colspan="10">
                        {t}There is no polls yet.{/t}
                   </td>
               </tr>
               {/section}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="10">
                        {$paginacion->links}&nbsp;
                    </td>
                </tr>
            </tfoot>

         </table>

        <input type="hidden" name="category" id="category" value="{$category}" />
        <input type="hidden" name="status" id="status" value="" />
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
    <script>
        jQuery('#buton-batchnoFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "0");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
    {include file="polls/modals/_modalDelete.tpl"}
    {include file="polls/modals/_modalBatchDelete.tpl"}
    {include file="polls/modals/_modalAccept.tpl"}

{/block}