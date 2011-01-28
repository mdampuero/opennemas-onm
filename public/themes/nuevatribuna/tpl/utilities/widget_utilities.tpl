<div class="utilities">
   <ul class="block">
      <li><a href="{$print_url}" class="share-action utilities-print" title="Imprimir"><span>Imprimir</span></a></li>
      <li><a href="{$sendform_url}" class="share-action utilities-send-by-email" rel="facebox" title="Enviar por email a un amigo"><span>Enviar email</span></a></li>
      <li><a href="" class="share-action utilities-increase-text"  onclick="increaseFontSize();return false;" title="Incrementar el tamaño del texto"><span>Ampliar texto</span></a></li>
      <li><a href="" class="share-action utilities-decrease-text"  onclick="decreaseFontSize();return false;" title="Decrementar el tamaño del texto"><span>Reducir texto</span></a></li>
</ul>
</div><!-- /utilities -->

<script type="text/javascript">
   $(function (){
       $('a[rel*=facebox]').facebox();
   });
</script>