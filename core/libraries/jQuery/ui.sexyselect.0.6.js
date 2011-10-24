/*
* jQuery UI Sexy Select
*
* Authors:
*  Nico VanHaaster
* 
* Liscenced under the MIT (MIT-LICENSE.txt)
* and GPL (GPL-LICENSE.txt) licenses.
* 
*
* Requirments
* jQuery 1.4.2  
* jQuery Ui 1.8.4
* 
* Notes
* When autoSort = true the drag drop sorting features are not available.
* If using the stylize options and the style sheet name is changed you must change the styleizeName below. Note by default the style sheet name is ui.sexyselect.x.x.css
* and the check looks for ui-sexyselect to maintain compatability with newer versions.
*
* usage $('select').sexyselect();
*
*
* Setup Notes
* If you have included the style pack you can either call it individually by  $('select').sexyselect({ styleize: true }); or set the options flag below triStateRadio: true,
*/


//$.expr[':'].notcontains = function (elem, index, meta) {
//    return new RegExp(meta[3], 'i').test($(elem).text());
//}
String.prototype.trim = function () {
    return this.replace(/^\s*/, "").replace(/\s*$/, "");
}

;(function ($) {
    $.widget("ui.sexyselect", {
         el : { s:'<span />', d:'<div />', li : '<li />', i : '<img />', u: '<ul />' , p :'<input />', o: '<option />' }, //element definitions. do not change
         revert : false,
         styleizeName :'ui.sexyselect', 
         filterTimer: null, //timer for the filtering. default should be null
         createEventArgs: {
             create : true,
             errorText : '',
             timeOut : 2500
         },
         options: { 
            styleize: false, //allow for styling the radio buttons using the sexyselect css files v0.5 and above
            confirmDelete: { //confirm for removal of items.
                modal : true, //show in a modal dialog box. jQuery UI Dialog
                text : "Are you sure you would like to remove option '{0}' ?", //default delete text
                confirm : false, //toggle confirm or not
                callBack : null //call back on answer selected
            },
            connectedList: {
                twoWay: false, //true: allows two way binding, false indicates one way binding
                connection: null //id of the other sexyselect box.
            },
            trigger: null,
            filterKeyUpTimeout: 500, //timeout for how long after the last key up before searching. Setting this to 0 will speed up the filter rate, but use more resources in the browser.
            triStateRadio : false, // Allows for tri-state radio buttons (and radioboxes) this is only avaiable in selection Mode = single
            allowFilter: false,
            onItemSelected: null, //event for onitem selected
            allowDebug: false, //used for debugging purposes
            nooptionstext : 'no options added...', //no opptions text, will be shown if no options have been added.
            onitemcreating: null, //event called when the items are being created. return true to continue or return false to cancel 
            expanded: true,
            width : 200, //width of the outside container
            height : 200, //total height of the container
            background : 'transparent', //background of the container default: transparent
            title : 'Multi-Select', //title bar text
            showTitle : true, //show the title bar
            allowInput : false, //allow the input bar to create new options
            addValueMaxLength : 50, //maxValue for the input bar
            defaultInputText : 'add new option', //default watermark for the inputText blank for none
            autoSort : false, //auto sort the list? false will allow for drag & drop sorting
            minSize : 0, //minimum size : used for validation. If you set the value to 2 and use the method $(elem).sexyselect('validateSize') will return true or false against minimum requirements
            onitemcreated : null, //event fired when an Item is created (new items only)
            autoExpand : false, //not functioning
            onItemsSync : null, //event fired when the Items are Synronized
            allowDelete : true, //allow items to be removed.
            allowAdditionalOption : false, //allow additional option.
            allowCollapse : true,
            text: {
                allowWrapAround : false,
                checkBoxPosition: 'left', //allow for check box on left & right
                textSize: null,
                textAlign: null
            },
            onItemDeleted : null,
            selectionMode : 'none' //type of selection required : this function will be overriden if the select has the attr multiple added to it.
                                   //                             none = no selection
                                   //                             single = radio button list
                                   //                             multiple = checkbox
        },
        _init: function(){
            //Checks for styleize stylesheet. 
            if(this.options.styleize)
            {
            	// kimai uses @import syntax, so do not check this way, it will always fail!
//                if($("link[href*='" + this.styleizeName + "']").size() == 0)
//                {
//                    if(this.options.allowDebug)
//                        alert('Missing Sexy Select UI Style Sheet, or you have renamed the file.\r\n\r\nStyle features removed, replace the file or modfy the styleizeName in the script');
//                    this.options.styleize = false;
//                }
            }

            if($.browser.msie)
                this.revert = false;
            else
                this.revert = true;

            var self = this, 
                initElem = this.element,
                options = this.options;

            if(!options.allowDebug)
            {
                initElem.hide();
                initElem.css({'position':'absolute','z-index':'-10','left' : '-3000000','top':'-30000'});
            }
            if(initElem.attr('multiple') == true && options.selectionMode == 'none')
                options.selectionMode = 'multiple';
            if(options.selectionMode == 'multiple')
                initElem.attr('multiple','multiple');
            else if(options.selectionMode == 'single')
                initElem.removeAttr('multiple');

//            if(options.height < 150)
//                options.height = 150;
//            if(options.width < 100)
//                options.width = 100;

            this.sselect = $(self.el.d)
                           .css({'height': ((!options.expanded && options.allowCollapse) ? 28 : (!options.allowInput ? 28 - options.height : options.height)) + 'px', 'width' : options.width + 'px', 'background' : options.background, 'padding' : '0', 'margin' : '0','overflow' : 'hidden', 'position':'relative'})
                           .attr('id',initElem.attr('id') + '_ssWrapper')
                           .addClass('ui-sexyselect-wrapper')
                           .addClass('ui-widget')
                           .addClass('ui-widget-content')
                           .addClass('ui-corner-all');
            if(options.showTitle)
            {
                this.header = $(self.el.d)
                            .css({ 'height' : '22px', 'line-height' : '22px', 'padding' : '0 0 0 4px', 'margin':'2px' })
                            .addClass('ui-widget-header')
                            .addClass('ui-sexyselect-header')
                            .addClass('ui-corner-all')
                            .attr('id',initElem.attr('id') + '_header');
                if(options.allowCollapse)
                {
                    this.header.click(function(){
                                        self._expand();
                                    });
                    this.header.append($(self.el.s)
                                       .css({'width' : options.width - 40 + 'px', 'display':'inline-block','overflow':'hidden'})
                                       .html(options.title)
                                       .addClass('ui-sexyselect-header-text')
                                       );
                    this.header.append($(self.el.s)
                                       .addClass('ui-icon')
                                       .addClass((options.expanded ? 'ui-icon-circle-triangle-s' : 'ui-icon-circle-triangle-n'))
                                       .addClass('ui-sexyselect-header-expand-icon')
                                       .css({'margin-top' : '2px' ,'cursor':'pointer', 'display' : 'inline-block'})
                                       .attr('id',initElem.attr('id') + '_icon')
                                       .attr('ex',(options.expanded ? '1' : '0'))
                                       );
                   
                }
                else
                    this.header.html(options.title);
                this.sselect.append(this.header);
            }
            if(options.allowInput)
            {
                this.input = $(self.el.d)
                             .css({'border-bottom' : 'solid 1px #f1f1f1', 'font-size' : '0.8em' , 'color' : '#666','height' : '23px', 'line-height' : '23px'})
                             .addClass('ui-sexyselect-input-wrapper'),
                
                    inputBox = $(self.el.p)
                               .attr('type','text')
                               .attr('id',initElem.attr('id') + '_addText')
                               .val(options.defaultInputText)
                               .addClass('ui-widget-content')
                               .addClass('ui-sexyselect-input-box')
                               .attr('maxlength',options.addValueMaxLength)
                               .css({'color' : '#999', 
                                     'width' : options.width - 65 + 'px' ,
                                     'padding-left' : '5px',
                                     'font-style' : 'italic',
                                     'font-weight' : 'normal',
                                     '-moz-border-radius': '4px', 
                                     'display':'inline-block',
                                     '-webkit-border-radius': '4px',
                                     'border-radius' : '4px' })
                               .attr('wmt',options.defaultInputText)
                               .hover(
                                    function(){
                                        $(this).addClass('ui-state-hover');
                                    },
                                    function() {
                                       $(this).removeClass('ui-state-hover');
                                    }
                               )
                               .focus(function()
                               {
                                    x = $(this);
                                    var t = x.val();
                                    if(t == x.attr('wmt')) {
                                        x.val('').addClass('ui-state-active').css({'color' : '#666', 'font-style' : 'normal' });
                                    }
                               })
                               .blur(function()
                               {
                                    x = $(this);
                                    x.removeClass('ui-state-active');
                                    var t = x.val();
                                    if(t == '')
                                    {
                                        x.css({'color' : '#999','font-style' : 'italic'}).val(x.attr('wmt'));
                                        
                                    }
                               }),
                    inputBtn = $(self.el.p)
                               .attr('type','button')
                               .attr('id' ,initElem.attr('id') + '_addBtn')
                               .addClass('ui-widget-content')
                               .addClass('ui-corner-all')
                               .addClass('ui-sexyselect-input-btn')
                               .val('add')
                               .hover(
                                    function() 
                                    {
                                        $(this).removeClass('ui-state-highlight').addClass('ui-state-active').css('font-weight','normal');
                                    },
                                    function()
                                    {
                                        $(this).removeClass('ui-state-highlight').removeClass('ui-state-active').css('font-weight','normal');
                                    }
                               )
                               .mousedown(function()
                               {
                                    $(this).removeClass('ui-state-active').addClass('ui-state-highlight').css('font-weight','normal');
                               })
                               .mouseup(function()
                               {
                                    $(this).removeClass('ui-state-highlight').removeClass('ui-state-active').css('font-weight','normal');
                               })
                               .click(function(){
                                    var x = $(this).parent().children('input[type=text]'),
                                        t = x.val().trim(),
                                        complete = true;
                                    self._resetCreateEventArgs();
                                    
                                    if($.isFunction(self.options.onitemcreating))
                                    {
                                        var result = self.options.onitemcreating(x, self.createEventArgs);
                                        if(result !== undefined) self.createEventArgs = result;
                                    }
                                    if(self.createEventArgs.create)
                                        self._addItem(t, x);
                                    else if(!self.createEventArgs.create && self.createEventArgs.errorText != '')
                                        self.showError(self.createEventArgs.errorText, self.createEventArgs.timeOut);
                               }),
                    inputError = $('<div />')
                                 .attr('id',initElem.attr('id') + '_addText_e')
                                 .addClass('ui-state-error')
                                 .addClass('ui-corner-bottom')
                                 .addClass('ui-sexyselect-error')
                                 .css({'display':'none' , 'padding' :'5px', 'position' : 'relative', 'z-index' :'10000', 'margin' : '0', 'cursor':'default' })
                                 .html('');
                this.input.append(inputBox);
                this.input.append(inputBtn);
                this.input.append(inputError);
                this.sselect.append(this.input);
            }
            this.itemHolder = $(self.el.d)
                            .css({'height': options.height - (options.showTitle ? 53 : 22) + 'px', 'width' : options.width , 'overflow' : 'hidden','overflow-y' : 'auto', 'margin' : '0px' , 'padding' : '0px'})
                            .attr('id',initElem.attr('id') + '_items')
                            .addClass('ui-sexyselect-item-holder')
                            .append($(self.el.u)
                                    .addClass('ui-sexyselect-listholder')
                                    .attr('id',initElem.attr('id') + '_listHolder')
                                    .css({'list-style-type' : 'none' , 'padding' : '0' , 'margin' : '0' }));
                this.sselect.append(this.itemHolder);

            if(self.options.allowFilter)
                this.sselect.append(this._createFilter());
            initElem.after(this.sselect);
            this._syncItems();
        },
        _createFilter: function()
        {
            var self = this;
            var filterWrapper = $(self.el.d)
                                    .addClass('ui-sexyselect-filter-wrapper')
                                    .css({ 'position':'absolute', 'bottom':'-15px', 'height':'15px', 'font-weight':'normal','cursor':'pointer','z-index':'1000', 'margin-left':'1px','width':self.options.width-18 + 'px' })
                                    .html(''),

                filterHeader = $(self.el.d)
                                    .addClass('ui-corner-top')
                                    .addClass('ui-widget-header')
                                    .css('font-weight','normal')
                                    .attr('title','Filter')
                                    .css('font-size','0.9em')
                                    .css({'width' : '18px','padding':'0px 5px','float':'right','position':'relative','margin-top':'-15px','margin-right':'15px','opacity':'0.7'})
                                    .addClass('ui-sexyselect-filter-header')
                                    .hover(
                                        function(){ 
                                            $(this).animate({opacity:1},100,function() {});
                                        }, //in,
                                        function() {
                                            var par = $(this).parent();
                                            var fBar = par.find('div.ui-sexyselect-filter-bar');
                                            if(fBar.height() ==0 )
                                                $(this).animate({opacity:0.7},100,function() {});
                                        } //out
                                    )
                                    .click(function()
                                    {
                                        var par = $(this).parent();
                                        var fBar = par.find('div.ui-sexyselect-filter-bar');
                                        if(fBar.height() !=0 )
                                        {
                                            par.animate({
                                                bottom: '-=27'
                                            },100,function() {});
                                            fBar.animate({
                                                height: '-=25'
                                            },100,function() {})
                                            $(this).animate({opacity:0.7},100,function() {});
                                        }
                                        else
                                        {
                                            par.animate({
                                                bottom: '+=27'
                                            },100,function() {});
                                            fBar.animate({
                                                height: '+=25'
                                            },100,function() {})
                                        }
                                    }),

                    filterHeaderClear = $(self.el.d).css({'clear':'both','height':'0px'}),
                    filterWrapText = $(self.el.s)
                                        .addClass('ui-sexyselect-filter-header-text')
                                        .text('filter')
                                        .css('font-size','0.9em'),
                                    
                    filterWrapImg = $(self.el.s)
                                    .addClass('ui-icon')
                                    .addClass('ui-icon-search')
                                    .addClass('ui-sexyselect-filter-header-image')
                                    .text(' ')
                                    .attr('title','Filter')
                                    .attr('align','left')
                                    .css({'display':'inline-block','height':'14px','width':'14px'});
                filterHeader.append(filterWrapImg);
                filterWrapper.append(filterHeader);
                filterWrapper.append(filterHeaderClear);
                var filterBar = $(self.el.d)
                                .addClass('ui-sexyselect-filter-bar')
                                .addClass('ui-state-focus')
                                .addClass('ui-corner-top')
                                .css({'height':'0px','line-height':'25px','padding-left':'5px' }),
                    filterInput = $(self.el.p)
                                    .addClass('ui-sexyselect-filter-input')
                                    .addClass('ui-widget-content')
                                    .attr('id',this.element.attr('id') + '_filterInput')
                                    .addClass('ui-corner-all')
                                    .attr('type','text')
                                    .attr('wmt','enter filter text')
                                    .css({'color' : '#999', 'padding-left' : '5px','font-style' : 'italic','font-weight' : 'normal','-moz-border-radius': '4px', 'display':'inline-block','-webkit-border-radius': '4px','font-size':'0.8em','width' : self.options.width - 80 + 'px','border-radius' : '4px' })
                                    .val('enter filter text')
                                .focus(function()
                                {
                                    $(this).val('').addClass('ui-state-active').css({'color' : '#666', 'font-style' : 'normal' });
                                    self._syncItems();
                                })
                                .blur(function()
                                {
                                    x = $(this);
                                    x.removeClass('ui-state-active');
                                    var t = x.val();
                                    if(t == '')
                                    {
                                        x.css({'color' : '#999','font-style' : 'italic'}).val(x.attr('wmt'));
                                    }
                                    self._syncItems();
                                })
                                .keyup(function(e){
                                    if(self.filterTimer != null)
                                        clearTimeout(self.filterTimer);
                                    self.filterTimer = setTimeout("$('#" + self.element.attr('id') + "').sexyselect('filter')",self.options.filterKeyUpTimeout);
                                }),
                    filterClear = $(self.el.p)
                                    .attr('type','button')
                                    .attr('id' ,this.element.attr('id') + '_filterBtn')
                                    .addClass('ui-widget-content')
                                    .addClass('ui-corner-all')
                                    .addClass('ui-sexyselect-filter-btn')
                                    .css({'font-size':'0.8em', 'cursor':'pointer'})
                                    .val('clear')
                                    .mousedown(function()
                                    {
                                        $(this).removeClass('ui-state-active');
                                        $(this).addClass('ui-state-highlight');
                                        $(this).css('font-weight','normal');
                                    })
                                    .mouseup(function()
                                    {
                                        $(this).removeClass('ui-state-highlight');
                                        $(this).removeClass('ui-state-active');
                                        $(this).css('font-weight','normal');
                                    })
                                    .click(function(){
                                        $(this).parent().children('input[type=text]').val($(this).parent().children('input[type=text]').attr('wmt')).css({'color' : '#999','font-style' : 'italic'});
                                        self.filter();
                                    });
                filterBar.append(filterInput);
                filterBar.append(filterClear);
                filterWrapper.append(filterBar);
                return filterWrapper;
        },
        filter: function(filterText)
        {
            if(filterText !== undefined)
                $('#' + this.element.attr('id') + '_filterInput').val(filterText);
            this._syncItems();
        },
        _resetCreateEventArgs: function()
        {
            var self = this;
            self.createEventArgs.create = true;
            self.createEventArgs.errorText ='';
        },
        addItem : function(inputText, index)
        {
            var self = this,
                newOption = $('<option />')
                                    .val(inputText)
                                    .text(inputText);
            if(index !== undefined && index < self.element.children('option').size()-1)
                newOption.insertBefore(self.element.children('option:eq(' + index +')'));
            else
                self.element.append(newOption);
            self._syncItems();
        },
        _addItem: function(inputText, inputCtrl)
        {
            var self = this,
                initElem = this.element,
                x = inputCtrl,
                t = inputText;
            if(t != x.attr('wmt') && t != '')
                if(self.searchItem(t))
                    self.showError('!! New item already exists...',2500);
                else{
                    initElem.append($(self.el.o).val(t).text(t));
                    if($.isFunction(self.options.onitemcreated))
                    {
                        try {
                            self.options.onitemcreated(newOption, initElem);
                        } catch (ex) {
                            if(self.options.allowDebug)
                            {
                                alert('creation function failed: ' + ex.Description);
                            }
                        }
                    }
                    self._syncItems();
                }
            else if(t == '')
                self.showError('!! New option text empty..',2500);
            else if(t == x.attr('wmt'))
                self.showError('!! Enter new option text..',2500);
            x.css({'color' : '#999','font-style' : 'italic'}).val(x.attr('wmt'));
        },
        syncAux: function()
        {
            this._syncItems();
        },
        _syncItems : function()
        {
            var alt = false,
                self = this,
                initElem = this.element,
                filterText = $('#' + initElem.attr('id') + '_filterInput').val();//.trim();
            if(filterText !== undefined) 
                filterText.trim();
            else 
                filterText = null;
            if(filterText == $('#' + initElem.attr('id') + '_filterInput').attr('wmt') || filterText.trim == '')
                filterText = null;
            
            var itemsWrapper = $('#' + initElem.attr('id') + '_items ul'),
                init = self.element.children().size();
            itemsWrapper.html('');

            if(this.options.autoSort)
            {
                listitems = initElem.children('option').get().sort(function (a,b){  
                    a1 = a;  
                    b1 = b;  
                    if (isNaN($(a).val())) {  
                        a1 = $(a).text().toUpperCase();   
                    }  
                    if (isNaN($(b).val())) {  
                        b1 = $(b).text().toUpperCase();   
                    }  
                    return (a1 < b1) ? -1 : (a1 > b1) ? 1 : 0;   
                });  
                initElem.html('');  
                var opt = null,
                    $o = null;
                for(var i=0;i<=listitems.length-1;i++)
                {
                    opt = listitems[i];
                    $o = $('<option />')
                        .val($(opt).val())
                        .html($(opt).text());

                    if($(opt).attr('style'))
                    	$o.attr('style', $(opt).attr('style'));
                    if($(opt).attr('class'))
                    	$o.attr('class', $(opt).attr('class'));
                    if($(opt).attr('selected'))
                        $o.attr('selected','selected');
                    initElem.append($o);  
                }
            }
            initElem.children().each(function(){
                    var opt = $(this),
                        text = opt.text(),
                        value = opt.val(),
                        clazz = opt.attr('class'),
                        style = opt.attr('style'),
                        checked = opt.attr('selected'),
                        item = self._createItem(text,value,alt, checked, clazz, style);
                    itemsWrapper.append(item);
                    alt = (alt ? false : true);
            });
            
            if(filterText != null && filterText != '')
                itemsWrapper.find("li:not(':contains('" + filterText + "')')").hide();

            if((self.totalItems() > 1 || self.options.connectedList != null) && !self.options.autoSort)
            {
                itemsWrapper.sortable('destroy');
                itemsWrapper.sortable({ 
                    revert: self.revert,
                    containment: 'document',
                    appendTo: 'body',
                    helper: function(ui) {
                        return self._createDragItem( $(this).find('.ui-state-hover').find('.ui-sexyselect-item-label').text());
                    },
                    update: function(event, ui) {
                        var firstList = $(ui.item).attr('olist'),
                            firstItem = $(ui.item).attr('oitem'),
                            it,
                            ix;
                        if(itemsWrapper.attr('id') != firstList)
                        {
                            if(self.searchItem(self._getText($(ui.item))))
                            {
                                self.showError('!! Item alredy exists..',2500);
                                 $('#' + firstItem).sexyselect('syncAux');
                            }
                            else
                            {
                                it = self._getText($(ui.item));
                                ix = $(ui.item).index();
                                $('#' + firstItem).sexyselect('removeItem',it);
                                self.addItem(it,ix);
                            }
                        }
                        
                        items = itemsWrapper.children('li');
                        oItems = initElem.children('option');
                        var child = null;
                        $.each(items,function(idx,itm)
                        {
                            var i = $(itm).find('span:eq(2)'), 
                                text = i.html();
                            $(itm).find('span:first').removeClass('ui-icon-carat-2-n-s').removeClass('ui-icon-carat-1-s').removeClass('ui-icon-carat-1-n').addClass('ui-icon-carat-2-n-s');
                            $.each(oItems,function(idx,itm){
                                if($(this).html() == text)
                                {
                                    child= $(this);
                                    return;
                                }
                            });
                            initElem.append(child);
                        });
                        itemsWrapper.find('span.ui-sexyselect-sort:first').removeClass('ui-icon-carat-2-n-s').addClass('ui-icon-carat-1-s');
                        itemsWrapper.find('span.ui-sexyselect-sort:last').removeClass('ui-icon-carat-2-n-s').addClass('ui-icon-carat-1-n');
                        self._syncItems();
                        if($.isFunction(self.options.onItemsSync))
                        {
                            try {
                                self.options.onItemsSync(initElem);
                            } catch (ex) {
                                if(self.options.allowDebug)
                                {
                                    alert('creation function failed: ' + ex.Description);
                                }
                            }
                        }
                    },
                    start: function(event, ui)
                    {
                        ui.item.find('input').attr('rel','move');
                    },
                    stop: function(event, ui)
                    {   
                        ui.item.find('input').removeAttr('rel');
                    },
                   
                });
                if(self.options.connectedList != null)
                {
                     var select = self.options.connectedList.connection + '_listHolder';
                    itemsWrapper.sortable('option','connectWith',select);
                    if(self.options.connectedList.twoWay)
                        setTimeout("$('" + self.options.connectedList.connection + "').sexyselect('connectWith','" + itemsWrapper.attr('id') + "');",500);
                }
                itemsWrapper.find('span.ui-sexyselect-sort:first').removeClass('ui-icon-carat-2-n-s').addClass('ui-icon-carat-1-s');
                itemsWrapper.find('span.ui-sexyselect-sort:last').removeClass('ui-icon-carat-2-n-s').addClass('ui-icon-carat-1-n');
            }
            else if(self.totalItems() == 1 && !self.options.autoSort)
            {
                itemsWrapper.sortable('destroy');
                itemsWrapper.find('span.ui-sexyselect-sort:first').removeClass('ui-icon-carat-2-n-s').addClass('ui-icon-carat-1-s');
            }
            if(self.totalItems() == 0)
                itemsWrapper.append($(self.el.li)
                                    .css({ 'color' : '#999', 'font-style' : 'italic', 'font-size' : '0.8em'})
                                    .attr('align','center')
                                    .attr('id',self.element.attr('id') + '_noOption')
                                    .addClass('ui-sexyselect-nooptions')
                                    .html(self.options.nooptionstext));
            else if(self.totalVisibleItems() == 0)
                itemsWrapper.append($(self.el.li)
                                    .css({ 'color' : '#999', 'font-style' : 'italic', 'font-size' : '0.8em'})
                                    .attr('align','center')
                                    .attr('id',self.element.attr('id') + '_noOption')
                                    .addClass('ui-sexyselect-nooptions')
                                    .html(self.options.nooptionstext));
            if($.isFunction(self.options.onItemsSync))
            {
                try {
                    self.options.onItemsSync(initElem);
                } catch (ex) {
                    if(self.options.allowDebug)
                        alert('onItemsSync function failed: ' + ex.Description);
                }
            }
        },
        connectWith: function(itemID)
        {
            if($('#' + itemID) === undefined)
            {
                if(this.options.allowDebug) alert('Invalid connected list, please ensure you are calling the connection correctly');
                return;
            }
             $('#' + this.element.attr('id') + '_items ul').sortable('option','connectWith','#' + itemID);
        },
        searchItem: function(text)
        {
            var x = false; var self = this; var initElem = this.element;
            initElem.children().each(function(){
                if($(this).text().toLowerCase() == text.toLowerCase())
                    return x = true;
            });
            return x;
        },
        destroy: function()
        {
            var self = this, initElem = this.element;
            $('#' +initElem.attr('id') + '_ssWrapper').remove();
            initElem.show();
        },
        validateSize: function()
        {
            var x = true;
            if(this.options.minSize !== undefined)
                if(this.totalItems() < parseInt(this.options.minSize))
                {
                    x = false;
                    return false;
                }
            return x;
        },
        totalItems: function()
        {
            return this.element.children().size();
        },
        totalVisibleItems: function()
        {
             return $('#' + this.element.attr('id') + '_items ul').children('li:visible').size();
        },
        showError: function(text, timeOut)
        {
            var e = $('#' + this.element.attr('id') + '_ssWrapper').find('div.ui-sexyselect-error:first');
            e.html(text).slideDown(300,function() { setTimeout("$('#"+ $(e).attr('id') + "').slideUp(300);",timeOut); });
        },
        removeItem: function(value)
        {
            this._removeItem(value);
        },
        _removeItem: function(value)
        {
            var self = this;
            this.element.find("option[value='" + value + "']").remove();
            if($.isFunction(self.options.onItemDeleted))
            {
                try{
                    self.options.onItemDeleted(value, this);
                }
                catch(e){
                    if(self.options.allowDebug)
                        alert("Error occured while deleting item." + e);
                }
            }
            this._syncItems();
        },
        _getText : function(el)
        {
            return el.find('.ui-sexyselect-item-label').html();

        },
        _createItem: function(text, value, alt, checked, clazz, style)
        {
            var self = this,
                item = $(self.el.li)
                        .css({'cursor' : 'pointer', 'cursor':'default', 'font-weight':'normal','margin':'1px' })
                        .addClass('ui-state-default')
                        .addClass('ui-sexyselect-listitem')
                        .addClass(clazz)
                        .attr('olist', self.element.attr('id') + '_listHolder')
                        .attr('oitem', self.element.attr('id'))
                        .attr('value', text)
                        .attr('sel','sel_option'),
                dragPoistion =$(self.el.s)
                                .addClass('ui-icon')
                                .addClass('ui-icon-carat-2-n-s')
                                .addClass('ui-sexyselect-sort')
                                .css({'display' : (self.options.autoSort ? 'none' : 'inline-block'), 'margin' : '0' , 'padding' : '0', 'cursor':'pointer', 'width' :  (!self.options.autoSort ? '16px' : '1px') })
                                .attr('title','drag to sort')
                                .attr('value',text),
                additionalOption =$(self.el.s)
				                .addClass((self.options.allowAdditionalOption ? 'ui-icon' : ''))
				                .addClass((self.options.allowAdditionalOption ? 'ui-icon-option' : ''))
				                .addClass('ui-sexyselect-option')
				                .css({'display' : 'inline-block', 'cursor':'pointer', 'width' : (self.options.allowAdditionalOption ? '16px' : '1px'), 'vertical-align':'top', 'align' : 'right' })
				                .attr('value',text)
				                .attr('title','change attributes for this option'),
                trashBin =   $(self.el.s)
                                .addClass((self.options.allowDelete ? 'ui-icon' : ''))
                                .addClass((self.options.allowDelete ? 'ui-icon-trash' : ''))
                                .addClass('ui-sexyselect-trash')
                                .css({'display' : 'inline-block', 'cursor':'pointer', 'width' : (self.options.allowDelete ? '16px' : '1px'), 'vertical-align':'middle' })
                                .attr('value',text)
                                .attr('title','remove this option')
                                .click(function()
                                {
                                    if(self.options.allowDelete)
                                    {
                                        var id = $(this).attr('value'),
                                            dItem = $(this);
                                        if(self.options.confirmDelete.confirm) 
                                        {
                                            var delText = self.options.confirmDelete.text;//;
                                            delText = delText.replace('{0}',id );
                                            if(!self.options.confirmDelete.modal)
                                            {
                                                if(!confirm(delText))
                                                {
                                                    if($.isFunction(self.options.confirmDelete.callBack))
                                                        self.options.confirmDelete.callBack(true, dItem);
                                                    return false;
                                                }
                                                if($.isFunction(self.options.confirmDelete.callBack))
                                                    self.options.confirmDelete.callBack(false, dItem);
                                            }
                                            else
                                            {
                                                var titleText = '<span class=\'ui-sexyselect-modal-title-icon\'></span> Confirm Delete',
                                                    modal = $(self.el.d)
                                                            .addClass('ui-sexyselect-modal-wrapper')
                                                            .attr('title',titleText),
                                                    text = $(self.el.d)
                                                            .addClass('ui-sexyselect-modal-delete-text')
                                                            .html(delText);
                                                modal.append(text);
                                                modal.dialog({
                                                    resizable: false,
                                                    height: 160,
                                                    modal: true,
                                                    buttons: { 'Confirm' : function() { 
                                                                                        self._removeItem(id); 
                                                                                        $(this).dialog('destroy'); 
                                                                                        if($.isFunction(self.options.confirmDelete.callBack))
                                                                                            self.options.confirmDelete.callBack(true, dItem);
                                                                                      },
                                                                'Cancel' : function() {
                                                                                        $(this).dialog('destroy'); 
                                                                                        if($.isFunction(self.options.confirmDelete.callBack))
                                                                                            self.options.confirmDelete.callBack(false, dItem);
                                                                                      }
                                                    },
                                                    close: function() { $(this).dialog('destroy'); }
                                                });
                                                return false;
                                            }
                                        }
                                       
                                        self._removeItem(id);
                                    }
                                }),
                labelItem = $(self.el.s)
                                .css({ 'cursor':'default'  } )
                                .addClass('ui-sexyselect-item-label')
                                .css({'text-align' : (self.options.text.textAlign == null ? 'inherit' : self.options.text.textAlign),'font-size': (self.options.text.textSize == null ? 'inherit' : self.options.text.textSize) })
                                .html(text);

            if(style) {
            	item.attr('style', style);
            }
            item.append(dragPoistion);
            item.append(trashBin);
            item.append(labelItem);

            if(self.options.selectionMode == 'multiple' || self.options.selectionMode == 'single')
            {
                labelItem.remove();
                var sel = self.options.selectionMode == 'multiple',
                    input = $(self.el.p)
                            .attr('type',(sel ? 'checkbox' : 'radio'))
                            .addClass((sel ? 'ui-checkbox' : 'ui-radiobox'))
                            .attr('name',self.element.attr('id') + (sel ? '_check' : '_radio'))
                            .addClass((sel ? 'ui-sexyselect-checkbox' : 'ui-sexyselect-radiobox'))
                            .attr('id',self.element.attr('id') + '_chk_' + text.replace(/ /gi,'_'))
                            .click(function(){
                                self._handleClick($(this));
                                
                            })
                            .val(text),
                    label = $('<label />')
                            .addClass((sel ? 'ui-sexyselect-checkbox-label' : 'ui-sexyselect-radiobox-label'))
                            .addClass('ui-sexyselect-item-label')
                            .css({'text-align' : (self.options.text.textAlign === undefined ? 'inherit' : self.options.text.textAlign),'font-size': (self.options.text.textSize === undefined ? 'inherit' : self.options.text.textSize) })
                            .attr('for',self.element.attr('id') + '_chk_' + text.replace(/ /gi,'_'))
                            .html(text);
                if(this.options.text.checkBoxPosition == 'left'  || this.options.styleize)
                {
                    item.append(input);
                    item.append(label);
                }
                else if(this.options.text.checkBoxPosition == 'right' && this.options.styleize == false)
                {
                    item.append(label);
                    item.append(input);
                }
                if(checked)
                    $(item.children('input[type=' + (sel ? 'checkbox' : 'radio') + ']').get(0)).attr('checked','checked');
                if(self.options.styleize)
                {
                    
                    if(sel)
                        input.checkbox({ callBack: function(item) { self._handleClick(input)}, checkPosition: self.options.text.checkBoxPosition, textAlign: self.options.text.textAlign, textSize: this.options.text.textSize  });
                    else
                        input.radiobox({ callBack: function(item) { self._handleClick(input)}, triState: self.options.triStateRadio, checkPosition: self.options.text.checkBoxPosition, textAlign: self.options.text.textAlign, textSize: this.options.text.textSize });
                }
            }
            // the option is attached at the end, not at the beginning like the rest
            item.append(additionalOption);
            

            item.hover(
                function() { $(this).addClass('ui-state-hover'); },
                function() { $(this).removeClass('ui-state-hover'); }
            );
            return item;
        },
        _createDragItem: function(text)
        {
            var self = this,
                wrap = $(self.el.u).css({'list-style-type':'none', 'padding' : '0' , 'margin' : '0','cursor':'pointer' }).append(),
                item = $(self.el.li)
                        .css({'cursor' : 'pointer', 'cursor':'default', 'font-weight':'normal','margin':'1px','height':'22px','line-height':'22px' })
                        .addClass('ui-state-hover')
                        .addClass('ui-sexyselect-drag-listitem')
                        .attr('value', text)
                        .attr('sel','sel_option');
            if(self.options.styleize)
                item.addClass('ui-sexyselect-styleize-label');
            var dragHandle = $(self.el.s)
                             .css({'height':'12px','width':'12px','display':'inline-block'})
                             .addClass('ui-icon')
                             .addClass('ui-icon-grip-dotted-vertical')
                             .addClass('ui-sexyselect-drag-item-grip')
                             .text(' '),
                dragText = $(self.el.s)
                            .css({ 'cursor':'default'  } )
                            .addClass('ui-sexyselect-item-label')
                            .html(text);
            item.append(dragHandle);
            item.append(dragText);
            wrap.append(item)
            return wrap;
        },
        _handleClick : function(elem)
        {
            var self = this, initElem = this.element, checked = $(elem).attr('checked');
            initElem.children('option').each(function(o,i){
                if($(i).html() == $(elem).val())
                {
                    if(!self.options.triStateRadio || self.options.selectionMode == 'multiple')
                    {
                        if(checked)
                            $(i).attr('selected','selected');
                        else
                            $(i).removeAttr('selected');
                    }
                    else if(self.options.triStateRadio && self.options.selectionMode == 'single')
                    {
                        if($(i).is(':selected') && checked)
                        {
                            $(elem).removeAttr('checked');
                            $(i).removeAttr('selected');
                        }
                        else if($(i).is(':not(:selected)') && checked)
                        {
                            $(i).attr('selected','selected');
                        }
                        else
                            $(i).removeAttr('selected');
                    }
                }
            });
            self.element.trigger('onchange');
            self.element.trigger('click');
            if($.isFunction(self.options.onItemSelected)){
               try{
                   self.options.onItemSelected(elem,initElem.children('option'));
               } catch (ex) {
                   if(self.options.allowDebug)
                   alert('select function failed: ' + ex.Description);
               }
           }
        },
        _expand: function()
        {
            var self = this, initElem = this.element, dir = (self.options.expanded = (!self.options.expanded)),
                ico = $('#' + initElem.attr('id') + '_icon');
            ico.removeClass('ui-icon-circle-triangle-n')
               .removeClass('ui-icon-circle-triangle-s')
               .addClass((dir ? 'ui-icon-circle-triangle-s' : 'ui-icon-circle-triangle-n'));
            $('#' +initElem.attr('id') + '_ssWrapper').animate({
                height: (!dir ? 28 : self.options.height)  + 'px'
            },500,function(){});
        },
        elements: function()
        {
            return this.element.children('option');
        },
        selectOption: function(element, checked)
        {
        
            var self = this,
                initElem = this.element;
            if(checked)
                $(element).attr('selected','selected');
            else
                $(element).removeAttr('selected');
            this._syncItems();
            
        },
        getOption: function (optionName) {
            if(optionName == 'autoSort') return this.options.autoSort;
        }
        
    });
})(jQuery);

