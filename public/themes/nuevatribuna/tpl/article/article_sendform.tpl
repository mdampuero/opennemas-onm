{if $smarty.request.action == 'sendform'}
<html>
    <head>
        <link href="{$params.CSS_DIR}/onm-mockup.css" media="screen" rel="stylesheet" type="text/css"/>
        <style type="text/css">
        body{
                background:White;
            }
        </style>
    </head>
<body>
    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>

    {literal}
    <script type="text/javascript">
        $('#sendform').submit(function(event) {
            event.preventDefault();
            $.ajax({
                    method:'post',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(xhr, resultado) {
                        $('#sendform').html(xhr.responseText);
                        $('#resultado').hide();
                        $('#facebox').hide();
                        $('#facebox_overlay').hide();
                    },
                    error: function(xhr, resultado) {
                        $('#resultado').html(xhr.responseText)
                            .effect("highlight", {}, 3000)
                            .css({color:'Red'});

                    }
            });
        });
    </script>
    {/literal}

    <div id="sendform_content">

        <h4>Envia esta noticia a un amigo</h4>
        <div id="resultado"></div>
        <form id="sendform" method="POST" action="/controllers/article.php?category_name={$category_name}&subcategory_name={$subcategory_name}">
            <input type="hidden" name="article_id" value="{$article->id}" />
            <input type="hidden" name="action" value="send" />
            <input type="hidden" name="token"  value="{$token}" />

            <table width="100%" border="0" cellpadding="3" cellspacing="2" class="tabular-form">
                <tr>
                    <th><label for="name_sender">Tu nombre:</label></th>
                    <td><input name="name_sender" type="text" size="42" /></td>
                </tr>
                <tr>
                    <th><label for="sender">Tu e-mail:</label></th>
                    <td><input name="sender" type="text" size="42" /></td>
                </tr>
                <tr>
                    <th><label for="destination">E-mail destinatario(s):</label></th>
                    <td><input name="destination" type="text" size="42" /><br>
                    <font style="font-size: 0.80em;">Para enviar a varios destinatarios, separar por coma.</font> </td>
                </tr>
                <tr>
                    <th><label for="body">Tu comentario:</label></th>
                    <td>
                        <textarea name="body" rows="8" cols="50" size="50">{$article->title|clearslash}</textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input id="submitform" type="submit" value="Enviar" class="button" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
    {/if}

    {if $smarty.request.action == 'send'}
        <div class="message">{$message} - <a href="javascript:$.facebox.close(); return false;">Pechar esta xanela</a></div>
    {/if}
        
</body>
</html>

