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
                            setTimespace(selectedDate,undefined);
                        break;

                        case 'pick_out':
                            setTimespace(undefined,selectedDate);
                        break;
                    }
                    this.blur();
                }
        );

       
        $('#pick_in').dpSetSelected('{/literal}{$timespace_in|date_format:'%d/%m/%Y'}{literal}');
        $('#pick_out').dpSetSelected('{/literal}{$timespace_out|date_format:'%d/%m/%Y'}{literal}');        
        
        setTimespaceStart(new Date({/literal}{$timespace_in*1000}{literal}));
        setTimespaceEnd(new Date({/literal}{$timespace_out*1000}{literal}));
        updateTimespaceWarning();
             
    });
</script>
{/literal}


<div id="dates">
    <a href="#" id="pick_in" class="date-pick" title="{$kga.lang.in}"><span id="ts_in"></span></a> - 
    <a href="#" id="pick_out" class="date-pick" title="{$kga.lang.out}"><span id="ts_out"></span></a>
</div>


<div id="infos">
    {$today_display} &nbsp; 
    <img src="../skins/{$kga.conf.skin}/grfx/g3_display_smallclock.png" width="13" height="13" alt="Display Smallclock" />
    <span id="n_uhr">00:00</span> &nbsp; 
    <img src="../skins/{$kga.conf.skin}/grfx/g3_display_eye.png" width="15" height="12" alt="Display Eye" /> 
    <strong id="display_total">{$total}</strong> 
</div>
