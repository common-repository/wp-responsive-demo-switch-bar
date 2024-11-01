jQuery(document).ready(function($) {

    /***** Colour picker *****/

    $('.colorpicker').hide();
    $('.colorpicker').each( function() {
        $(this).farbtastic( $(this).closest('.color-picker').find('.color') );
    });

    $('.color').click(function() {
        $(this).closest('.color-picker').find('.colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('.colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });


    /***** Uploading images *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );
        var preview_id = button_id.replace( '_button', '_preview' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
          file_frame.open();
          return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'uploader_title' ),
          button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
          },
          multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          attachment = file_frame.state().get('selection').first().toJSON();
          jQuery("#"+field_id).val(attachment.id);
          
          if( attachment.sizes.thumbnail.url ) {
             jQuery("#"+preview_id).attr('src','');
            jQuery("#"+preview_id).attr('src',attachment.sizes.thumbnail.url);
          }
        });

        // Finally, open the modal
        file_frame.open();
    }

    jQuery('.image_upload_button').on('click',function(e) {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.image_delete_button').on('click',function(e) {
        jQuery(this).closest('td').find( '.image_data_field' ).val( '' );
        jQuery( '.image_preview' ).remove();
        return false;
    });


    /***** Navigation for settings page *****/

    // Make sure each heading has a unique ID.
    jQuery( 'ul#settings-sections.tabrow' ).find( 'a' ).each( function ( i ) {
        var id_value = jQuery( this ).attr( 'href' ).replace( '#', '' );
        jQuery( 'h2:contains("' + jQuery( this ).text() + '")' ).attr( 'id', id_value ).addClass( 'section-heading' );
        //always show first div only
        jQuery( '#plugin_settings  h2, #plugin_settings form > p:not(".submit"), #plugin_settings table' ).hide()
        var first=jQuery( '#plugin_settings #settings-sections  a.current' ).attr('href');
            toShow = first.replace( '#', '', first );
            jQuery( 'h2#' + toShow ).show().nextUntil( 'h2.section-heading', 'p, table, table p' ).show();
    });

    // Create nav links for settings page
    jQuery( '#plugin_settings .tabrow a.tab' ).click( function ( e ) {
        // Move the "current" CSS class.
        jQuery( this ).parents( '.tabrow' ).find( '.current' ).removeClass( 'current' );
        jQuery( this ).addClass( 'current' );
        jQuery( this ).parent().addClass( 'current' );
        // If "All" is clicked, show all.
        if ( jQuery( this ).hasClass( 'all' ) ) {
            jQuery( '#plugin_settings > h2, #plugin_settings form p, #plugin_settings table.form-table, p.submit' ).show();

            return false;
        }

        // If the link is a tab, show only the specified tab.
        var toShow = jQuery( this ).attr( 'href' );

        // Remove the first occurance of # from the selected string (will be added manually below).
        toShow = toShow.replace( '#', '', toShow );
        

        jQuery( '#plugin_settings  h2.section-heading, #plugin_settings form > p:not(".submit"), #plugin_settings table' ).hide();
        jQuery( 'h2#' + toShow ).show().nextUntil( 'h2.section-heading', 'p, table, table p' ).show();

        return false;
    });

    jQuery(".chb").on('change',function(e) {
    var checked = jQuery(this).is(':checked');
    var currentid=jQuery(this).attr('id');
    var splitid=currentid.split("_");
    var previd=splitid[0];
    var nextno=splitid[1];
    if(parseInt(nextno)==1)
        nextno=0
    else
        nextno=1;

    var anotherid=previd+"_"+nextno;
    //alert(anotherid)
    jQuery("#"+anotherid).prop('checked',false);
    if(checked) {
        jQuery(this).prop('checked',true);
    }
    });
    jQuery('#product_type').on('change',function(){
        var currentval=$(this).val();
        if(currentval=='premium')
        {
            $('#metaproduct_price').addClass('display-price');
            return false;
        }
        else
        {
           $('#metaproduct_price').removeClass('display-price'); 
        }
    })
});