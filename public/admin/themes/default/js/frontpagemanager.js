$(document).ready(function(){
            
    // Make content providers sortable and allow to D&D over the placeholders
    jQuery('div.content-provider-block').sortable({
        connectWith: "div.placeholder div.content",
        placeholder: 'placeholder-element'
        //containment: '#content-with-ticker'
    }).disableSelection();
    
    // Make content providers sortable and allow to D&D over placeholders and content provider
    jQuery('div.placeholder div.content').sortable({
        connectWith: "div.content-provider-block, div.placeholder div.content",
        placeholder: 'placeholder-element'
        //containment: '#content-with-ticker'
    }).disableSelection();

    // Toggle content-provider-element checkbox if all the content-provider-elemnt is clicked
    jQuery('div.placeholder div.content-provider-element').click(function() {
       checkbox = $(this).find('input[type="checkbox"]');
       checkbox.attr(
           'checked', 
           !checkbox.is(':checked')
       ); 
    });
    
    
    // When get_ids button is clicked get all the contents inside any placeholder
    // and build some js objects with information about them
    jQuery('#get_ids').click(function() {
        
        var els = [];
        $('div.placeholder').each(function (){
            var placeholder = $(this).data('placeholder');
            $(this).find('div.content-provider-element').each(function (index){
                els.push({
                    'class': $(this).data('class'),
                    'id':$(this).data('id'),
                    'placeholder': placeholder,
                    'params': {}
                });
            });
            
        });

        //console.log(els);
        console.log(JSON.stringify(els))
        
        return false; 
    });
    
    
    $('.selectButton, .edit-button, .settings-button, .home-button, .delete-button').click(function (){
        alert('Not implemented yet.');
        return false;
    });
    
});