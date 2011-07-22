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
        if (confirm('¿Está seguro de querer eliminar este fichero?')) {
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
        <div class="title"><h2>{t 1=$datos_cat[0]->title}Image manager :: Upload image to "%1"{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search"name="submit_mult" value="Buscar Imágenes">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />{t}Search{/t}
                </a>
            </li>
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_all" name="submit_mult" value="Catálogo de Fotos">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}folder_image.png" alt="Catálogo de Fotos"><br />{t}Photo catalog{/t}
                </a>
            </li>
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_today"  name="submit_mult" value="Fotos de Hoy">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}image_today.png" alt="Fotos de Hoy"><br />{t}Today photos{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <ul class="tabs2">
        <li>
            <a href="mediamanager.php?listmode={$listmode}&category=GLOBAL" {if $category==0}style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
                {t}GLOBAL{/t}</a>
        </li>

        <li>
            <a href="mediamanager.php?listmode={$listmode}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                {t}ADS{/t}</a>
        </li>

        {include file="menu_categorys.tpl" home="mediamanager.php?listmode="}

    </ul>


    <div id="upload-photos" >

        <table class="adminheading">
            <tbody>
                <tr>
                    <th>{t}Uploding an image{/t}</th>
                </tr>
            </tbody>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo adminlist">
            <tbody>
                <tr>
                    <td align="left">
                        <div>
                            <form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addPhoto" method="POST" enctype="multipart/form-data">

                                <input type="hidden" id="action" name="action" title="Título" value="addPhoto" />

                                <div id="wrapper-form-upload" style="margin:20px auto; width:50%">

                                    <div id="fotosContenedor">
                                        <div style="text-align:right">
                                            <input type="button" onclick="addFile();" style="cursor:pointer;" value="{t}Add new image{/t}"></input>
                                            <input type="button" onclick="delFile();" style="cursor:pointer;" value="{t}Remove last image{/t}"></input>
                                        </div>
                                        <hr />
                                        <div class="marcoFoto" id="foto0">
                                            <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                            <p> {t}Photo #0{/t}
                                                <input type="hidden" id="title" name="title" title="Título" value="" readonly="readonly" />
                                                <input type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/>
                                                <div id="fileCat[0]" name="fileCat[0]" style="display:none;">
                                                    <table border="0" bgcolor="red" cellpadding="4">
                                                        <tr>
                                                            <td>
                                                                {t}Invalid image: the filename name contains spaces or special chars.{/t}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <input type="hidden" name="category" value="{$category}" />
                                                <input type="hidden" name="media_type" value="image" />
                                            </p>
                                        </div>
                                    </div>

                                    <div id="explanations" style="margin:20px auto; width:50%; border:1px solid #ccc; padding:5px 15px">
                                        <h2>{t}How I can use this form?{/t}</h2>
                                        <br /><br />

                                        <table>
                                            <tr>
                                                <td>
                                                    <img src="{$params.IMAGE_DIR}add.png" border="0" alt="Añadir fichero" width="22px" height="22px" />
                                                </td>
                                                <td>
                                                    {t}This icon ADDS one image to the upload form{/t}<br />
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <img src="{$params.IMAGE_DIR}del.png" border="0" alt="Añadir fichero" width="22px" height="22px" />
                                                </td>
                                                <td>
                                                    {t}This icon DELETES one image from the upload form{/t}<br />
                                                </td>
                                            </tr>
                                        </table>

                                        <ul>
                                            <li>{t}The max size allowed for images is 200 kb.{/t}</li>
                                            <li>{t escape="off"}You <strong>ONLY</strong> can upload <strong>10</strong> images at the same time{/t}</li>
                                            <li>The uploaded images will be stored in the selected category folder</li>
                                        </ul>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
		<div class="action-bar clearfix">
			<div class="right">
				<button type="submit" class="onm-button red">{t}Upload files{/t}</button>
			</div>
		</div>

    </div>

</div>
{/block}
