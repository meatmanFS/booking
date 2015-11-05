$ = jQuery.noConflict();
$(document).ready(function() {
    $('#mi_booking_sortable').pbTable({
            selectable: false,
            sortable:true,
            toolbar:{
                enabled:true,
                filterBox:true,
                buttons:[]
            },
            pagination:{
                enabled: true,
                pageSize: mi_pagesize
            },
            locale:{ 
                placeholderSearchBox:mi_placeholder,
                msgNoData:mi_nodata,
            }
    });
    $('#search-mi_booking_sortable').bind('input', function(){
        var val = $(this).val();
        if(val !== '')
        {
            $('#pagination-row').hide();
        }
        else
        {
            $('#pagination-row').show();
        }
    });
    $('tr[name=filaMsgSinDatos] td').attr('colspan',9);
    var num_rows = $('.features-table tbody tr').length;
    var sortValidation = new Array(num_rows);
    for(var i = 0; i < sortValidation.length; i++)
        sortValidation[i] = [0,0];
    $('body').on('click', 'thead .verification', function(){
        $('td.dash-control h2').each(function(index){            
            sortValidation[index][0] = $(this).parents('tr');
            sortValidation[index][1] = $(this).text();
        });
        if(!$(this).hasClass('sorttable_sorted') && !$(this).hasClass('sorttable_sorted_reverse'))
        {
            sortValidation.sort(sIncrease);
            $(sortValidation).each(function(index){
                $(this[0]).parent().append($(this[0]));
                $(this[0]).attr('data-page', Math.ceil((index+1)/mi_pagesize));
                if( Math.ceil((index+1)/mi_pagesize) === 1)
                {
                    $(this[0]).css(
                        'display','table-row'
                    );
                }
                else
                {
                    $(this[0]).css(
                        'display','none'
                    );
                }
            });
            $(this).addClass('sorttable_sorted_reverse');
            $(this).append('<span id="sorttable_sortrevind">&nbsp;▴</span>');
        }
        else if($(this).hasClass('sorttable_sorted'))
        {
            sortValidation.sort(sIncrease);
            $(sortValidation).each(function(index){
                $(this[0]).parent().append($(this[0]));
                $(this[0]).attr('data-page', Math.ceil((index+1)/mi_pagesize));
                if( Math.ceil((index+1)/mi_pagesize) === 1)
                {
                    $(this[0]).css(
                        'display','table-row'
                    );
                }
                else
                {
                    $(this[0]).css(
                        'display','none'
                    );
                }
                
            });
            $(this).find('#sorttable_sortfwdind').remove();
            $(this).removeClass('sorttable_sorted');
            $(this).addClass('sorttable_sorted_reverse');
            $(this).append('<span id="sorttable_sortrevind">&nbsp;▴</span>');
        }
        else if($(this).hasClass('sorttable_sorted_reverse'))
        {
            sortValidation.sort(sDecrease);
            $(sortValidation).each(function(index){
                $(this[0]).parent().append($(this[0]));
                $(this[0]).attr('data-page', Math.ceil((index+1)/mi_pagesize));
                if( Math.ceil((index+1)/mi_pagesize) === 1)
                {
                    $(this[0]).css(
                        'display','table-row'
                    );
                }
                else
                {
                    $(this[0]).css(
                        'display','none'
                    );
                }
            });
            $(this).find('#sorttable_sortrevind').remove();
            $(this).removeClass('sorttable_sorted_reverse');
            $(this).addClass('sorttable_sorted');
            $(this).append('<span id="sorttable_sortfwdind">&nbsp;▾</span>');
        }
    });     
    
    function sIncrease(i, ii) { // asc
        if (i[1] > ii[1])
        return 1;
        else if (i[1] < ii[1])
        return -1;
        else
        return 0;
    }
    function sDecrease(i, ii) { // desc
        if (i[1] > ii[1])
        return -1;
        else if (i[1] < ii[1])
        return 1;
        else
        return 0;
    }
});
$(function(){	
	$('body').on('click', '.panel-heading span.filter', function(e){
		var $this = $('#mi_booking_sortable-pbToolbar');		
		$this.slideToggle();
		if($this.css('display') != 'none') {
                    $this.find('#search-mi_booking_sortable').focus();
		}
	});
});  
     