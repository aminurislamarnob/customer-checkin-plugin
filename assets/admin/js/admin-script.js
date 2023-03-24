jQuery(document).ready( function($) {

    jQuery(".customer-checkin-form").on( "submit", function(e) {
        e.preventDefault();
        // var dataString = $(this).serialize();

        var customer_email = jQuery(this).find( "input[name=customer_email]" ).val();
        var customer_name = jQuery(this).find( "input[name=customer_name]" ).val();
        var order_id = jQuery(this).find( "input[name=order_id]" ).val();
        var count = jQuery(this).find( "input[name=count]" ).val();
        var nonce = jQuery(this).find( "input[name=nonce]" ).val();
        var dataCollections = {action: 'woocusch_checkin', customer_email : customer_email, customer_name: customer_name, order_id: order_id, count: count, nonce: nonce};
        var loadingIcon = woocuschAjax.PLUGIN_PATH + "/assets/admin/img/loading.gif";
        //
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : woocuschAjax.ajaxurl,
            data : dataCollections,
            beforeSend: function() {
                jQuery('#woocusch_loader').show();
            },
            success: function(response) {
                if(response == 1) {
                    jQuery('#woocusch_loader').hide();
                    Swal.fire(
                        'Success',
                        'Customer successfully checked-in!',
                        'success'
                    ).then(function(){
                        location.reload();
                    });
                }else {
                    jQuery('#woocusch_loader').hide();
                    Swal.fire(
                        'Failed',
                        'Customer check-in failed!',
                        'error'
                    ).then(function(){
                        location.reload();
                    });
                }
            }
        })

    })

})