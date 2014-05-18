/**
 * This file is part of
 * Kimai - Open Source Time Tracking // http://www.kimai.org
 * (c) 2006-2009 Kimai-Development-Team
 *
 * Kimai is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; Version 3, 29 June 2007
 *
 * Kimai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimai; If not, see <http://www.gnu.org/licenses/>.
 */
(function( $ ) {

    $.fn.invoice = function( options )
    {
        var opts = $.extend( {}, $.fn.invoice.defaults, options );

        $('#editVatLink').click(function () {
            this.blur();
            floaterShow(opts.path + "floaters.php","editVat",0,0,250);
        });

        $('#invoice_customerID').change(function() {
            $.ajax({
                url: opts.path + 'processor.php',
                data: {
                    'axAction': 'projects',
                    'customerID': $(this).val()
                }
            }).done(function(data) {
                $('#invoice_projectID').empty();
                for(var projectID in data)
                    $('#invoice_projectID').append($('<option>', {
                        value: projectID,
                        text: data[projectID]
                    }));
            });
        });

        $('#invoice_extension_form').on('submit', function() {
            if($('#invoice_projectID').val() == null) {
                alert(opts.noProject);
                return false;
            }
        })

        $.subscribe('resize', function (_, activeTab) {
            if (activeTab == 'ki_invoice') {
                invoice_extension_resize();
            }
        });

        $.subscribe('tabs', function (_, activeTab, tabId) {
            if (activeTab == 'ki_invoice') {
                invoice_extension_resize();
            }
        });

        $.subscribe('timeframe', function(_, timeframe) {
            $.post(opts.path + "processor.php", { axAction: "reload_timespan" },
                function (data) {
                    $("#invoice_timespan").html(data);
                }
            );
        });

        invoice_extension_resize();
        $("#loader").hide();
    };

    function invoice_extension_resize() {
        generic_extension_resize('invoice_extension', 'invoice_extension_header', 'invoice_extension_wrap');
    }

    $.fn.invoice.defaults = {
        noProject: 'No project selected',
        path: '../extensions/ki_invoice/'
    };

}( jQuery ));
