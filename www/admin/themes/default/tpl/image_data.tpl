{include file="header.tpl"}

<div id="menu-acciones-admin">
    <ul>
        <li>
            <a href="#" class="admin_add" onClick="enviar(this, '_self', 'save_data', '{$photo1->id}');">
                 <img border="0" src="{php}echo($this->image_dir);{/php}save.gif" title="Guardar y salir"  alt="Guardar y salir" ><br />Guardar
            </a>
        </li><li>
            {if !isset($smarty.request.stringSearch)}
                <a href="#" class="admin_add" onClick="enviar(this, '_self','{$smarty.session.desde}', 0);" value="Cancelar" title="Cancelar">
            {else}
                <a href="search_advanced.php?action=search&stringSearch={$smarty.request.stringSearch}&page={$smarty.request.page}" class="admin_add" value="Cancelar" title="Cancelar">
            {/if}
                 <img border="0" src="{php}echo($this->image_dir);{/php}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
            </a>
        </li>
     </ul>
</div>
 
 
    {include file="photo_data.tpl" display='inline'}

<input type="hidden" name="category" value="{$photo1->category}" />

{include file="footer.tpl"}