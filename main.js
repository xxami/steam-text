
/*function preload(arr) {
	$(arr).each(function() {
		$('<img/>')[0].src = this;
	});
}*/

var ErrorCode = {
	'NONE': 0,
	'NO_EMOTE': 1,
	'PRIVATE': 2,
};

var emoticons = [];
var emoticon_fg = null;
var emoticon_bkg = null;

function set_emoticon_bkg(emote) {
	emoticon_bkg = emote;
	$('#bkg_selected').html(emote);
	update_emoticons();
}

function set_emoticon_fg(emote) {
	emoticon_fg = emote;
	$('#fg_selected').html(emote);
	update_emoticons();
}

function update_emoticons() {
	var text = $('#input_text').val().toUpperCase().replace(/[^A-Z0-9!]/g, '');
	var font = $('#font_select').val();
	if (text == '' || emoticons.length < 2 || !emoticon_fg || !emoticon_bkg || font == '') return;
	
}

function load_emoticons(emotes) {
	if (emotes.length < 2) return;
	var imgs = [];
	var html_loaded = '';
	var html_bkg = '';
	var html_fg = '';
	for (var i = 0; i < emotes.length; i++) {
		imgs.push('img/' + emotes[i].replace(/:/g, '-') + '.png');
		html_loaded += '<img src=\"img/' + emotes[i].replace(/:/g, '-') + '.png\" />';
		html_bkg += '<a href=\"javascript:set_emoticon_bkg(\'' + emotes[i] + '\')\">;<img src=\"img/' + emotes[i].replace(/:/g, '-') + '.png\" /></a>';
		html_fg += '<a href=\"javascript:set_emoticon_fg(\'' + emotes[i] + '\')\">;<img src=\"img/' + emotes[i].replace(/:/g, '-') + '.png\" /></a>';
	}
	/*preload(imgs);*/
	$('#emoticons_loaded').html(html_loaded);
	$('#select_bkg').html(html_bkg);
	$('#select_fg').html(html_fg);
}

function unload_emoticons() {
	emoticons = [];
	emoticon_fg = null;
	emoticon_bkg = null;
	$('#emoticons_loaded').html('');
	$('#select_bkg').html('');
	$('#select_fg').html('');
	$('#input_text').val('');
	update_emoticons();
}

function validate_input() {
	$('#input_text').val($('#input_text').val().toLowerCase().replace(/[^a-z0-9!]/g, ''))
}

$(document).ready(function() {
	$('#js_disabled').remove(); /* comment out when styling pls */
	
	for (var i = 0; i < fonts.length; i++) {
		$('#font_select').html($('#font_select').html() + '<option value=\"' + fonts[i] + '\">' + fonts[i] + '</option>');
	}
	
	$('#submit_steamid').click(function() {
		$('#submit_steamid').attr('disabled', 'disabled');
		/* display loading against submit button */
		$.ajax({
			url: "get_emoticons.php?steamid=" + $('#input_steamid').val(),
			dataType: 'json',
			timeout: 10000
		}).success(function(data) {
			$('#submit_steamid').removeAttr('disabled');
			/* remove loading display */
			if (data['error'] == ErrorCode.NONE) {
				// OK
				$('#status_steamid').html('');
				emoticons = data['emoticons']
				load_emoticons(emoticons);
				console.log(data);
			}
			else {
				unload_emoticons();
				switch (data['error']) {
					case ErrorCode.NO_EMOTE:
						$('#status_steamid').html('Could not find 2+ known emoticons! :(');
						break;
					case ErrorCode.PRIVATE:
						$('#status_steamid').html('Inventory for this steam-id is private! :(');
						break;
					default:
						$('#status_steamid').html('Something went wrong, sorry! :(');
				}
			}
		})
		.error(function() {
			unload_emoticons();
			$('#status_steamid').html('Something went wrong, sorry! :(');
			$('#submit_steamid').removeAttr('disabled');
			/* remove loading display */
		});
	});
	
});