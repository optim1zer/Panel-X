/**
 *Действия с сайтами в панелях, связанных с отображением информации о сайтах
**/


//Изменение состояния флажка "установлен/не установлен"
function change(id, field){
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;">&nbsp;');
    window.current_request=$.ajax({
        type: "POST",
        url: "ajax.php",
        async: false,
        dataType: "json",
        data: {
            action: 'change_stat',
            id: id,
            field: field
        },
        success: function(data){
            $('#debugger').html('');
            $('#' + field + '_' + id).html(data.answer);
        }
    });
}
//Добавляем сайты по списку из формы
function do_add() {
    list = $('#sites_form').val();
    urls = list.split("\n")
    dir = $('#site_cat').val();
    host = $('#site_host').val();
    cms = $('#site_cms').val();
    registrator = $('#site_registrator').val();
    comment = $('#site_comment').val();
    panel = $('#cur_panel').val();
    if($('#noparams').is(':checked')) checked = '1'; else checked = '0';
    $.fancybox.close();
    sites_add(urls, dir, host, cms, registrator, comment, panel, checked);
}
//Добавляем все сайты в панель
function sites_add(urls, dir, host, cms, registrator, comment, panel, checked) {
    var urls_array = urls.shift();
    if(urls_array) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Добавляется ' + urls_array);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if(req.responseJS.answer=='0') alert("Такой сайт уже есть в панели");
                if((req.responseJS.answer == 'captcha') || (req.responseJS.answer == 'needip')) {
                    if(req.responseJS.answer == 'captcha') alert('Яндекс посчитал нас ботом. Нужно переждать.');
                    if(req.responseJS.answer == 'needip') alert('Зарегистрируйте на странице http://xml.yandex.ru/ IP-адрес для запросов: ' + req.responseJS.ip);
                    $('#debugger').html('');
                } else {
                    $('#debugger').html(req.responseJS.answer);
                    $("#tbodysites").append(req.responseJS.row);
                    $("#tablesorter").trigger("update");
                    $("#tablesorter").trigger("appendCache")
                    row_highlight();
                    make_options();
                    $('#debugger').html('');
                    $('#totalsites').html(parseInt($('#totalsites').html()) + 1);
                    if(panel=='sites') mydnd();
                    sites_add(urls, dir, host, cms, registrator, comment, panel, checked);
                }
            }
        }
        min_cur = $('#min_cur_position').val();
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'add_sites',
            domain: urls_array,
            dir: dir,
            host: host,
            cms: cms,
            registrator: registrator,
            comment: comment,
            checked: checked,
            panel: panel,
            min_cur: min_cur
        } );
    } else {
        if(panel=='sites') main_load('sites',$('#cur_page').val());
    }
}
//Запуск редактирования сайта
function site_edit(id) {
    dir = $('#site_cat').val();
    host = $('#site_host').val();
    cms = $('#site_cms').val();
    url = $('#site_url').val();
    fburn = $('#site_fburn').val();
    registrator = $('#site_registrator').val();
    comment = $('#site_comment').val();
    panel = $('#cur_panel').val();
    addstr = '';
    $('textarea.additional').each(function(){
        addstr += '||' + $(this).attr('id') + '::' + $(this).val();
    });
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Сохраняется ' + url);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            $('#debugger').html(req.responseJS.answer);
            $("#row_" + id).replaceWith(req.responseJS.row);
            $("#tablesorter").trigger("update");
            $("#tablesorter").trigger("appendCache")
            row_highlight();
            make_options();
            $('#debugger').html('');
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'edit_site',
        id: id,
        dir: dir,
        host: host,
        cms: cms,
        registrator: registrator,
        comment: comment,
        panel: panel,
        fburn: fburn,
        addstr: addstr
    } );
}
//Удаление сайта
function site_delete(id, url){
    if(confirm("Вы действительно хотите удалить этот сайт из панели?")) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Удаляется ' + url);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html(req.responseJS.answer);
                $("#row_" + id).remove();
                $('#debugger').html('');
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache");
                $("#totalsites").html(parseInt($("#totalsites").html()) - 1);
                row_highlight();
                make_options();

            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'delete_site',
            id: id
        } );
    }
}
//Запуск удаления
function sites_delete(ids, urls) {
    var urls_array = urls.shift();
    var ids_array = ids.shift();
    if(urls_array) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Удаляется ' + urls_array);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html(req.responseJS.answer);
                $("#row_" + ids_array).remove();
                $('#debugger').html('');
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache");
                $("#totalsites").html(parseInt($("#totalsites").html()) - 1);
                row_highlight();
                make_options();
                sites_delete(ids, urls);
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'delete_site',
            id: ids_array
        } );
    } else {
        if($('#cur_panel').val()=='sites') main_load('sites',$('#cur_page').val());
    }
}
//Обновление показателей сайта
function site_update(id, url){
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Обновляется ' + url);
    panel = $('#cur_panel').val();
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            //        		alert(req.responseText);
            if((req.responseJS.answer == 'captcha') || (req.responseJS.answer == 'needip')) {
                if(req.responseJS.answer == 'captcha') alert('Яндекс посчитал нас ботом. Нужно переждать.');
                if(req.responseJS.answer == 'needip') alert('Зарегистрируйте на странице http://xml.yandex.ru/ip.xml IP-адрес для запросов: ' + req.responseJS.ip);
                $('#debugger').html('');
            } else {
                $('#debugger').html(req.responseJS.answer);
                $("#row_" + id).replaceWith(req.responseJS.row);
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                make_options();
                $('#debugger').html('');
                row_highlight();
            }
        //            	$('#row_'+id).click(function(){if($(this).hasClass('selected')) $(this).removeClass('selected'); else $(this).addClass('selected');});
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'update_site',
        id: id,
        panel: panel
    } );
}
//Запуск обновления
function sites_update(ids, urls) {
    var urls_array = urls.shift();
    var ids_array = ids.shift();
    panel = $('#cur_panel').val();
    if(urls_array) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Обновляется ' + urls_array);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if((req.responseJS.answer == 'captcha') || (req.responseJS.answer == 'needip')) {
                    if(req.responseJS.answer == 'captcha') alert('Яндекс посчитал нас ботом. Нужно переждать.');
                    if(req.responseJS.answer == 'needip') alert('Зарегистрируйте на странице http://xml.yandex.ru/ip.xml IP-адрес для запросов: ' + req.responseJS.ip);
                    $('#debugger').html('');
                } else {
                    $('#debugger').html(req.responseJS.answer);
                    $("#row_" + ids_array).replaceWith(req.responseJS.row);
                    $("#tablesorter").trigger("update");
                    $("#tablesorter").trigger("appendCache")
                    row_highlight();
                    make_options();
                    if(panel=='sites') mydnd();
                    $('#debugger').html('');
                    sites_update(ids, urls);
                }
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'update_site',
            id: ids_array,
            panel: panel
        } );
    }
}
//Запуск обновления
function cols_update(ids, urls, col) {
    var urls_array = urls.shift();
    var ids_array = ids.shift();
    panel = $('#cur_panel').val();
    if(urls_array) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Обновляется ' + urls_array);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if((req.responseJS.answer == 'captcha') || (req.responseJS.answer == 'needip')) {
                    if(req.responseJS.answer == 'captcha') alert('Яндекс посчитал нас ботом. Нужно переждать.');
                    if(req.responseJS.answer == 'needip') alert('Зарегистрируйте на странице http://xml.yandex.ru/ip.xml IP-адрес для запросов: ' + req.responseJS.ip);
                    $('#debugger').html('');
                } else {
                    $('#debugger').html(req.responseJS.answer);
                    $("#row_" + ids_array).replaceWith(req.responseJS.row);
                    $("#tablesorter").trigger("update");
                    $("#tablesorter").trigger("appendCache")
                    row_highlight();
                    make_options();
                    $('#debugger').html('');
                    cols_update(ids, urls, col);
                }
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'update_col',
            id: ids_array,
            col: col,
            panel: panel
        } );
    }
}
//Обновление всех сайтов на странице
function update_all() {
    var urls = new Array();
    var ids = new Array();
    $('.myid').each(function () {
        urls.push($(this).attr('name'));
        ids.push($(this).val());
    });
    sites_update(ids, urls);
}
//Обновление выделенных сайтов
function update_selected() {
    var urls = new Array();
    var ids = new Array();
    $('.selected .myid').each(function (){
        urls.push($(this).attr('name'));
        ids.push($(this).val());
    });
    sites_update(ids, urls);
}
//Обновление выделенных сайтов
function refresh_col(col) {
    var urls = new Array();
    var ids = new Array();
    if($('.selected .myid').size()>0) {
        $('.selected .myid').each(function (){
            urls.push($(this).attr('name'));
            ids.push($(this).val());
        });
    } else {
        $('.myid').each(function (){
            urls.push($(this).attr('name'));
            ids.push($(this).val());
        });
    }
    cols_update(ids, urls, col);
}
//Обновление выделенных сайтов
function delete_selected() {
    if(confirm('Вы действительно хотите удалить выделенные сайты?')) {
        var urls = new Array();
        var ids = new Array();
        $('.selected .myid').each(function (){
            urls.push($(this).attr('name'));
            ids.push($(this).val());
        });
        sites_delete(ids, urls);
    }
}
//Функция перемещения сайта на одну позицию
function position(direction,rowid){
    if(direction=='up') {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                if($('#tablesorter tbody tr:first').attr('id') != 'row_'+rowid) {
                    $("#row_"+rowid).prev().before($("#row_"+rowid));
                    $('#row_'+rowid + ' .poslinks').html(req.responseJS.poslinks);
                    $('#row_'+rowid).next().children('td').children('div.poslinks').html(req.responseJS.poslinks2);
                } else {
                    $('#row_'+rowid).replaceWith(req.responseJS.replace);
                    $('#row_'+rowid + ' .poslinks').html(req.responseJS.poslinks2);
                }
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();
                make_options();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'onepos_update',
            id: rowid,
            direction: 'up'
        } );
    }
    if(direction=='down') {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                if($('#tablesorter tbody tr:last').attr('id') != 'row_'+rowid) {
                    $("#row_"+rowid).next().after($("#row_"+rowid));
                    $('#row_'+rowid + ' .poslinks').html(req.responseJS.poslinks);
                    $('#row_'+rowid).prev().children('td').children('div.poslinks').html(req.responseJS.poslinks2);
                } else {
                    $('#row_'+rowid).replaceWith(req.responseJS.replace);
                    $('#row_'+ req.responseJS.newid + ' .poslinks').html(req.responseJS.poslinks2);
                }
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();
                make_options();
                mydnd();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'onepos_update',
            id: rowid,
            direction: 'down'
        } );
    }
}
//Основная загрузка
function main_load(section,page) {
    $('#dright a').removeClass('ac');
    $('#dright a').addClass('noac');
    $('#dright a#button_'+section).addClass('ac').removeClass('noac');
    if(!section) var section = '';
    if(!page) var page = '1'
    var req = new JsHttpRequest();
    $('#debugger').html('<img src="images/loading_white.gif" border="0" alt="" style="vertical-align: middle;" />&nbsp;');
    // Code automatically called on load finishing.
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            // Write result to page element (_RESULT become responseJS).
            $('#cur_panel').val(section);
            $('#maintable').html(req.responseText);
            $('#buttons').html(req.responseJS.buttons);
            $('#buttons2').html(req.responseJS.buttons2);
            $('#bmenu').html(req.responseJS.pages);
            $('#update').html(req.responseJS.pupdate);
            loader_end();
            $('.dright span a').each(function () {
                $(this).removeClass('ac');
                $(this).addClass('noac');
            });
            $('#button_'+section).removeClass('noac');
            $('#button_'+section).addClass('ac');
            fixer(req.responseJS.fixer);
            $('#fixer').show();
            $("#tbodysites tr").quicksearch({
                position: 'after',
                attached: '#qs',
                stripeRowClass: ['odd', 'even'],
                labelText: '',
                inputClass: 'searchTextbox',
                inputText: 'Поиск...',
                loaderText: 'Обработка...',
                loaderId: 'debugger'
            });
	    $("a[rel=fancybox]").fancybox({showNavArrows: false,
                titleShow: false});
            if(section=='sites') mydnd();
        }
    }
    // Prepare request object (automatically choose GET or POST).
    req.open(null, 'ajax.php', true);
    // Send data to backend.
    req.send( {
        action: 'main_load',
        section: section,
        pagenum: page
    } );
}
//Отметка для строк ("с выделенными")
function select_toggle(element) {
    if($(element).hasClass('selected')) $(element).removeClass('selected'); else $(element).addClass('selected');
}
//Генерация отображения опций при наведении на УРЛ сайта
function make_options() {
    $('.myurl').each(function() {
        $(this).mouseover(function(){
            $(this).children('a.goto').hide();
            $(this).children('span.options').show();
            $(this).css({
                'text-align':'center'
            }).addClass('bgwhite');
        });
        $(this).mouseout(function(){
            $(this).children('span.options').hide();
            $(this).children('a.goto').show();
            $(this).css({
                'text-align':'left'
            }).removeClass('bgwhite');
        });
        $('a[rel=fancybox]').fancybox({
            showNavArrows: false,
            titleShow: false
        });
    });
    imagePreview();
}
//Драг'н'дроп для строк
function mydnd(){
    // Initialise the table 2
    $("#tablesorter").tableDnD({
        onDragClass: "myDragClass",
        dragHandle: "dragHandle",
        onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            var debugStr = "";
            firstpos = $('#min_cur_position').val();
            alert(firstpos);
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+":";
            }
            //            alert(debugStr);return false;
            $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
            var req = new JsHttpRequest();
            req.onreadystatechange = function() {
                if (req.readyState == 4) {
                    if(req.responseJS.status == 'needreload') {
                        main_load('sites',$('#cur_page').val());
                    } else {
                        $('#debugger').html('');
                        $("#tablesorter").trigger("update");
                        $("#tablesorter").trigger("appendCache")
                        row_highlight();
                        make_options();
                    }
                }
            }
            req.open(null, 'ajax.php', true);
            req.send( {
                action: 'multipos_update',
                positions: debugStr,
                firstpos: firstpos
            } );
        }
    });

    $("#tablesorter tr").hover(function() {
        $(this.cells[0]).addClass('showDragHandle');
    }, function() {
        $(this.cells[0]).removeClass('showDragHandle');
    });
}
//Подсветка ячеек и перегенерация нескрываемой таблички
function row_highlight(){
    $(".tablesorter tbody tr").mouseover(function(){
        $(this).addClass("over")
    }).mouseout(function(){
        $(this).removeClass("over");
    });
    //	$(".tablesorter tbody tr").click(function(){if($(this).hasClass('selected')) $(this).removeClass('selected'); else $(this).addClass('selected');});
    fixer();
}
//Несдвигаемая табличка, которая всегда на экране, если thead спрятался
function fixer(val){
    $('#fixer').html(val);
    $('#tablesorter thead tr th').each(function() {
        var id = $(this).attr('id').replace("htd", "ftd");
        width = $(this).width();
        //alert('$("#"'+id+').width('+width+');');
        $('#'+id+'').css({
            'width': width+'px'
        }).attr('width', width);
        $('#'+id+' div').css({
            'width': width+'px'
        }).attr('width', width);
    });
    $('#fixer .tablesorter').width($('#tablesorter').width());
    $('#maintable').scroll(function(){
        $('#fixer').animate({
            scrollLeft: $('#maintable').scrollLeft()
        }, 0);
    });
    $('#fixer .tablesorter thead tr th').mouseover(function() {
        $(this).children('.inf').hide();
        $(this).children('.rel').show();
    }).mouseout(function() {
        $(this).children('.inf').show();
        $(this).children('.rel').hide();
    });
    $('#fixer .tablesorter').show().css({
        'margin-right':'15px'
    });
}
//Удаление фильтра из кукисов
function del_filter(type) {
    setCookie("filter_" + type, "");
    main_load($('#cur_panel').val(), 1);
}
//Добавление фильтра по папкам/хостингам/регистраторам/CMS/будильникам
function add_filter(type, val) {
    startingfilter = get_cookie("filter_" + type);
    if(startingfilter.length=='0') startingfilter = ':';
    newFilter = startingfilter + val + ':';
    setCookie("filter_" + type, newFilter);
    main_load($('#cur_panel').val(), 1);
}
//Снятие фильтра по одному из айди
function switch_filter(type, val) {
    startingfilter = get_cookie("filter_" + type);
    newFilter = startingfilter.replace(val + ':', '');
    setCookie("filter_" + type, newFilter);
    main_load($('#cur_panel').val(), 1);
}

