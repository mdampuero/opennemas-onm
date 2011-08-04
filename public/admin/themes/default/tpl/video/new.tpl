{extends file="base/admin.tpl"}

{block name="header-css" append}
	<style type="text/css">
	table.adminlist label {
		padding-right:10px;
		width:100px !important;
		display:inline-block;
	}
	table.adminlist input, table.adminlist textarea{
		width:70%;
	}
	label {
		display:block;
		color:#666;
		text-transform:uppercase;
	}
	.utilities-conf label {
		text-transform:none;
	}
	</style>
{/block}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsVideo.js"></script>

{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >

<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Video manager{/t} :: {if $smarty.request.action eq "new"}{t}Creating video{/t}{else}{t}Editing video{/t}{/if}</h2></div>
		<ul class="old-button">
			<li>
			{if isset($video->id)}
				{acl isAllowed="VIDEO_UPDATE"}
					<a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$video->id}', 'formulario');" >
				{/acl}
			{else}
				{acl isAllowed="VIDEO_CREATE"}
					<a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');" >
				{/acl}
			{/if}
					<img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
				</a>
			</li>
			{acl isAllowed="VIDEO_CREATE"}
			<li>
				<a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$video->id}', 'formulario');" value="Validar" title="Validar">
					<img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
				</a>
			</li>
			{/acl}
			<li class="separator"></li>
			<li>
				<a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}" value="{t}Go Back{/t}" title="{t}Go Back{/t}">
					<img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go Back{/t}" alt="{t}Go Back{/t}" ><br />{t}Go Back{/t}
				</a>
			</li>
		</ul>
	</div>
</div>

        <div class="wrapper-content">


			<table class="adminheading">
				<tr>
					<td>{t}Enter video information{/t}</td>
				</tr>
			</table>
			<table class="adminform">
				<tbody>
					<tr>
						<td></td>
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
													<option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
													{section name=su loop=$subcat[as]}
														<option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
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
												 <option value="1" {if $video->available eq '1'} selected {/if}>Si</option>
												 <option value="0" {if $video->available eq '0'} selected {/if}>No</option>
											</select>
										</td>
									</tr>
								</table>
							</div>

							<br>

							<input type="hidden" value="1" name="content_status">
							<div class="help-block">
								<div class="title"><h4>Get API keys</h4></div>
								<div class="content">
									{t}For now OpenNeMas only accepts videos from:{/t}:
									<table style="padding:10px;">
										<tr style="width:40%">
											<td><strong>Youtube</strong></td>
											<td>http://www.youtube.com</td>
										</tr>
										<tr>
											<td><strong>Vimeo</strong></td>
											<td>http://vimeo.com</td>
										</tr>
										<tr>
											<td><strong>Metacafe</strong></td>
											<td>http://metacafe.com</td>
										</tr>
										<tr>
											<td><strong>Dailymotion</strong></td>
											<td>http://dailymotion.com</td>
										</tr>
										<tr>
											<td><strong>Collegehumor</strong></td>
											<td>http://collegehumor.com</td>
										</tr>
										<tr>
											<td><strong>Blip.tv</strong></td>
											<td>http://blip.tv</td>
										</tr>
										<tr>
											<td><strong>Myspace</strong></td>
											<td>http://vids.myspace.com</td>
										</tr>
										<tr>
											<td><strong>Ted Talks</strong></td>
											<td>http://www.ted.com/talks</td>
										</tr>
										<tr>
											<td><strong>11870.com</strong></td>
											<td>http://11870.com</td>
										</tr>
										<tr>
											<td><strong>Marca.tv</strong></td>
											<td>http://www.marca.tv</td>
										</tr>
										<tr>
											<td><strong>Dalealplay</strong></td>
											<td>http://www.dalealplay.com</td>
										</tr>
										<tr>
											<td><strong>Rutube</strong></td>
											<td>http://www.rutube.ru</td>
										</tr>
									</table>
								</div>
							</div>

						</td>
					</tr>
					<tr>
						<td style="padding:10px; vertical-align:top;">
							<label for="video_url">
							{if isset($video)}
								{t}Video URL:{/t}
							{else}
								{t}Write the video url in the next input and push "Get video information"{/t}
							{/if}
							</label>
							<input type="text" id="video_url" name="video_url" title="Video url"
									value="{$video->video_url|default:""}" class="required" style="width:70%"
									onChange="javascript:loadVideoInformation(this.value);"/> &nbsp;
							<a href="#" class="onm-button blue"
								 onClick="javascript:loadVideoInformation($('video_url').value); return false;">
								{t}Get video information{/t}
							</a>
						</td>
					</tr>
					<tr>
						<td style="width:100%; padding:10px"colspan="2">
							<div id="video-information">
								{* AJAX LOAD *}
								{if $smarty.request.action eq "read"}
									{include file="video/partials/_video_information.tpl"}
								{/if}
							</div>
						</td>
					</tr>
				</tbody>
				<tfooter>
					<tr>
						<td></td>
					</tr>
				</tfooter>
			</table>

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$video->id}" />
	</div>
</form>
{/block}
