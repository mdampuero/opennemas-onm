{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
	.utilities-conf label {
		text-transform:none;
	}
    table.adminform tbody {
        padding:5px;
    }

    table th, table label {
        color: #888;
        text-shadow: white 0 1px 0;
        font-size: 13px;
    }
    th {
        vertical-align: top;
        text-align: left;
        padding: 10px;
        width: 200px;
        font-size: 13px;
    }
    label{
        font-weight:normal;
    }
    legend {
        color:#666;
        text-transform:uppercase;
        font-size:13px;
        padding:0 10px;
    }
    
    input[type="text"],
    textarea{
        width:400px;
        max-height:80%
    }
</style>
{/block}

{block name="header-js" append}
    {script_tag src="/utilsVideo.js" language="javascript"}
{/block}

{block name="content"}

<form action="{$smarty.server.PHP_SELF}?action=create" method="post" name="formulario" id="formulario" enctype="multipart/form-data">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Video manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                {if isset($video->id)}
                    {acl isAllowed="VIDEO_UPDATE"}
                        <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->id|default:""}', 'formulario');" >
                    {/acl}
                {else}
                    {acl isAllowed="VIDEO_CREATE"}
                        <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');" >
                    {/acl}
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />{t}Save{/t}
                    </a>
                </li>
                {if isset($video->id)}
                {acl isAllowed="VIDEO_CREATE"}
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->id|default:""}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />
                        {t}Save and continue{/t}
                    </a>
                </li>
                {/acl}
                {/if}
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category|default:""}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

        <div class="wrapper-content">
            {render_messages}
			<table class="adminheading">
				<tr>
					<td>{t}Enter video information{/t}</td>
				</tr>
			</table>
			<table class="adminform">
				<tbody>
					<tr>
                        <td colspan=2></td>
						<td rowspan=3 style="padding:10px; width:30%; vertical-align:top;">
							<div class="utilities-conf">
								<table>
									<tr>
										<td>
											<label for="title">{t}Section:{/t}</label>
										</td>
										<td>
											<select name="category" id="category">
												{section name=as loop=$allcategorys}
													<option value="{$allcategorys[as]->pk_content_category}" {if isset($video) && ($video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category)}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
													{section name=su loop=$subcat[as]}
														<option value="{$subcat[as][su]->pk_content_category}" {if isset($video) && ($video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category)}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
													{/section}
												{/section}
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="title">{t}Available:{/t}</label></td>
										<td>
											<select name="available" id="available"
												{acl isNotAllowed="ALBUM_AVAILABLE"} disabled="disabled" {/acl} class="required">
												 <option value="1" {if isset($video) && $video->available eq '1'} selected {/if}>Si</option>
												 <option value="0" {if isset($video) && $video->available eq '0'} selected {/if}>No</option>
											</select>
										</td>
									</tr>
								</table>
							</div>

							<br>

							<input type="hidden" value="1" name="content_status">
                            {if $smarty.get.type != "file" && ( ($video->author_name != 'internal'))}
							<div class="help-block">
								<div class="title"><h4>{t}Allowed video sources:{/t}</h4></div>
								<div class="content">
									{t}For now OpenNeMas only accepts videos from:{/t}:
									{include file="video/partials/_sourceinfo.tpl"}
								</div>
                                {/if}
							</div>

						</td>
					</tr>
                    {if $smarty.get.type == "file" || (isset($video) && $video->author_name == 'internal')}
                    
                        {include file="video/partials/_form_video_internal.tpl"}

                    {else}
                        
                        {include file="video/partials/_form_video_panorama.tpl"}

                    {/if}

				</tbody>
				<tfooter>
					<tr>
						<td></td>
					</tr>
				</tfooter>
			</table>

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$video->id|default:""}" />
	</div>
</form>
{/block}
