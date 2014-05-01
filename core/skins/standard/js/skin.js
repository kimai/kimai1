
function lists_resize() {
    lists_set_tableWrapperWidths();
    lists_set_heightTop();
}

function resize_menu() {
    $('#menu').css('width',
        $('#display').position()['left']-$('#menu').position()['left']-20+parseInt($('#display').css('margin-left')));
}

function lists_extensionShrinkShow() {
    $('#extensionShrink').css("background-color","red");
}

function lists_extensionShrinkHide() {
    $('#extensionShrink').css("background-color","transparent");
}

function lists_customerShrinkShow() {
    $('#customersShrink').css("background-color","red");
}

function lists_customerShrinkHide() {
    $('#customersShrink').css("background-color","transparent");
}

function lists_userShrinkShow() {
    $('#usersShrink').css("background-color","red");
}

function lists_userShrinkHide() {
    $('#usersShrink').css("background-color","transparent");
}

function lists_shrinkExtToggle() {
    (extensionShrinkMode)?extensionShrinkMode=0:extensionShrinkMode=1;
    if (extensionShrinkMode) {
        $('#extensionShrink').css("background-image","url('../skins/"+skin+"/grfx/timeSheetShrink_down.png')");
    } else {
        $('#extensionShrink').css("background-image","url('../skins/"+skin+"/grfx/timeSheetShrink_up.png')");
    }
    lists_set_heightTop();
    hook_resize();
}

function lists_shrinkCustomerToggle() {
    (customerShrinkMode)?customerShrinkMode=0:customerShrinkMode=1;
    if (customerShrinkMode) {
        $('#customers, #customers_head, #customers_foot').fadeOut(fading_enabled?"slow":0,lists_set_tableWrapperWidths);
        $('#customersShrink').css("background-image","url('../skins/"+skin+"/grfx/customerShrink_right.png')");
        if (!userShrinkMode)
            $('#usersShrink').hide();
    } else {
        lists_set_tableWrapperWidths();
        $('#customers, #customers_head, #customers_foot').fadeIn(fading_enabled?"slow":0);
        $('#customersShrink').css("background-image","url('../skins/"+skin+"/grfx/customerShrink_left.png')");
        lists_resize();
        if (!userShrinkMode)
            $('#usersShrink').show();
    }
}

function lists_shrinkUserToggle() {
    (userShrinkMode)?userShrinkMode=0:userShrinkMode=1;
    if (userShrinkMode) {
        $('#users, #users_head, #users_foot').fadeOut(fading_enabled?"slow":0,lists_set_tableWrapperWidths);
        $('#usersShrink').css("background-image","url('../skins/"+skin+"/grfx/customerShrink_right.png')");
    } else {
        $('#users, #users_head, #users_foot').fadeIn(fading_enabled?"slow":0);
        lists_set_tableWrapperWidths();
        $('#usersShrink').css("background-image","url('../skins/"+skin+"/grfx/customerShrink_left.png')");
    }
}

function lists_get_dimensions() {
    scroller_width = 17;
    if (navigator.platform.substr(0,3)=='Mac') {
        scroller_width = 16;
    }

    subtableCount=4;

    if (customerShrinkMode) {
        subtableCount--;
    }

    if (userShrinkMode) {
        subtableCount--;
    }

    subtableWidth = (pageWidth()-10)/subtableCount-7;
    userColumnWidth = subtableWidth-5;
    customerColumnWidth = subtableWidth-5; // subtract the space between the panels
    projectColumnWidth = subtableWidth-6;
    activityColumnWidth = subtableWidth-5;
}

function lists_set_tableWrapperWidths() {
    lists_get_dimensions();
    $('#extensionShrink').css("width",pageWidth()-22);
    // set width of faked table heads of subtables -----------------
    $("#users_head, #users_foot").css("width",userColumnWidth-5);
    $("#customers_head, #customers_foot").css("width",customerColumnWidth-5); // subtract the left padding inside the header
    $("#projects_head, #projects_foot").css("width",projectColumnWidth-5); // which is 5px
    $("#activities_head, #activities_foot").css("width",activityColumnWidth-5);
    $("#users").css("width",userColumnWidth);
    $("#customers").css("width",customerColumnWidth);
    $("#projects").css("width",projectColumnWidth);
    $("#activities").css("width",activityColumnWidth);
    lists_set_left();
    lists_set_TableWidths();
}

function lists_set_left() {
    // push project/activity subtables in place LEFT
    leftmargin=0;
    rightmargin=0;
    userShrinkPos=0;
    if (userShrinkMode==0) {
        leftmargin+=subtableWidth;
        rightmargin+=7;
        userShrinkPos+=subtableWidth+7;
    }

    $("#customers, #customers_head, #customers_foot").css("left",leftmargin+rightmargin+10);
    $('#usersShrink').css("left",userShrinkPos);

    customerShrinkPos=userShrinkPos;

    if (customerShrinkMode==0) {
        leftmargin+=subtableWidth;
        rightmargin+=7;
        customerShrinkPos+=subtableWidth+7;
    }

    $("#projects, #projects_head, #projects_foot").css("left",leftmargin+rightmargin+10);
    $("#activities, #activities_head, #activities_foot").css("left",subtableWidth+leftmargin+rightmargin+15); //22
    $('#customersShrink').css("left",customerShrinkPos);
}

