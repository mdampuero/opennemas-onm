
{*
    OpenNeMas project
    @theme      Lucidity
*}
<div class="form-comments span-16">
    <div class="span-14 prepend-1 form-comments-content" style="margin:0 auto;">
        <h4>Escribe tu comentario</h4>
        <div class="auth-selector">

            <p>
                Para dejar un comentario debes acceder a XXXX.com. Si usas
                Facebook puedes hacer click en el botón de la derecha o
                también puedes publicar un comentario directamente.
            </p>
            <div>
                <a href="#" onclick="showCommentForm(); return false;" class="show-commment-form">Ver formulario »</a>
                <a href="" class="facebook-login"><img src="images/utilities/login-facebook.gif.png" /></a>
            </div>
        </div>
        <div class="form span-14">
            <form action="" method="POST">
                <div class="static-form span-8">
                    <textarea col="5"  tabindex="1" class="span-8"></textarea>
                    <input type="submit" tabindex="4" name="Submit" class="submit-button" value="Enviar »"/> o
                <a href="#" onclick="showCommentForm(); return false;">volver</a>
                </div>
                <div class="variable span-6 last">
                    <div>
                        <label for="name">Nombre:</label>
                        <input tabindex="2" type="text" name="name" />
                    </div>

                    <div>
                        <label for="mail">Correo electrónico:</label>
                        <input tabindex="3" type="text" name="mail" />
                    </div>
                </div>
                <div class="information span-6 last">
                    <p>No está permitido verter comentarios contrarios
                    a las leyes españolas o injuriantes.</p>

                    <p>Nos reservamos el derecho a eliminar los
                    comentarios que consideremos fuera de tema.</p>
                </div>
            </form>
        </div>
    </div>
</div>