/**
 *Действия с фильтрами: папки, хостинги, регистраторы, CMS.
**/


//Загрузка страницы управления фильтрами
function page(type){
    $('.dright span a').each(function () {
        $(this).removeClass('ac');
        $(this).addClass('noac');
    });
    $('#cur_panel').val('sites');
    var req = new JsHttpRequest();
    $('#debugger').html('<img src="images/loading_white.gif" border="0" alt="" style="vertical-align: middle;" />&nbsp;');
    // Code automatically called on load finishing.
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            // Write result to page element (_RESULT become responseJS).
            $('#fixer').hide();
            $('#maintable').html(req.responseJS.table);
            $('#buttons').html(req.responseJS.buttons);
            $('#buttons2').html('');
            $('#bmenu').html('');
            $('#nav2').droppy();
            $('a[rel=fancybox]').fancybox({
                showNavArrows: false,
                titleShow: false
            });
            $('#debugger').html('');
            row_highlight();
            make_options();
        }
    }
    // Prepare request object (automatically choose GET or POST).
    req.open(null, 'ajax.php', true);
    // Send data to backend.
    req.send( {
        action: 'page_load',
        type: type,
        pagenum: page
    } );
}
//Добавляем фильтр после формы
function add(type) {
    title = $('#new_title').val();
    if((type=='regs') || (type=='hosts')) {
        desc = $('#new_billing').val() + '||' + $('#new_cp').val();
    } else {
        desc = '';
    }
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Добавляется ' + title);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            if(req.responseText=='0') {
                alert("Такой эелемент уже есть");
                $('#debugger').html('');
                return;
            }
            page(type);
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'add_element',
        title: title,
        type: type,
        desc: desc
    } );
}
//Сохраняем фильтр после формы
function save(type, id) {
    title = $('#new_title').val();
    if((type=='regs') || (type=='hosts')) {
        desc = $('#new_billing').val() + '||' + $('#new_cp').val();
    } else {
        desc = '';
    }
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Сохраняется ' + title);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            if(req.responseText=='0') {
                alert("Этот элемент не принадлежит вам");
                return;
            }
            $("#element_" + id).replaceWith(req.responseText);
            $("#tablesorter").trigger("update");
            $("#tablesorter").trigger("appendCache")
            row_highlight();
            $('#debugger').html('');
        }
    }
    req.open(null, 'ajax.php', true);

    req.send( {
        action: 'edit_element',
        title: title,
        id: id,
        type: type
    } );
}
//Удаляем фильтр после формы
function el_delete(id, type) {
    if(confirm("Вы действительно хотите удалить этот элемент?")) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if(req.responseText=='0') {
                    alert("Этот элемент не принадлежит вам");
                    return;
                }
                $("#element_" + id).remove();
                $('#debugger').html('');
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();

            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'delete_element',
            id: id,
            type: type
        } );
    }
}
//Добавляем будильник после формы
function add_alarm() {
    title = $('#new_title').val();
    var values = '';
    //alert($('#pr_more').val().length);
    if($('#pr_more').val().length>0) values += $('#pr_more').val() + '||'; else values += 'xx||';
    if($('#pr_less').val().length>0) values += $('#pr_less').val() + '||'; else values += 'xx||';
    if($('#tcy_more').val().length>0) values += $('#tcy_more').val() + '||'; else values += 'xx||';
    if($('#tcy_less').val().length>0) values += $('#tcy_less').val() + '||'; else values += 'xx||';
    if($('#yai_more').val().length>0) values += $('#yai_more').val() + '||'; else values += 'xx||';
    if($('#yai_less').val().length>0) values += $('#yai_less').val() + '||'; else values += 'xx||';
    if($('#gi_more').val().length>0) values += $('#gi_more').val() + '||'; else values += 'xx||';
    if($('#gi_less').val().length>0) values += $('#gi_less').val() + '||'; else values += 'xx||';
    if($('#yi_more').val().length>0) values += $('#yi_more').val() + '||'; else values += 'xx||';
    if($('#yi_less').val().length>0) values += $('#yi_less').val() + '||'; else values += 'xx||';
    if($('#ri_more').val().length>0) values += $('#ri_more').val() + '||'; else values += 'xx||';
    if($('#ri_less').val().length>0) values += $('#ri_less').val() + '||'; else values += 'xx||';
    if($('#ybl_more').val().length>0) values += $('#ybl_more').val() + '||'; else values += 'xx||';
    if($('#ybl_less').val().length>0) values += $('#ybl_less').val() + '||'; else values += 'xx||';
    if($('#alexa_more').val().length>0) values += $('#alexa_more').val() + '||'; else values += 'xx||';
    if($('#alexa_less').val().length>0) values += $('#alexa_less').val() + '||'; else values += 'xx||';
    if($('#domain_more').val().length>0) values += $('#domain_more').val() + '||'; else values += 'xx||';
    if($('#domain_less').val().length>0) values += $('#domain_less').val() + '||'; else values += 'xx||';
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Добавляется будильник ' + title);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            if(req.responseText=='0') {
                alert("Такой элемент уже есть");
                return;
            }
            $("#panel_contain").append(req.responseText);
            $("#tablesorter").trigger("update");
            $("#tablesorter").trigger("appendCache")
            row_highlight();
            $('#debugger').html('');
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'add_alarm_do',
        title: title,
        values: values
    } );
}
//Сохраняем будильник после формы
function save_alarm(id) {
    title = $('#new_title').val();
    var values = '';
    //alert($('#pr_more').val().length);
    if($('#pr_more').val().length>0) values += $('#pr_more').val() + '||'; else values += 'xx||';
    if($('#pr_less').val().length>0) values += $('#pr_less').val() + '||'; else values += 'xx||';
    if($('#tcy_more').val().length>0) values += $('#tcy_more').val() + '||'; else values += 'xx||';
    if($('#tcy_less').val().length>0) values += $('#tcy_less').val() + '||'; else values += 'xx||';
    if($('#yai_more').val().length>0) values += $('#yai_more').val() + '||'; else values += 'xx||';
    if($('#yai_less').val().length>0) values += $('#yai_less').val() + '||'; else values += 'xx||';
    if($('#gi_more').val().length>0) values += $('#gi_more').val() + '||'; else values += 'xx||';
    if($('#gi_less').val().length>0) values += $('#gi_less').val() + '||'; else values += 'xx||';
    if($('#yi_more').val().length>0) values += $('#yi_more').val() + '||'; else values += 'xx||';
    if($('#yi_less').val().length>0) values += $('#yi_less').val() + '||'; else values += 'xx||';
    if($('#ri_more').val().length>0) values += $('#ri_more').val() + '||'; else values += 'xx||';
    if($('#ri_less').val().length>0) values += $('#ri_less').val() + '||'; else values += 'xx||';
    if($('#ybl_more').val().length>0) values += $('#ybl_more').val() + '||'; else values += 'xx||';
    if($('#ybl_less').val().length>0) values += $('#ybl_less').val() + '||'; else values += 'xx||';
    if($('#alexa_more').val().length>0) values += $('#alexa_more').val() + '||'; else values += 'xx||';
    if($('#alexa_less').val().length>0) values += $('#alexa_less').val() + '||'; else values += 'xx||';
    if($('#domain_more').val().length>0) values += $('#domain_more').val() + '||'; else values += 'xx||';
    if($('#domain_less').val().length>0) values += $('#domain_less').val() + '||'; else values += 'xx||';
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Сохраняется будильник ' + title);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            if(req.responseText=='0') {
                alert("Такой элемент уже есть");
                return;
            }
            $("#element_" + id).replaceWith(req.responseText);
            $("#tablesorter").trigger("update");
            $("#tablesorter").trigger("appendCache")
            row_highlight();
            $('#debugger').html('');
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'edit_alarm_do',
        title: title,
        values: values,
        id: id
    } );
}
//Удаляем будильник после формы
function alarm_delete(id) {
    if(confirm("Вы действительно хотите удалить этот будильник?")) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                if(req.responseText=='0') {
                    alert("Этот элемент не принадлежит вам");
                    return;
                }
                $("#element_" + id).remove();
                $('#debugger').html('');
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'delete_alarm',
            id: id
        } );
    }
}
//Функция перемещения сайта на одну позицию
function el_position(direction,eltype,rowid){
    if(direction=='up') {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                $("#element_"+rowid).prev().before($("#element_"+rowid));
                $('#element_'+rowid + ' .poslinks').html(req.responseJS.poslinks);
                $('#element_'+rowid).next().children('td').children('div.poslinks').html(req.responseJS.poslinks2);
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'elpos_update',
            id: rowid,
            direction: 'up',
            eltype: eltype
        } );
    }

    if(direction=='down') {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" />&nbsp;');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                $("#element_"+rowid).next().after($("#element_"+rowid));
                $('#element_'+rowid + ' .poslinks').html(req.responseJS.poslinks);
                $('#element_'+rowid).prev().children('td').children('div.poslinks').html(req.responseJS.poslinks2);
                $("#tablesorter").trigger("update");
                $("#tablesorter").trigger("appendCache")
                row_highlight();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'elpos_update',
            id: rowid,
            direction: 'down',
            eltype: eltype
        } );
    }
}


