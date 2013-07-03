{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script>
jQuery(function($){
    $('#batch-delete').on('click', function(){
        var form = $('#authorform');
        form.attr('action', '{url name="admin_opinion_author_batchdelete"}');
    });
});
</script>
{/block}


{block name="content"}
<form action="{url name=admin_opinion_authors}" method="get" id="authorform">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Authors{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" id="batch-delete" title="{t}Delete selected authors{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_opinion_author_create}" title="{t}Create new author{/t}">
                        <img src="{$params.IMAGE_DIR}user_add.png" alt="Nuevo"><br />{t}New author{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <table class="table table-hover table-condensed">
            {if count($users) gt 0}
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th class="center" style="width:20px;">{t}Avatar{/t}</th>
                    <th class="left">{t}Full name{/t}</th>
                    <th class="left" >{t}Biography{/t}</th>
                    <th class="center" style="width:10px">{t}Actions{/t}</th>
                </tr>
            </thead>
            {/if}
            <tbody>
                {foreach from=$users item=user name=user_listing}
                <tr>
                    <td>
                        <input type="checkbox" name="selected[]" value="{$user->id}">
                    </td>
                    <td class="center">
                        {if is_object($user->photo) && !is_null($user->photo->name)}
                        {dynamic_image src="{$user->photo->path_file}/{$user->photo->name}" transform="thumbnail,40,40"}
                        {else}
                        {gravatar email="{$user->email}" image_dir=$params.IMAGE_DIR image=true size="40"}
                        {/if}
                    </td>

                    <td class="left">
                        <a href="{url name=admin_opinion_author_show id=$user->id}" title="{t}Edit user{/t}">
                            {$user->name}
                        </a>
                    </td>

                    <td class="left">
                        {$user->bio}
                    </td>

                    <td class="right nowrap">
                        <div class="btn-group">
                            <a class="btn" href="{url name=admin_opinion_author_show id=$user->id}" title="{t}Edit user{/t}">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>

                            <a class="del btn btn-danger"
                                href="{url name=admin_opinion_author_delete id=$user->id}"
                                data-url="{url name=admin_opinion_author_delete id=$user->id}"
                                data-title="{$user->name}"
                                title="{t}Delete this user{/t}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                {foreachelse}
                <tr>
                    <td colspan="7" class="empty">
                        {t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr >
                    <td colspan="7" class="center">
                        <div class="pagination">
                            {$pagination->links|default:""}&nbsp;
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
{include file="opinion/modals/_modalDelete.tpl"}
{/block}