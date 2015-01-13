<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="fonts-min.js"></script>
	<meta charset="utf-8">
</head>
<body>
	<!-- there should be a pretty header or logo or title here, maybe with a description 
		 i'm not too sure! i think it might be nice if it had a title made out of
		 the output that is created (ie. emoticon art logo) but also some text
		 is good. i will let u think what u decide!
	-->

	<header>
		<h1>steamtext</h1>
	</header>

	<!-- this should show fixed box on the top that says please enable js to work -->
	<div id="js_disabled">Please enable JavaScript to use this application! :)</div>
	<div>
	<label>Enter a Steam Community ID or a List of emoticons to use</label>
	<div>
	</div>
	<label>example: <span>http://steamcommunity.com/id/msiao/</span></label>
	<div></div>
	<label>example: <span>:zgbunny::B1::mastermedal::meso:</span></label>
	<div class="input_steam">
		<input type="text" name="steamid" id="input_steamid">
		<input type="submit" id="submit_steamid" value="Load">
		<div id="status_steamid"></div>
		<div id="emoticons_loaded"></div>
	</div>
	<!-- this is where the error message from above goes it is mostly blank until an error-->
	
	<!-- emoticons are initially loaded here for the user to see
		 i'm not sure how to present this or if they should even see them
		 at first so yewr ideas are welcome here! there could be someone
		 with like 1000 emoticons so keep in mind those things. maybe it
		 could show like 20 and have scrollbars to show more or something
		 or maybe it shouldn't be shown at all. also whenever the user hovers
		 over an emoticon anywhere it should show the text in a small popup
		 [:spaceduck:] or something like that!
	-->
	<div>
	Font:
	<!-- this should look nice a fit with the other elements, nothing special here though! just colours etc. -->
	<select id="font_select" onchange="update_emoticons()"></select>
	</div>
	
	<!-- emoticons are initially loaded here with urls for the user to select them
		 as the background emoticon to use: i dont think these should be displayed but
		 it is up to you. my idea is that there is a single button that you click which
		 opens a popup at the side of it showing all the emoticons temperarily and after
		 selection the button would show that emoticon as its content letting the user
		 see which is the current emoticon. right now it just shows the text in span.
		 yewr ideas about this are welcome, i'm not tew sure what is good!
	-->
	<div>
	Select Background: <span id="bkg_selected"></span>
	<div id="select_bkg"></div>
	</div>
	<div>
	<!-- forground should be the same system as background so see the comments there please -->
	Select Foreground: <span id="fg_selected"></span>
	<div id="select_fg"></div>
	</div>
	
	<!-- it will update the preview automatically on typing but i think there should be the button
		 also because there might be some instance where it needs to be used to update like if someone
		 use ctrl + v. i'm not sure how this should look but i guess just styled nicely with the others!
	-->
	<div>
	Your message:
	<input type="text" name="text" id="input_text" onkeyup="validate_input(); update_emoticons()" onkeydown="validate_input()">
	<input type="submit" id="submit_steamid" value="Preview" onclick="update_emoticons()">
	</div>
	<div>
	Preview:
	<!-- this is where the preview goes there will be lots of images loaded here!
		 it could be anywhere from 0pixels wide to 2000pixels wide so it should
		 use some presentation method that suits that, maybe set a fixed width
		 of 1024 and use a scrollbar inside, i'm not sure so it's up to you!
	-->
	<div id="emoticons_preview"></div>
	<span id="chars_left"></span>
	</div>
	
	<!-- this is just a note of how many characters the user can enter that will fit inside a steam message
		 i think that it should be next to input_Text instead of here but i'm not sure! also its just text
	-->
	
	<div>
	Code:
	<!-- the code goes here nothing special really just should be a code box i'm not sure how to do that
		 the user should be able to copy/paste the text easily so maybe it should use an input instead of div
		 it is up to yew!
	-->
	<div id="emoticons_code"></div>
	</div>
	
	
	<!-- some nice footer here not quite sure what it should say but this is a start maybe some links about us
		 or link to repository for website source code also credits should go here and stuff, feel free to edit
	-->
	<div>
	Created by lodysama 「t g b s」 and ami 「t g b s」
	</div>
	<div>
	<a href="fonts.js">Font data 「gpl2」</a> is derived from <a href="http://artwizaleczapka.sourceforge.net/">artwiz improved fonts</a>
	</div>
</body>
<script type="text/javascript" src="main.js"></script>
</html>
