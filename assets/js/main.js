
    var ytplayer;

    function onYouTubePlayerReady(playerId) {
        ytplayer = document.getElementById(playerId);
    }

    function play() {
        console.log('Play: '+ytplayer);
        if (ytplayer) {
            ytplayer.playVideo();
        }
    }
	
	var RHYME  = {};

    var params   = { allowScriptAccess: "always" };
    var atts     = { id: "ytplayer" };

	jQuery(function($) {

		RHYME.player         = $("#player");
		RHYME.queue          = $("#queue");
        RHYME.progressParent = $("#player-progressbar");
        RHYME.progressBar    = $("#progress-move-bar");
        RHYME.scrubHandle    = $("#progress-scrub-handle");

		$('#q').autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "api",
					dataType: "json",
					data: {
						q: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								thumbnail: item.thumbnail,
								label: item.label,
								value: item.value
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				addToQueue(ui.item.value, ui.item.thumbnail, ui.item.label, ui.item.duration);
			}
		});

        RHYME.queue.on("click", ".queue-item:not('.selected')", function(e) {
            var $this = $(this);
            var $id   = $this.data('youtube-id');
            $('.queue-item').removeClass('selected');
            $this.addClass('selected');

            playVideo($id);
        });

        function playVideo(code) {
            swfobject.embedSWF("http://www.youtube.com/v/"+code+"?enablejsapi=1&playerapiid=ytplayer&version=3", "ytplayer", "400", "250", "8", null, null, params, atts);
            play();
        }


		/**
		 * Add To Queue
		 *
		 * Add a video to the playlist Queue
		 *
		 */
		function addToQueue(videoId, thumbnail, title, duration) {
			if (RHYME.queue.length) {

                var find = $(".queue-item").filter('[data-youtube-id = "'+videoId+'"]');

                if (find.length == 0) {
                    var temp  = $("<li class='queue-item' data-youtube-id='"+videoId+"'><a href='javascript:void(0);' class='delete'></a></li>");
                    var thumb = $("<span class='queue-item-thumbnail'><img src='"+thumbnail+"' height='70' width='70'></span>");
                    var meta  = $("<span class='queue-item-meta'><span class='queue-item-title'>"+title+"</span><span class='queue-item-duration'>"+duration+"</span></span>");

                    thumb.prependTo(temp);
                    meta.appendTo(temp);

                    temp.prependTo('#queue-list');
                }

			}
		}

        $('.queue-item .delete').bind("click", function(e) {
            var $id = $(this).parent().data('youtube-id');
            removeFromQueue($id);
            e.preventDefault();
        });

		/**
		 * Remove From Queue
		 *
		 * Remove a video from the playlist Queue
		 *
		 */
		function removeFromQueue(videoId) {

			AttentionBox.showMessage("Are you sure you want to remove this playlist item?", 
			{ 
				modal   : true,
				buttons : 
				[
					{ caption : "Delete It!" },
					{ caption : "Don't Delete It!", cancel: true }
				],
				callback: function(action, buttons)
				{
					if (action != "CANCELLED") {
                        $(".queue-item").filter('[data-youtube-id = "'+videoId+'"]').remove();
					}
				}
			});

		}

		/**
		 * Skip To Item
		 *
		 * Skip to an item in the playlist Queue
		 *
		 */
		function skipToItem(videoId) {

		}

	});