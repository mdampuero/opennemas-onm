 <div id="comments" style="width:98%">
    <table>
        <tbody>
            <tr>
                <th class="title" style='width:50%'>Comentario</th>
                <th class="title"  style='width:20%'>Autor</th>
                <th class="right">Publicar</th>
                <th class="right">Eliminar</th>
            </tr>
            {section name=c loop=$comments}
            <tr>
                <td>
                    <a style="cursor:pointer;font-size:14px;"
                        onclick="new Effect.toggle($('{$comments[c]->pk_comment}'),'blind')">
                        {$comments[c]->body|truncate:30}
                    </a>
                </td>
                <td>
                    {$comments[c]->author} ({$comments[c]->ip})
                    <br />
                    {$comments[c]->email}
                </td>
                <td class="right">
                </td>
                <td class="right">
                    <a href="#" onClick="javascript:confirmarDelComment(this, '{$comments[c]->pk_comment}');" title="Eliminar">
                        <img src="{$params.IMAGE_DIR}trash.png"  />
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="{$comments[c]->pk_comment}" class="{$comments[c]->pk_comment}" style="display: none;">
                        <strong>Comentario:</strong> (IP: {$comments[c]->ip} - Publicado: {$comments[c]->changed})
                        <br/> {$comments[c]->body}
                    </div>
                </td>
            </tr>
            {/section}
        </tbody>
    </table>
</div>