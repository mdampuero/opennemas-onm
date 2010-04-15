        </td>
    </tr>
    </table>
    
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" id="id"     name="id"     value="{$id}" />
    
    <input type="hidden" name="pk_author" value="{$smarty.session.userid}" />
    <input type="hidden" name="fk_publisher" value="{$smarty.session.userid}" />
    <input type="hidden" name="fk_user_last_editor" value="{$smarty.session.userid}" />
</form>
</td></tr>
</table>

{if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
	<script language="javascript" type="text/javascript">
	// <![CDATA[ {literal}
    new Validation('formulario', {immediate : true});    

    // Para activar los separadores/tabs
    $fabtabs = new Fabtabs('tabs');		
    // ]]> {/literal}
	</script>
{/if}

</body>
</html>
