jQuery(document).ready(function($) {
    $("input[name='with_script']").on('change', function(e, ui) {
        if ($(this).val() == '1') {
            $('#script_content').show();
            $('#normal_content').hide();
            $('#hide_flash').hide();
        } else {
            $('#normal_content').show();
            $('#script_content').hide();
            $('#hide_flash').show();
        }
    });

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('#type_medida').on('change', function(e, ui){
        var selected_option = $("#type_medida option:selected").attr('value');
        if (selected_option=='CLIC') {
            $('#porclic').show();
            $('#porview, #porfecha').hide();
            $('').hide();
        } else if (selected_option == 'VIEW') {
            $('#porview').show();
            $('#porclic, #porfecha').hide();
        } else if (selected_option=='DATE') {
            $('#porfecha').show();
            $('#porclic, #porview').hide();
        } else {
            $('#porclic, #porview, #porfecha').hide();
        }
    });

    var tabs = $('#position-adv').tabs();
    tabs.tabs('select', '{$place}');

    jQuery('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', advertisement_urls.calculate_tags);
    });
});