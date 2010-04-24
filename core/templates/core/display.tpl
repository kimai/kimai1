{literal}
<script type="text/javascript" charset="utf-8">
    $(function()
    {
        $('.date-pick').datePicker(

          {
                createButton:false,
                createButton:false,
                startDate:'{/literal}{$dp_start}{literal}',
                endDate:'{/literal}{$dp_today}{literal}'
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

                    switch (pickerClicked) {

                        case 'pick_in':
                            fromDay   = selectedDate.getDate();
                            fromMonth = selectedDate.getMonth()+1;
                            fromYear  = selectedDate.getFullYear();
                            setTimespace(fromDay,fromMonth,fromYear,0,0,0);
                        break;

                        case 'pick_out':
                            toDay     = selectedDate.getDate();
                            toMonth   = selectedDate.getMonth()+1;
                            toYear    = selectedDate.getFullYear();
                            setTimespace(0,0,0,toDay,toMonth,toYear);
                        break;
                    }
                    this.blur();
                }
        );

       
        $('#pick_in').dpSetSelected('{/literal}{$timespace_in|date_format:'%d/%m/%Y'}{literal}');
        $('#pick_out').dpSetSelected('{/literal}{$timespace_out|date_format:'%d/%m/%Y'}{literal}');        
        
        switch ({/literal}{$timespace_warning}{literal}) {
            case 0: 
                $('#ts_in').removeClass('datewarning');  // in_ok
                $('#ts_out').removeClass('datewarning'); // out_ok
        break;
            case 1:
                $('#ts_in').removeClass('datewarning');  // in_ok
                $('#ts_out').addClass('datewarning');    // out_bad
        break;
            case 2:
                $('#ts_in').addClass('datewarning');     // in_bad
                $('#ts_out').removeClass('datewarning'); // out_ok
        break;
            case 3:
                $('#ts_in').addClass('datewarning');     // in_bad
                $('#ts_out').addClass('datewarning');    // out_bad
        break;
        }
        
        {/literal}{if $hook_tss_inDisplay}hook_tss();{/if}{literal}
             
    });
</script>
{/literal}


<div id="dates">
    <a href="#" id="pick_in" class="date-pick" title="{$kga.lang.in}"><span id="ts_in">{$timespace_in|date_format:$kga.date_format.2}</span></a> - 
    <a href="#" id="pick_out" class="date-pick" title="{$kga.lang.out}"><span id="ts_out">{$timespace_out|date_format:$kga.date_format.2}</span></a>
</div>


<div id="infos">
    {$today_display} &nbsp; 
    <img src="../skins/{$kga.conf.skin}/grfx/g3_display_smallclock.png" width="13" height="13" alt="Display Smallclock" />
    <span id="n_uhr">00:00</span> &nbsp; 
    <img src="../skins/{$kga.conf.skin}/grfx/g3_display_eye.png" width="15" height="12" alt="Display Eye" /> 
    <strong id="display_total">{$total}</strong> 
</div>
