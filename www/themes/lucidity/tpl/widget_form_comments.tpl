
{*
    OpenNeMas project
    @theme      Lucidity
*}

<script type="text/javascript" src="{$params.JS_DIR}jquery.commentform.js"></script>
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/es_ES" type="text/javascript"></script>

<div class="form-comments span-16">
    <div class="span-14 prepend-1 form-comments-content" style="margin:0 auto;">
        <h4>Escribe tu comentario</h4>
        <div class="auth-selector">

            <p>
                Para dejar un comentario pulsa en <em>Ver formulario</em>. Si usas
                Facebook puedes identificarte con tu cuenta o
                también puedes publicar un comentario directamente.
            </p>
            <div>
                <a href="#" onclick="showCommentForm(); return false;" class="show-commment-form">Ver formulario »</a>
                {* <a href="" class="facebook-login"><img src="{$params.IMAGE_DIR}utilities/login-facebook.gif.png" /></a> *}
            </div>
        </div>
        <div class="form span-14">
            <form action="" name="comentar" id="comentar" onSubmit="return false;">
                <div id="form-messages" class="span-14"></div>
        
                <div class="static-form span-8">
                    
                    <input type="hidden" id="id" name="id" value="{$content->id}"/>
                    <input type="hidden" id="category" name="category" value="{$content->category}"/>
                    
                    <div>
                        <label for="title">Título:</label><br />
                        <input type="text" tabindex="1" id="title" name="title" />
                    </div>
                    
                    <textarea id="textareacomentario" name="textareacomentario" col="5" tabindex="2" class="span-8"></textarea>
                    
                    {* SPAM detector *}
                    <input type="text" class="hide" id="security_code" name="security_code" value=""/>
                    
                    <input type="submit" tabindex="5" name="Submit" class="submit-button" value="Enviar »"/> o
                    <a href="#" onclick="showCommentForm(); return false;">volver</a>
                </div>
                <div class="variable span-6 last">
                    <div>
                        <label for="name">Nombre:</label><br />
                        <input tabindex="3" type="text" name="nombre" id="nombre" />
                    </div>

                    <div>
                        <label for="mail">Correo electrónico:</label><br />
                        <input tabindex="4" type="text" name="email" id="email" />
                    </div>
                    
                    <hr class="space" />
                    
                    <div>                        
                        <fb:login-button onlogin="commentForm.updateFbStatus();" v="2">
                            <fb:intl>Identificarse con Facebook</fb:intl>
                        </fb:login-button>
                    </div>
                </div>
                <div class="information span-6 last">                    
                    <p>No está permitido verter comentarios contrarios
                    a las leyes españolas o injuriantes.</p>

                    <p>Nos reservamos el derecho a eliminar los
                    comentarios que consideremos fuera de tema.</p>
                </div>
            </form>
            
            <script type="text/javascript">            
            /* <![CDATA[ */                        
            var fbAppKey = '{$smarty.const.FB_APP_APIKEY}';
            var commentForm = null;
            {literal}
            jQuery(document).ready(function() {
                commentForm = jQuery('#comentar .variable').commentform({
                    'form': jQuery('#comentar').get(0),
                    'fbAppKey': fbAppKey
                });
            });
            {/literal}
            /* ]]> */
            </script>
            
        </div>
    </div>
</div>