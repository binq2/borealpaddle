jQuery(document).ready(function($){
	$(function() {

		$('input[type="checkbox"]').styler();

		$('input[type="radio"]').styler();

		var widget_select = $('.widget').find('select');
		var content_select = $('.site-content').find('select:not(#rating)');
        var qv_select = $('#jckqv').find('select:not(#rating)');

		widget_select.each(function(){
			$(this).styler({
				selectSearchPlaceholder: 'Search...',
				selectSearchNotFound: 'Nothing found...'
			});
		});

		content_select.each(function(){
			$(this).styler({
				selectSearchPlaceholder: 'Search...',
				selectSearchNotFound: 'Nothing found...'

			});
		});

        qv_select.each(function(){
            $(this).styler({
                selectSearchPlaceholder: 'Search...',
                selectSearchNotFound: 'Nothing found...'
            });
        });

		$('select').not('#rating').on('change', function(){
			$('select').not('#rating').trigger('refresh');
		});


        jQuery(document).on('mfpUpdateStatus', function(e){
            var qv_select = $('select:not(#rating)');
            qv_select.each(function(){
                $(this).styler({
                    selectSearchPlaceholder: 'Search...',
                    selectSearchNotFound: 'Nothing found...'
                });
            });

            $('select').not('#rating').on('change', function(){
                $('select').not('#rating').trigger('refresh');
            });

        });

	})
});


