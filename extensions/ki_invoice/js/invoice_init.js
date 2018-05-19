/**
 * This file is part of
 * Kimai - Open Source Time Tracking // https://www.kimai.org
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

// ======================
// Invoice Extension init
// ======================

// set path of extension
var invoice_extension_path = "../extensions/ki_invoice/";

function invoice_extension_onload() {
    invoice_extension_resize();
    $("#loader").hide();
}

function invoice_extension_resize() {
    scroller_width = 17;
    if (navigator.platform.substr(0, 3) == 'Mac') {
        scroller_width = 16;
    }
    pagew = pageWidth() - 15;

    var $invoice_extension_header = $("#invoice_extension_header");
    $invoice_extension_header.css("width", pagew - 27);
    $invoice_extension_header.css("top", headerHeight());
    $invoice_extension_header.css("left", 10);

    var $invoice_extension_wrap = $("#invoice_extension_wrap");
    $invoice_extension_wrap.css("top", headerHeight() + 30);
    $invoice_extension_wrap.css("left", 10);
    $invoice_extension_wrap.css("width", pagew - 7);
    $("#invoice_extension").css("height", pageHeight() - headerHeight() - 64);
}

function invoice_extension_tab_changed() {
    invoice_extension_resize();
}
