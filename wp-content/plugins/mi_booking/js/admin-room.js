$ = jQuery.noConflict();
$(document).ready(function() {
    var jVal = {
    "check" : function() {
                var ele1 = $("#add-room-name");
                var ele2 = $('#add-room-disp-days');
                var patt1 = /^(.){1,}$/i;
                var patt2 = /^(\d){1,}$/i;

                if(!patt1.test(ele1.val()))  {
                        jVal.errors = true;
                        ele1.parents('.form-group').addClass('has-error');                        
                } 
                else
                {
                    if(!patt2.test(ele2.val()))
                    {
                        ele2.parents('.form-group').addClass('has-error');
                        ele1.parents('.form-group').removeClass('has-error');
                    }
                    else
                    {
                       ele1.parents('.form-group').removeClass('has-error');
                       ele2.parents('.form-group').removeClass('has-error'); 
                    }                    
                }
        },

        "sendIt" : function (){
                if(!jVal.errors) {
                    $("#add-room-form").submit();
                }
        }
    };
    jQuery("html").on('click',".add-room-inputs button" , function(){
        jVal.errors = false;
        jVal.check();

        jVal.sendIt();
        if(jVal.errors) {
        return false;
        }
    });
    $("html").on('keyup',"#add-room-name, #add-room-disp-days" , function(){
        jVal.errors = false;
        jVal.check();
    });
    $("html").on('change',"#add-room-name, #add-room-disp-days" , function(){
        jVal.errors = false;
        jVal.check();
    });
    $('#add-room-name, #add-room-disp-days').mouseout(function(){
        jVal.errors = false;
        jVal.check();   
    });
    $('#add-room-name, #add-room-disp-days').click(function(){
        jVal.errors = false;
        jVal.check();   
    });
    $('.add-room span').click(function(){
        $('.add-room-inputs').toggle('slow');
    });
    $("button[name='delete_room']").click(function(e){
        e.preventDefault();
        var isDelete = confirm(deleteMessage);
        if(isDelete)
        {
            $(this).parent().append('<input type="hidden" name="confirm_del" value="delete"/>');
            $(this).parents('form').submit();
        }
    });
    $(':checkbox').iphoneStyle();
});


