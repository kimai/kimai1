// defined in kimai_onload();
function skin_onload()
{
}

// defined in init.js and main.js
function lists_resize()
{
}

// show a floater based on the current skin
function floaterShow(phpFile, axAction, axValue, id, width, callback)
{
    $("#kimai_modal .modal-content").load(phpFile,
        {
            axAction: axAction,
            axValue: axValue,
            id: id
        },
        function() {
            $('#focus').focus();
            //$('.extended').hide();

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

            if (callback != undefined) {
                callback();
            }
        }
    );

    $('#kimai_modal').modal({
        show: true
    });
}

function floaterClose()
{
    $('#kimai_modal').modal('hide');
}