{extends 'layout.tpl'}

{block 'main'}
	
	<div id="homepage-hero">

		<h1>RhymeUp</h1>
		<h2>Listen to free music via Youtube.</h2>

		<form id="homepage-search" action="search">
			<input type="text" name="q" id="q" placeholder="Search for Music">
		</form>

	</div>

    <div class="artwork-block" id="popular-block">
        <h2>Popular Music</h2>
        <ul>
            <li>
                <div class="artwork-thumb">
                    <img src="http://i.ytimg.com/vi/Z3xPiDMmr-E/default.jpg" height="148" width="148">
                    <div class="hover-buttons">
                    <a href="javascript:void(0);" class="hover-play"></a>
                    <a href="javascript:void(0);" class="hover-add"></a>
                    </div>
                </div>
                <div class="artwork-title">Thrice - Atlantic</div>
                <div class="artwork-duration"></div>
            </li>
        </ul>
    </div>

{/block}