function lists_set_heightTop() {
    lists_get_dimensions();
    if (!extensionShrinkMode) {
        $('#gui>div').css("height",pageHeight()-headerHeight()-150-40);
        $("#users,#customers,#projects,#activities").css("height","160px");
        $("#users_foot, #customers_foot, #projects_foot, #activities_foot").css("top",pageHeight()-30);
        $('#usersShrink').css("height","211px");
        $('#customersShrink').css("height","211px");
        // push customer/project/activity subtables in place TOP
        var subs = pageHeight()-headerHeight()-90+25;
        $("#users,#customers,#projects,#activities").css("top",subs);
        // push faked table heads of subtables in place
        var subs = pageHeight()-headerHeight()-90;
        $("#users_head,#customers_head,#projects_head,#activities_head").css("top",subs);
        $('#extensionShrink').css("top",subs-10);
        $('#usersShrink').css("top",subs);
        $('#customersShrink').css("top",subs);
    } else {
        $("#gui>div").css("height","105px");
        $("#users_head,#customers_head,#projects_head,#activities_head").css("top",headerHeight()+107);
        $("#users,#customers,#projects,#activities").css("top",headerHeight()+135);
        $("#users,#customers,#projects,#activities").css("height",pageHeight()-headerHeight()-165);
        $('#customersShrink').css("height",pageHeight()-headerHeight()-110);
        $('#usersShrink').css("height",pageHeight()-headerHeight()-110);
        $('#extensionShrink').css("top",headerHeight()+97);
        $('#customersShrink').css("top",headerHeight()+105);
        $('#usersShrink').css("top",headerHeight()+105);
    }

    lists_set_TableWidths();
}

function lists_set_TableWidths() {
    lists_get_dimensions();
    // set table widths
    ($("#users").innerHeight()-$("#users table").outerHeight()>0)?scr=0:scr=scroller_width; // same goes for subtables ....
    $("#users table").css("width",userColumnWidth-scr);
    ($("#customers").innerHeight()-$("#customers table").outerHeight()>0)?scr=0:scr=scroller_width; // same goes for subtables ....
    $("#customers table").css("width",customerColumnWidth-scr);
    ($("#projects").innerHeight()-$("#projects table").outerHeight()>0)?scr=0:scr=scroller_width;
    $("#projects table").css("width",projectColumnWidth-scr);
    ($("#activities").innerHeight()-$("#activities table").outerHeight()>0)?scr=0:scr=scroller_width;
    $("#activities table").css("width",activityColumnWidth-scr);
}

// ----------------------------------------------------------------------------------------
// shows floating dialog windows based on processor data
function floaterShow(phpFile, axAction, axValue, id, width, callback)
{
    if ($('#floater').css("display") == "block") {
        $("#floater").fadeOut(fading_enabled?500:0, function() {
            floaterLoadContent(phpFile, axAction, axValue, id, width, callback);
        });
    } else {
        floaterLoadContent(phpFile, axAction, axValue, id, width, callback);
    }
}

// load floater content
function floaterLoadContent(phpFile, axAction, axValue, id, width, callback)
{
    $("#floater").load(phpFile,
        {
            axAction: axAction,
            axValue: axValue,
            id: id
        },
        function() {

            $('#floater').css({width: width+"px"});

            resize_floater();

            x = ($(document).width()-(width+10))/2;
            if (x<0) x=0;
            $("#floater").css({left:x+"px"});
            $("#floater").fadeIn(fading_enabled?200:0);

            $('#focus').focus();
            $('.extended').hide();
            $('#floater_content').css("height",$('#floater_dimensions').outerHeight()+5);

            // toggle class of the proberbly existing extended options button
            $(".options").toggle(function(){
                el = $(this);
                el.addClass("up");
                el.removeClass("down");
                return false;
            },function(){
                el = $(this);
                el.addClass("down");
                el.removeClass("up");
                return false;
            });

            if (callback != undefined)
                callback();

        }
    );
}

// resize floater based on window size
function resize_floater()
{
    var height = $(window).height();
    height -= $('#floater').outerHeight() - $('#floater').height(); // floater border and padding
    height -= $('#floater_tabs').outerHeight() - $('#floater_tabs').height(); // floaterTab border and padding
    height -= $('#floater_handle').outerHeight(true) + $('.menuBackground').outerHeight(true) + $('#formbuttons').outerHeight(true); // other elements heights
    $('#floater_tabs').css({'max-height': height + "px"});

    var y = ($(window).height() - $('#floater').height()) / 2;
    if (y<0) y=0;
    $("#floater").css({top:y+"px"});

}

// hides dialog again
function floaterClose()
{
    //$('#floater').draggable('destroy');
    $("#floater").fadeOut(fading_enabled?500:0);
}