/**
 *Общие функции
**/


//Вырезание тегов
function strip_tags(str) {
    return str.replace(/&lt;\/?[^&gt;]+&gt;/gi, "");
}
//Конец загрузки данных
function loader_end() {
    //	$("#tablesorter").tablesorter({textExtraction: 'complex', widgets: ['zebra','repeatHeaders']});
    $("#tablesorter").tablesorter({
        textExtraction: 'complex',
        widgets: ['zebra']
    });
    //	$('.tablesorter tbody tr').click(function(){if($(this).hasClass('selected')) $(this).removeClass('selected'); else $(this).addClass('selected');});
    $('#nav').droppy();
    $('#debugger').html('');
    row_highlight();
    make_options();
}
//Получение кукисов
function get_cookie(name)
{
    cookie_name = name + "=";
    cookie_length = document.cookie.length;
    cookie_begin = 0;
    while (cookie_begin < cookie_length)
    {
        value_begin = cookie_begin + cookie_name.length;
        if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
        {
            var value_end = document.cookie.indexOf (";", value_begin);
            if (value_end == -1)
            {
                value_end = cookie_length;
            }
            return unescape(document.cookie.substring(value_begin, value_end));
        }
        cookie_begin = document.cookie.indexOf(" ", cookie_begin) + 1;
        if (cookie_begin == 0)
        {
            break;
        }
    }
    return '';
}
//Установка кукисов
function setCookie (name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
}
//Сохранение пользовательских настроек
function settings_save() {
    new_pass = '';
    if($('#user_password').val().length>0) {
        if($('#user_password').val() != $('#user_password2').val()) {
            alert('Введенные пароли не совпадают');
            return false;
        }
        new_pass = $('#user_password').val();
    }
    rownum = $('#rownum').val();
    email = $('#email').val();
    tocheck = '';
    $('.tocheck').each(function(){
        if($(this).is(':checked')) {
            tocheck += ','+$(this).attr('id');
        }
    });
    if($('#send_alarms').is(':checked')) send_alarms = "1"; else send_alarms = "0";
    yandex_method = $('#yandex_method').val();
    yandex_request = $('#yandex_request').val();
    antigate_key = $('#antigate_key').val();
    google_key = $('#google_key').val();
    proxies = $('#proxies').val();
    sites_per_query = $('#sites_per_query').val();
    time_between_checks = $('#time_between_checks').val();

    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;"> Сохранение настроек');
    window.current_request=$.ajax({
        type: "POST",
        url: "ajax.php",
        async: false,
        dataType: "json",
        data: {
            action: 'settings_save',
            rownum: rownum,
            new_pass: new_pass,
            email: email,
            tocheck: tocheck,
            yandex_method: yandex_method,
            yandex_request: yandex_request,
            antigate_key: antigate_key,
            google_key: google_key,
            proxies: proxies,
            sites_per_query: sites_per_query,
            time_between_checks: time_between_checks,
            send_alarms: send_alarms
        },
        success: function(data){
            main_load('sites');
            $('#debugger').html('');
        }
    });

}


