/*
* jquery Datepicker call
*/
jQuery(function() {
    jQuery('#starttime').datetimepicker({
        hourGrid: 4,
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
	minuteGrid: 10,
        onClose: function(dateText, inst) {
            var endDateTextBox = jQuery('#endtime');
            if (endDateTextBox.val() != '') {
                var testStartDate = new Date(dateText);
                var testEndDate = new Date(endDateTextBox.val());
                if (testStartDate > testEndDate)
                    endDateTextBox.val(dateText);
            }
            else {
                endDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime){
            var start = jQuery(this).datetimepicker('getDate');
            jQuery('#endtime').datetimepicker('option', 'minDate', new Date(start.getTime()));
        }
    });
    jQuery('#endtime').datetimepicker({
        hourGrid: 4,
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
	minuteGrid: 10,
        onClose: function(dateText, inst) {
        var startDateTextBox = jQuery('#starttime');
        if (startDateTextBox.val() != '') {
            var testStartDate = new Date(startDateTextBox.val());
            var testEndDate = new Date(dateText);
            if (testStartDate > testEndDate)
                startDateTextBox.val(dateText);
            }
            else {
                startDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime){
            var end = jQuery(this).datetimepicker('getDate');
            jQuery('#starttime').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
        }
    });
    jQuery('#date').datepicker({
        showAnim: "fadeIn",
        dateFormat: 'yy-mm-dd'
    });

    jQuery('#ui-datepicker-div').css('clip', 'auto');

});

