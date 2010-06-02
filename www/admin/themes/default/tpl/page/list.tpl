{toolbar_route toolbar="toolbar-top"
    icon="new" route="page-create" text="New Page"}
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Page Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<table class="adminheading">
    <tbody>
        <tr>
            <th>{t}Pages{/t}</th>
        </tr>
    </tbody>
</table>

<div>
    {$list}
</div>