/**
 *Работа с вкладками внутри панели
**/


//Добавление панели с параметрами для отображения
function add_panel(panel) {
    str = '';
    $('.adcol').each(function() {
        if($(this).is(':checked')) str += $(this).attr('id') + ',';
    });
    ptitle = $('#panel_title').val();
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Сохраняется панель ' + ptitle);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            $('#debugger').html('');
            $('.dleft .dright span').html(req.responseText);
            main_load(panel);
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'panel_add_do',
        title: ptitle,
        cols: str,
        panel: panel
    } );
}
//Сохранение и удаление панели с подтверждением
function save_panel(panel,del) {
    str = '';
    $('.adcol').each(function() {
        if($(this).is(':checked')) str += $(this).attr('id') + ',';
    });
    ptitle = $('#panel_title').val();
    if(!del || confirm('Вы уверены, что хотите удалить эту вкладку?')) {
        $.fancybox.close();
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Сохраняется панель ' + ptitle);
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                $('.dleft .dright span').html(req.responseText);
                if(!del) main_load(panel); else main_load('sites');
            }
        }
        req.open(null, 'ajax.php', true);
        if(del) req.send( {
            action: 'panel_save',
            del: del,
            panel: panel
        } ); else req.send( {
            action: 'panel_save',
            title: ptitle,
            cols: str,
            panel: panel
        } );
    }
}
//Драг'н'дроп для строк
function mydnd_sorting(){
    // Initialise the table 2
    $("#sortingcols").tableDnD({
        onDragClass: "myDragClass",
        dragHandle: "dragHandle"
    });

    $("#sortingcols tr").hover(function() {
        $(this).addClass("over");
        $(this.cells[1]).addClass('showDragHandle');
    }, function() {
        $(this).removeClass("over");
        $(this.cells[1]).removeClass('showDragHandle');
    });
}
//Показать график
function showGraph(sites,field) {
    param = (field) ? field : 0;
    var so = new SWFObject("./templates/amline.swf", "amline", "100%", "500px", "8", "#FFFFFF");
    so.addVariable("path", "./templates/");
    so.addVariable("settings_file", encodeURIComponent("./templates/amline_settings.xml"));
    so.addVariable("data_file", encodeURIComponent("./ajax.php?action=graph&sites="+sites+"&field="+param));
    so.write("graph_canvas");
    return false;
}
//Добавить колонку в базу
function add_column() {
    title = $('#col_title').val();
    type = $('#col_type').val();
    $.fancybox.close();
    $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Добавляется столбец ' + title);
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
            $('#debugger').html('');
        }
    }
    req.open(null, 'ajax.php', true);
    req.send( {
        action: 'column_add',
        title: title,
        type: type
    } );
}
//Удалить колонку из базы
function del_column(id) {
    if(confirm('Вы действительно хотите удалить этот столбец?')) {
        $('#debugger').html('<img src="images/loading_white.gif" alt="" border="0" style="vertical-align: middle;" /> Удаляется столбец');
        var req = new JsHttpRequest();
        req.onreadystatechange = function() {
            if (req.readyState == 4) {
                $('#debugger').html('');
                $('#list_'+id).remove();
            }
        }
        req.open(null, 'ajax.php', true);
        req.send( {
            action: 'column_remove',
            cid: id
        } );
    }
}
//Показываем/скрываем строки настройки парсинга Яндекса
function showhide_ya() {
    if($('#yandex_method').val()=='XML') {
        $('.yasimple').hide();$('.yaxml').show();
    } else {
        $('.yasimple').show();$('.yaxml').hide();
    }
}

