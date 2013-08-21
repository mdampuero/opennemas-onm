jQuery(document).ready(function($) {
    $("input[name='with_script']").on('change', function(e, ui) {
        if ($(this).val() == '1') {
            $('#script_content').show();
            $('#normal_content').hide();
            $('#hide_flash').hide();
            $('#div_url1').hide();
            $('#url').removeAttr('required');
        } else {
            $('#normal_content').show();
            $('#script_content').hide();
            $('#hide_flash').show();
            $('#div_url1').show();
            $('#url').attr('required', 'required');
        }
    });

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('#type_medida').on('change', function(e, ui){
        var selected_option = $("#type_medida option:selected").attr('value');
        if (selected_option == 'DATE') {
            $('#porfecha').show();
        } else {
            $('#porfecha').hide();
        }
    });

    var tabs = $('#position-adv').tabs();
    tabs.tabs('select', '{$place}');

    jQuery('#title').on('change', function(e, ui) {
        fill_tags(jQuery('#title').val(),'#metadata', advertisement_urls.calculate_tags);
    });
});