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
<form id="form_upload" action="{$smarty.server.PHP_SELF}?action=addPhoto" method="POST" enctype="multipart/form-data">

	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t 1=$datos_cat[0]->title}Image manager :: Upload image to "%1"{/t}</h2></div>
			<ul class="old-button">
				<li>
					<a href="{$smarty.server.PHP_SELF}?category={$category}&amp;action=list_today"  name="submit_mult" value="{t}Go Back{/t}">
						<img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}"><br />{t}Go back{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

		<ul class="pills">
			<li>
				<a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&category=GLOBAL" {if $category==0}class="active"{/if}>
					{t}GLOBAL{/t}</a>
			</li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?listmode={$listmode|default:""}&category=2" {if $category==2}class="active"{/if}>
					{t}ADS{/t}</a>
			</li>
			{include file="menu_categories.tpl" home="{$smarty.server.PHP_SELF}?listmode="}
		</ul>


		<div id="upload-photos" >

			<table class="adminheading">
				<tbody>
					<tr>
						<th>{t}Uploding an image{/t}</th>
					</tr>
				</tbody>
			</table>
			<table class="adminform">
				<tbody>
					<tr>
						<td style="text-align:left; vertical-align:top;">
							<input type="hidden" id="action" name="action" title="Título" value="addPhoto" />

							<div id="fotosContenedor" style=" padding:20px;">
								<div style="text-align:right">
									<input class="onm-button green" type="button" onclick="addFile();" style="cursor:pointer;" value="{t}+{/t}"></input>
									<input class="onm-button red" type="button" onclick="delFile();" style="cursor:pointer;" value="{t}-{/t}"></input>
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
						</td>
						<td style="padding:20px; width:30%;">

							<div class="help-block">
								<div class="title"><h4>{t}How I can use this form?{/t}</h4></div>
								<div class="content">

									<ul style="list-style:none">
										<li><span class="onm-button green">+</span><br/>{t}This icon ADDS one image to the upload form{/t}</li>
										<li><span class="onm-button red">-</span><br/>{t}This icon DELETES one image from the upload form{/t}</li>
									</ul>

									<ul style="list-style:none">
										<li>{t}The max size allowed for images is 200 kb.{/t}</li>
										<li>{t escape="off"}You <strong>ONLY</strong> can upload <strong>10</strong> images at the same time{/t}</li>
										<li>The uploaded images will be stored in the selected category folder</li>
									</ul>
								</div>
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
</form>

{/block}