//По загрузке главной страницы делаем это:
var viewportwidth;
var viewportheight;
if (typeof window.innerWidth != 'undefined')
{
    viewportwidth = window.innerWidth,
        viewportheight = window.innerHeight
} else if (typeof document.documentElement != 'undefined'
    && typeof document.documentElement.clientWidth !=
    'undefined' && document.documentElement.clientWidth != 0)
{
    viewportwidth = document.documentElement.clientWidth,
        viewportheight = document.documentElement.clientHeight
} else {
    viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
        viewportheight = document.getElementsByTagName('body')[0].clientHeight
}
document.getElementById('main').style.width = viewportwidth + 'px';
document.getElementById('main').style.height = (viewportheight-87) + 'px';
var maxheight = viewportheight-229;
$.tablesorter.addWidget({
    // give the widget a id
    id: "repeatHeaders",
    // format is called when the on init and when a sorting has finished
    format: function(table) {
        // cache and collect all TH headers
        if(!this.headers) {
            var h = this.headers = [];
            $("thead th",table).each(function() {
                h.push(
                    "<th>" + $(this).text() + "</th>"
                    );

            });
        }

        // remove appended headers by classname.
        $("tr.repated-header",table).remove();

        // loop all tr elements and insert a copy of the "headers"
        for(var i=0; i < table.tBodies[0].rows.length; i++) {
            // insert a copy of the table head every 10th row
            if((i%21) == 20) {
                $("tbody tr:eq(" + i + ")",table).before(
                    $("<tr></tr>").addClass("repated-header").html(this.headers.join(""))

                    );
            }
        }

    }
});
$('#maintable').height(maxheight);

var myTextExtraction = function(node)
{
    return strip_tags(node);
}