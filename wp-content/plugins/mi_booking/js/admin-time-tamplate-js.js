$ = jQuery.noConflict();
$(document).ready(function() {
    var $mi_booking = {
        thisis:0,
        val:0
    };
    $('.cost_input').keydown(function(e){        
        var char = String.fromCharCode(e.keyCode || e.charCode);
        var keyAllowed = true;
        switch(e.keyCode)
        {
            case 8: keyAllowed = false; break;
            case 46: keyAllowed = false; break;
            case 110: keyAllowed = false; break;    
        }
        if (e.keyCode >= 96 && e.keyCode <= 105)
        {
            keyAllowed = false;
        }
        if (e.keyCode >= 37 && e.keyCode <= 40)
        {
            keyAllowed = false;
        }
        var patt = /[0-9.]/;
        if(!patt.test(char))
        {
            if(keyAllowed)
            {
                e.preventDefault();
            }            
        }
    });
    $('.time-holiday input').keydown(function(e){ 
        e.preventDefault();
    });
    $('.add-time-tamplates span').click(function(){
        $('.add-time-tamplates-inputs').toggle('slow');
    });
    $('.add-date-tamplate span').click(function(){
        $('.add-date-tamplate-inputs').toggle('slow');
    });
    $('.time-holiday input[type=text]').each(function(){        
        $(this).DatePicker({
            format: 'Y-m-d',
            date: $(this).data('timeformated'),
            current: $(this).data('timeformated'),
            prev:'<i class="fa fa-arrow-left"></i>',
            next:'<i class="fa fa-arrow-right"></i>',
            starts: MIBookingOptions.starts,
            position: 'r',
            locate: MIBookingOptions.inlocale,
            onBeforeShow: function(){
                $mi_booking.thisis = this;
                $mi_booking.val = $(this).data('timeformated');
                $(this).DatePickerSetDate($(this).data('timeformated'), true);
            },
            onChange: function(formated, dates){
                if($mi_booking.val !== formated)
                {
                    var date = '';
                    var split = formated.split('-');
                    var year = split[0];
                    var month = split[1];
                    var day = split[2];
                    var partsFormat = MIBookingOptions.format.split(/(\W+)/);
                    for(var i = 0; i < partsFormat.length; i++)
                    {
                        switch(partsFormat[i])
                        {
                            case 'd': date += day;break;
                            case 'D': date += MIBookingOptions.inlocale.daysShort[parseInt(day, 10)-1];break;
                            case 'm': date += month;break;
                            case 'M': date += MIBookingOptions.inlocale.monthsShort[parseInt(month, 10)-1];break;
                            case 'n': date += parseInt(month, 10).toString();break;
                            case 'j': date += parseInt(day, 10).toString();break;
                            case 'F': date += MIBookingOptions.inlocale.months[parseInt(month, 10)-1];break;
                            case 'y': date += year.substring(2,2);break;
                            case 'Y': date += year;break;
                            default:
                                date +=partsFormat[i];
                                break;
                        }
                    }
                    $($mi_booking.thisis).val(date);
                    $($mi_booking.thisis).DatePickerHide();
                }
            }
        });
    });
    $(':checkbox').iphoneStyle();
    $('.container').each(function(){
        $(this).attr('title', $(this).find('input').attr('title'));
    });
});
