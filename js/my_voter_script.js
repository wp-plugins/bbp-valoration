jQuery(document).ready( function() {

	      jQuery('.form1').submit( function (e) {
				var topicserial = jQuery(this).serialize();
				console.log(topicserial);
			    jQuery.ajax({
			            type: 'post',
			            dataType : 'json',
			            context: this,
			            url: myAjax.ajaxurl,
			            data: {action: 'bbpv_user_vote', topic: topicserial},
			        success: function(response) {
			            if(response.type == "success") {
			               totales=response.vote_count

			               console.log("ifsuccess: "+response.vote_count)
			               jQuery('.total-likes span#numberoflikes').text(totales)
			               jQuery('#totales').text(totales)
			               jQuery(".bbpv_mess #successtext").show()
			               jQuery(".thumbsup").attr('disabled','disabled')
			            }
			            else {
			               jQuery(".bbpv_mess #errortext").show()
			            }
         			}
        		});
        		e.preventDefault();
	      });
	      var url = WPURLS.pluginurl;
	      var div = jQuery('<div />',
	      	 { id:'thumbsup-div' }).appendTo(jQuery('.bbp-reply-position-1 .bbp-reply-content'));
	      var divimg = jQuery('<div />',
	      	 { id:'thumbsup-img' }).appendTo(jQuery('#thumbsup-div'));
		  var img = jQuery('<img />',
             { id: 'thumbs-up',
               src: ''+url+'img/Thumbs_Up_30.png', 
               alt:'thumbs_up'})
              .appendTo(jQuery('#thumbsup-img'));
          total = jQuery('#numberoflikes').text();
          jQuery('#thumbsup-div').append('<div id="totales">'+total+'</div>');
})

