<html>
    <head>
        {css_tag href="/admin.css"}
        {css_tag href="/buttons.css"}
        <!--[if IE]{css_tag href="/ieadmin.css.css"}[endif]-->
        {script_tag src="/prototype.js" language="javascript"}
        {script_tag src="/scriptaculous/scriptaculous.js" language="javascript"}
        {$script|default:""}
    </head>

    <body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
        <form id="form_upload" action="{$smarty.server.PHP_SELF}?action=addPhoto" method="POST" enctype="multipart/form-data">
            <div id="upload-photos" >
                <table class="adminform" style="width:65%;">
                    <tbody>
                        <tr>
                            <td style="text-align:left; vertical-align:top; width: 10%;">
                                <input type="hidden" id="action" name="action" title="Título" value="addPhoto" />

                                <div id="fotosContenedor" style=" padding:20px; width: 400px;">
                                    <div class="marcoFoto" id="foto0">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        <input type="hidden" id="nameAuthor" name="nameAuthor" title="nameAuthor" value="{$nameAuthor|default:""}" size="40" />
                                        <input type="hidden" id="titles" name="titles" title="Título" value="" readonly="readonly" />
                                        <input type="file" name="file[0]" id="fFile0" class="required" size="30" onChange="ckeckName(this,'fileCat[0]');"/>
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
                                    </div>

                                </div>
                                <div class="right" style="text-align:right;margin-right: 55px;">
                                    <button type="submit" onClick="return getNameAuthor();" class="onm-button red">{t}Upload photo{/t}</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </body>
</html>
