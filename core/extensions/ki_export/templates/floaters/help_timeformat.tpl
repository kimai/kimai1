{literal}    
    <script type="text/javascript"> 
        
        $(document).ready(function() {
            // $('#help').hide();

            $('#xp_ext_form_export_PDF').ajaxForm(function() { 
                
                // $edit_in_time = $('#edit_in_day').val()+$('#edit_in_time').val();
                // $edit_out_time = $('#edit_out_day').val()+$('#edit_out_time').val();
                
				// floaterClose();
				// xp_ext_reload();
                
            });

        }); 
        
    </script>
{/literal}


<div id="floater_innerwrap">

    <div id="floater_handle">
        <span id="floater_title">Help Timeformat</span>
        <div class="right">
            <a href="#" class="close" onClick="floaterClose();">{$kga.lang.close}</a>
            <!-- <a href="#" class="help" onClick="$(this).blur(); $('#help').slideToggle();">{$kga.lang.help}</a> -->
			<!-- <a href="#" class="options down" onClick="floaterOptions();">{$kga.lang.options}</a> -->
        </div>  
    </div>

    <div id="floater_content"><div id="floater_dimensions" style="height:400px;overflow:auto">
	
	
			<ul><li><p>
			     %a - abbreviated weekday name according to the current locale
			    </p></li><li><p>
			     %A - full weekday name according to the current locale
			    </p></li><li><p>
			     %b - abbreviated month name according to the current locale
			    </p></li><li><p>
			     %B - full month name according to the current locale
			    </p></li><li><p>
			     %c - preferred date and time representation for the current locale
			    </p></li><li><p>
			     %C - century number (the year divided by 100 and truncated to an integer, range 00 to 99)
			    </p></li><li><p>
			     %d - day of the month as a decimal number (range 01 to 31)
			    </p></li><li><p>
			     %D - same as %m/%d/%y
			    </p></li><li><p>
			     %e - day of the month as a decimal number, a single digit is preceded by a space (range 1
			     to 31)
			    </p></li><li><p>
			     %g - Week-based year within century [00,99]
			    </p></li><li><p>
			     %G - Week-based year, including the century [0000,9999]
			    </p></li><li><p>
			     %h - same as %b
			    </p></li><li><p>
			     %H - hour as a decimal number using a 24-hour clock (range 00 to 23)
			    </p></li><li><p>
			     %I - hour as a decimal number using a 12-hour clock (range 01 to 12)
			    </p></li><li><p>
			     %j - day of the year as a decimal number (range 001 to 366)
			    </p></li><li><p>
			     %k - Hour (24-hour clock) single digits are preceded by a blank. (range 0 to 23)
			    </p></li><li><p>
			     %l - hour as a decimal number using a 12-hour clock, single digits preceeded by a space
			     (range 1 to 12)
			    </p></li><li><p>
			     %m - month as a decimal number (range 01 to 12)
			    </p></li><li><p>
			     %M - minute as a decimal number
			    </p></li><li><p>
			     %n - newline character
			    </p></li><li><p>
			     %p - either `am' or `pm' according to the given time value, or the corresponding strings
			     for the
			     current locale
			    </p></li><li><p>
			     %r - time in a.m. and p.m. notation
			    </p></li><li><p>
			     %R - time in 24 hour notation
			    </p></li><li><p>
			     %S - second as a decimal number
			    </p></li><li><p>
			     %t - tab character
			    </p></li><li><p>
			     %T - current time, equal to %H:%M:%S
			    </p></li><li><p>
			     %u - weekday as a decimal number [1,7], with 1 representing Monday
			    </p></li><li><p>
			     %U - week number of the current year as a decimal number, starting with the first Sunday
			     as the first
			     day of the first week
			    </p></li><li><p>
			     %V - The ISO 8601:1988 week number of the current year as a decimal number, range 01 to
			     53, where week
			     1 is the first week that has at least 4 days in the current
			     year, and with Monday as the first day of the week.
			    </p></li><li><p>
			     %w - day of the week as a decimal, Sunday being 0
			    </p></li><li><p>
			     %W - week number of the current year as a decimal number, starting with the first Monday
			     as the first
			     day of the first week
			    </p></li><li><p>
			     %x - preferred date representation for the current locale without the time
			    </p></li><li><p>
			     %X - preferred time representation for the current locale without the date
			    </p></li><li><p>
			     %y - year as a decimal number without a century (range 00 to 99)
			    </p></li><li><p>
			     %Y - year as a decimal number including the century
			    </p></li><li><p>
			     %Z - time zone or name or abbreviation
			    </p></li><li><p>
			     %% - a literal `%' character
			    </p></li></ul>
    </div>


</div>