{extends file="base/admin.tpl"}


{block name="footer-js" append}
    {script_tag src="/photos.js" defer="defer" language="javascript"}
    {if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
        <div class="message" id="console-info">{$smarty.request.message}</div>
        <script defer="defer" type="text/javascript">
            new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
        </script>
    {/if}

    <script defer="defer" type="text/javascript">
    function confirmar(url) {
        if(confirm('¿Está seguro de querer eliminar este fichero?')) {
            location.href = url;
        }
    }
    </script>

    {if !empty($smarty.request.alerta)}
    <script type="text/javascript">
        alert("NO SE PUEDE ELIMINAR {$smarty.request.name} .\n Esta imagen está siendo utilizada en: {$smarty.request.alerta}.");
    </script>
    {/if}
{/block}

{block name="content"}
<form id="form_upload" action="{$smarty.server.PHP_SELF}" method="GET">

    <div class="top-action-bar">
        <div class="wrapper-content">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2></h2></div>
            <div class="title"><h2> {t}Media manager{/t} :: {t 1=$foto->name}Deleting image '%1'{/t}</h2></div>
            <ul class="old-button">
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action={$smarty.session.desde}" class="admin_add" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" />
                        <br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <div style="width:700px; margin:0 auto;">
                <table class="listing-table">
                    <thead>
                        <tr>
                            <th>
                                {t}Unable to delete this photo directly as it's been used by some contents.{/t}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                {foreach from=$related_contents item=related_content name=related_contents}
                                    <li>{$related_content->title}</li>
                                {/foreach}
                                </ul><!-- / -->
                            </td>
                        </tr>
                    </tbody>
                </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <button type="submit" class="onm-button red">{t}Delete{/t}</button>
                </div>
            </div>
        </div>

    </div><!--wrapper-content-->
    <input type="hidden" name="category" value="{$foto->category}" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="force" value="yes" />
    <input type="hidden" name="id" value="{$id}" />
</form>
{/block}
