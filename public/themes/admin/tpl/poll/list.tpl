{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_polls category=$category page=$page}" method="GET" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Polls{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category == 'widget'}{t}WIDGET HOME{/t}{elseif $category == 'all'}{t}All categories{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Special elements{/t}</h4>
                        <a href="{url name=admin_polls_widget}" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                        {include file="common/drop_down_categories.tpl" home={url name=admin_polls l=1}}
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="POLL_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-poll-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="POLL_AVAILABLE"}
                <li>
                    <button id="batch-unpublish" type="submit" name="status" value="1" title="{t}UnPublish{/t}">
                        <img src="{$params.IMAGE_DIR}publish_no.gif" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </button>
                </li>
                <li>
                    <button id="batch-publish" type="submit" name="status" value="0" title="{t}Publish{/t}">
                        <img src="{$params.IMAGE_DIR}publish.gif" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="POLL_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{url name=admin_polls_config}" class="admin_add" title="{t}Config album module{/t}">
                            <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                {/acl}
                {acl isAllowed="POLL_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_poll_create}" title="{t}New poll{/t}">
                        <img src="{$params.IMAGE_DIR}/poll-new.png" alt="{t}New poll{/t}"><br />{t}New poll{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <table class="table table-hover table-condensed">

            <thead>
               <tr>
                    {if count($polls) > 0}
                        <th style="width:15px;"><input type="checkbox" class="toggleallcheckbox"></th>
                        <th>{t}Title{/t}</th>
                        {if $category == 'widget' || $category == 'all'}
                            <th style="width:65px;" class="center">{t}Section{/t}</th>
                        {/if}
                        <th class="center" style="width:40px">{t}Votes{/t}</th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                        <th style="width:110px;" class="center">{t}Date{/t}</th>
                        <th style="width:40px;" class="center">{t}Published{/t}</th>
                        <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                        <th style="width:40px;" class="center">{t}Home{/t}</th>
                        <th class="center">{t}Actions{/t}</th>
                    {else}
                        <th scope="col" colspan="10">&nbsp;</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {foreach name=c from=$polls item=poll}
                <tr >
                    <td>
                        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$poll->id}">
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
                        {$poll->title|clearslash}
                    </td>
                    {if $category == 'widget' || $category == 'all'}
                    <td class="center">
                        {$poll->category_title}
                    </td>
                    {/if}
                    <td class="center">
                        {$poll->total_votes}
                    </td>
                    <td class="center">
                        {$poll->views}
                    </td>
                    <td class="center">
                        {$poll->created}
                    </td>
                    <td class="center">
                        {acl isAllowed="POLL_AVAILABLE"}
                        {if $poll->available == 1}
                        <a href="{url name=admin_poll_toggleavailable id=$poll->id status=0 category=$category page=$page}" title="Publicado">
                            <img src="{$params.IMAGE_DIR}publish_g.png" alt="Publicado" />
                        </a>
                        {else}
                        <a href="{url name=admin_poll_toggleavailable id=$poll->id status=1 category=$category page=$page}" title="Pendiente">
                            <img src="{$params.IMAGE_DIR}publish_r.png" alt="Pendiente" />
                        </a>
                        {/if}
                        {/acl}
                    </td>
                    <td class="center">
                        {acl isAllowed="POLL_FAVORITE"}
                        {if $poll->favorite == 1}
                        <a href="{url name=admin_poll_togglefavorite id=$poll->id status=0 category=$category page=$page}" class="favourite_on" title="{t}Favorite{/t}">
                            &nbsp;
                        </a>
                        {else}
                        <a href="{url name=admin_poll_togglefavorite id=$poll->id status=1 category=$category page=$page}" class="favourite_off" title="{t}NoFavorite{/t}">
                            &nbsp;
                        </a>
                        {/if}
                        {/acl}
                    </td>
                    <td class="center">
                    {acl isAllowed="POLL_HOME"}
                        {if $poll->in_home == 1}
                           <a href="{url name=admin_poll_toggleinhome id=$poll->id status=0 category=$category page=$page}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_poll_toggleinhome id=$poll->id status=1 category=$category page=$page}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    <td class="right">
                        <div class="btn-group">
                            {acl isAllowed="POLL_UPDATE"}
                            <a class="btn"
                                href="{url name=admin_poll_show id=$poll->id}"
                                title="Modificar">
                                <i class="icon-pencil"></i>
                            </a>
                            {/acl}
                            {acl isAllowed="POLL_DELETE"}
                            <a class="del btn btn-danger"
                                data-controls-modal="modal-from-dom"
                                data-url="{url name=admin_poll_delete id=$poll->id}"
                                data-title="{$poll->title|capitalize}"
                                href="{url name=admin_poll_delete id=$poll->id}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                            {/acl}
                       </ul>
                    </td>
                </tr>

               {foreachelse}
               <tr>
                   <td class="empty" colspan="10">
                        {t}There is no polls yet.{/t}
                   </td>
               </tr>
               {/foreach}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="11">
                        <div class="pagination">
                            {$paginacion->links}&nbsp;
                        </div>
                    </td>
                </tr>
            </tfoot>

         </table>

        <input type="hidden" name="category" value="{$category}" />
        <input type="hidden" name="page" value="{$page}" />

    </div>
</form>
    <script>
        jQuery('#batch-unpublish, #batch-publish').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_polls_batchpublish}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
    {include file="poll/modals/_modalDelete.tpl"}
    {include file="poll/modals/_modalBatchDelete.tpl"}
    {include file="poll/modals/_modalAccept.tpl"}

{/block}
