{extends file="base/admin.tpl"}


{block name="footer-js" append}
    <script defer="defer" type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
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
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Images manager :: General statistics{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search" onmouseover="return escape('<u>B</u>uscar Imagenes');" name="submit_mult" value="Buscar Imágenes">
                    <img border="0" src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />{t}Search{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <ul class="pills">
        <li>
            <a href="mediamanager.php?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}class="active"{/if}>
                {t}Global statistics{/t}
            </a>
        </li>
        {if $smarty.server.PHP_SELF eq '/admin/controllers/mediamanager/mediamanager.php'}
        {acl isAllowed="ADVERTISEMENT_ADMIN"}
            <li>
                <a href="mediamanager.php?listmode={$listmode|default:""}&category=2" {if $category==2}class="active"{/if}>
                    {t}Advertisement{/t}
                </a>
            </li>
        {/acl}
        {/if}
        {include file="menu_categories.tpl" home="mediamanager.php?listmode="}
    </ul>
    {include file="mediamanager/_partials/list_information.tpl"}
</div>
{/block}