(function ($) {
    $.widget('ui.checkbox', {
        options: {
            callBack: null,
            checkPosition: 'left',
            textSize : '1em', 
            textAlign : 'left' //stops inheritance align
        },
        _init: function () {
            var self = this;
            var initElem = this.element;
            var input = initElem;
            var label = initElem.next()
            input.hide();
            label.hide();
            var image = $('<span />')
                        .addClass(!input.attr('disabled') ? 'ui-checkbox-icon' : 'ui-checkbox-icon-disbled')
                        .addClass((this.options.checkPosition == 'right' ? 'ui-checkbox-right' : null))
                        .css({'text-align' : (this.options.textAlign == null ? 'inherit' : this.options.textAlign), 'font-size': (this.options.textSize == null ? 'inherit' : this.options.textSize) }) 
                        .attr('id', input.attr('id') + '_img');
            
            if (input.attr('checked'))
                image.addClass('ui-checkbox-selected' + (this.options.checkPosition == 'right' ? '-right' : ''));
            else
                image.addClass('ui-checkbox-unselected' + (this.options.checkPosition == 'right' ? '-right' : ''));
            if (!input.attr('disabled')) {
                image.click(function () {
                    if(input.attr('rel')) return;
                    if (input.is(':checked')) {
                        input.removeAttr('checked');
                        if(self.options.checkPosition == 'left')
                        {
                            $(this).removeClass('ui-checkbox-selected-over');
                            $(this).removeClass('ui-checkbox-selected');
                            $(this).addClass('ui-checkbox-unselected');
                        }
                        else
                        {
                            $(this).removeClass('ui-checkbox-selected-over-right')
                                   .removeClass('ui-checkbox-selected-right')
                                   .addClass('ui-checkbox-unselected-right');
                        }
                    }
                    else {
                        input.attr('checked', 'checked');
                        if(self.options.checkPosition == 'left')
                        {
                            $(this).removeClass('ui-checkbox-unselected-over');
                            $(this).removeClass('ui-checkbox-unselected');
                            $(this).addClass('ui-checkbox-selected');
                        }
                        else
                        {
                            $(this).removeClass('ui-checkbox-unselected-over-right')
                                   .removeClass('ui-checkbox-unselected-right')
                                   .addClass('ui-checkbox-selected-right');
                        }
                    }
                    if($.isFunction(self.options.callBack))
                    {
                        try{ self.options.callBack($(this)); } 
                        catch (ex) { }
                    }
                });
            }
            image.html(label.html());
            if (!input.is(':disabled')) {
                image.hover(
                        function () {
                            if (input.attr('checked')) {
                                $(this).addClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                            else {
                                $(this).addClass('ui-checkbox-unselected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        }, //in
                        function () {
                            if (input.attr('checked')) {
                                $(this).removeClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                            else {
                                $(this).removeClass('ui-checkbox-unselected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        } //out
                    );
            }
            initElem.after(image);
        }
    });
})(jQuery);

(function ($) {
    $.widget('ui.radiobox', {
        options: {
            callBack: null,
            triState: false,
            checkPosition: 'left',
            textSize : null, 
            textAlign : null 
        },
        _init: function () {
            var self = this;
            var initElem = this.element;

            var input = initElem;
            var label = input.next();
            input.hide();
            label.hide();
            var image = $('<span />')
                                .addClass(!input.attr('disabled') ? 'ui-checkbox-icon' : 'ui-checkbox-icon-disbled')
                                .addClass((this.options.checkPosition == 'right' ? 'ui-checkbox-right' : null))
                                .css({'text-align' : (this.options.textAlign == null ? 'inherit' : this.options.textAlign), 'font-size': (this.options.textSize == null ? 'inherit' : this.options.textSize) }) 
                                .attr('id', input.attr('id') + '_img');
            image.attr('name',initElem.attr('name'));
            if(!image.hasClass('ui-radiobox')) image.addClass('ui-radiobox');
            if (input.attr('checked'))
                image.addClass('ui-checkbox-selected' + (self.options.checkPosition == 'right' ? '-right' : ''));
            else
                image.addClass('ui-checkbox-unselected' + (self.options.checkPosition == 'right' ? '-right' : ''));

            label.attr('for', input.attr('id') + '_img');
            if (!input.is(':disabled')) {
                image.click(function () {
                    if(input.attr('rel')) return;
                    if(input.is(':not(:checked)'))
                    {
                        $('span.ui-radiobox[name=' + initElem.attr('name') + ']').each(
                            function(idx,i){
                                        $(i).removeAttr('checked')
                                        .removeClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                        .removeClass('ui-checkbox-selected' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                        .addClass('ui-checkbox-unselected' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        );
                        
                        input.attr('checked', 'checked');
                        image.removeClass('ui-checkbox-unselected-over' + (self.options.checkPosition == 'right' ? '-right' :''))
                             .removeClass('ui-checkbox-unselected' + (self.options.checkPosition == 'right' ? '-right' : ''))
                             .addClass('ui-checkbox-selected' + (self.options.checkPosition == 'right' ? '-right' : ''));
                    }
                    else if(self.options.triState)
                    {
                        $('span.ui-radiobox[name=' + initElem.attr('name') + ']').each(
                            function(idx,i){
                                        $(i).removeAttr('checked')
                                        .removeClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                        .removeClass('ui-checkbox-selected' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                        .addClass('ui-checkbox-unselected' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        );
                        if(input.is(':checked'))
                        {
                            input.removeAttr('checked');
                            image.removeClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                 .removeClass('ui-checkbox-selected' + (self.options.checkPosition == 'right' ? '-right' : ''))
                                 .addClass('ui-checkbox-unselected' + (self.options.checkPosition == 'right' ? '-right' : ''));
                        }
                    }
                    if($.isFunction(self.options.callBack))
                    {
                        try{ self.options.callBack($(this)); } 
                        catch (ex) { }
                    }
                });
            }
            image.html(label.html());
            image.attr('title',label.text());
            if (!input.is(':disabled')) {
                image.hover(
                        function () {
                            if (input.attr('checked')) {
                                $(this).addClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                            else {
                                $(this).addClass('ui-checkbox-unselected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        }, //in
                        function () {
                            if (input.attr('checked')) {
                                $(this).removeClass('ui-checkbox-selected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                            else {
                                $(this).removeClass('ui-checkbox-unselected-over' + (self.options.checkPosition == 'right' ? '-right' : ''));
                            }
                        } //out
                    );
            }
            initElem.after(image);
        }
    });
})(jQuery);

/**
 * .disableTextSelect - Disable Text Select Plugin
 *
 * Version: 1.1
 * Updated: 2007-11-28
 *
 * Used to stop users from selecting text
 *
 * Copyright (c) 2007 James Dempster (letssurf@gmail.com, http://www.jdempster.com/category/jquery/disabletextselect/)
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 **/

/**
 * Requirements:
 * - jQuery (John Resig, http://www.jquery.com/)
 **/
(function($) {
    if ($.browser.mozilla) {
        $.fn.disableTextSelect = function() {
            return this.each(function() {
                $(this).css({
                    'MozUserSelect' : 'none'
                });
            });
        };
        $.fn.enableTextSelect = function() {
            return this.each(function() {
                $(this).css({
                    'MozUserSelect' : ''
                });
            });
        };
    } else if ($.browser.msie) {
        $.fn.disableTextSelect = function() {
            return this.each(function() {
                $(this).bind('selectstart.disableTextSelect', function() {
                    return false;
                });
            });
        };
        $.fn.enableTextSelect = function() {
            return this.each(function() {
                $(this).unbind('selectstart.disableTextSelect');
            });
        };
    } else {
        $.fn.disableTextSelect = function() {
            return this.each(function() {
                $(this).bind('mousedown.disableTextSelect', function() {
                    return false;
                });
            });
        };
        $.fn.enableTextSelect = function() {
            return this.each(function() {
                $(this).unbind('mousedown.disableTextSelect');
            });
        };
    }
})(jQuery);