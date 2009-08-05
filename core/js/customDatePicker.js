// --------------------------
// DatePicker for edit window
// --------------------------

function init_epick() {

    $('.date-epick').datePicker(

        {
            createButton:false,
            createButton:false,
            startDate:'01/01/2005',
            endDate:'31/12/2008'
        }

    ).bind(
            'click',
            function() {
                $(this).dpDisplay();
                pickerClicked = $(this).attr('id');
                this.blur();
                return false;
            }

    ).bind(
            'dateSelected',
            function(e, selectedDate, $td) {

                value = selectedDate.getDate();
                if (value < 10) {
                    value = "0" + value;
                }
                value += ".";
                if ( (selectedDate.getMonth()+1) < 10) {
                    value += "0";
                }
                value += (selectedDate.getMonth()+1) + ".";
                value += selectedDate.getFullYear();
                
                switch (pickerClicked) {

                    case 'epick_in':
                        $("#edit_in").val(value);
                    break;

                    case 'epick_out':
                        $("#edit_out").val(value);
                    break;
                }
            }
    );
        value = $('#edit_in').val();
        $('#epick_in').dpSetSelected(value);
        value = $('#edit_out').val();
        $('#epick_out').dpSetSelected(value);
}
