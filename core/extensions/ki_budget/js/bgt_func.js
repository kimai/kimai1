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

// =============
// BGT EXT funcs
// =============

function bgt_ext_onload() {
    bgt_ext_resize();
    $("#loader").hide();
    lists_visible(false);
}

function bgt_ext_get_dimensions() {
    bgt_h = pageHeight()-224;//-headerHeight()-28;
}

function bgt_ext_resize() {
    bgt_ext_get_dimensions();
    $("#bgt").css("height",bgt_h);
}

function bgt_ext_plot(plotdata) {
    for (var i in plotdata) {
        if ($('#bgt_chartdiv_'+i).length == 0) continue;
        try {
          $.jqplot('bgt_chartdiv_'+i,  [plotdata[i]], {              
              seriesDefaults:{renderer:$.jqplot.PieRenderer,
                  rendererOptions: {padding:10}
              },
              seriesColors:chartColors,
              grid:{background:$('#bgt_chartdiv_'+i).css("background-color"), borderWidth:0, shadow:false}
          });
        }
        catch (err) {
        }
    }
}