<script>
	function show_prompt( title, text, confirmURL ) {
		$( "#div_prompt_title" ).text( title );
		$( "#div_prompt_text" ).html( text );

		$( "#div_prompt_confirm" ).prop("href", confirmURL);

		$( "#div_overlay" ).show();
	}

	function hide_prompt() {
		$( "#div_overlay" ).hide();
	}
</script>

<div id="div_overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background-color: rgba(0, 0, 0, 0.6); display: none">
	<div id="div_prompt" style="display: block; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 32px; background-color: white; border-radius: 16px;">
		<h1 id="div_prompt_title">Title</h1>
		<p id="div_prompt_text">Text</p>
		<br>
		<a id="div_prompt_confirm"><div class="inline-button"><p>Yes</p></div></a>
		<div onclick="hide_prompt()" class="inline-button"><p>Cancel</p></div>
	</div>
</div>