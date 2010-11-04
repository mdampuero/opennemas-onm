{*
    OpenNeMas project

    @theme      Lucidity
*}    

<div id="main-search-form">
    <form action="#">
        <input type="text" name="firstname" value="Buscar en la página..."
               onblur="if(this.value=='') this.value='Buscar en la página...';"
               onfocus="if(this.value=='Buscar en la página...') this.value='';" />
        <input type="submit" name="lastname" value="Buscar" />
    </form>
</div>