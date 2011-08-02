<table class="adminform">
    <tr {cycle values="class=row0,class=row1"} style="padding:20xp;">
        <td>
            <div style="margin:0 auto; width:50%">
                <h3>{t}We cant find any content with your search criteria.{/t}</h3>
                <p>{t escape="no" 1=$smarty.request.stringSearch|clearslash}Your search "<b>%1</b>" didn't return any element.{/t}</p>
                <p style="margin-top: 1em;">{t}Suggestions:{/t}</p>
                <ul>
                    <li>{t}Check if all the words are written correctly.{/t}</li>
                    <li>{t}Use other words.{/t}</li>
                    <li>{t}Use more general search criteria.{/t}</li>
                </ul>
            </div>
        </td>
    </tr>
</table>
