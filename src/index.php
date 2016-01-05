<?php
/* 
Package: BathTime{r}
File: index.php
(c) 2015 Brooke Dukes All Rights Reserved

This is the main user facing file. It is used to allow the user to to set the access codes and timer. 
*/
?>


<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link rel="author" href="humans.txt" />
        <title>BathTime{r}</title>
        <meta name="description" content="Web interface for BathTime{r}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link href='https://fonts.googleapis.com/css?family=Oxygen:400,300' rel='stylesheet' type='text/css'>
        <link href='https://cdn.jsdelivr.net/genericons/3.4.1/genericons/genericons.css' rel="stylesheet" type="text/css">

        <link rel="stylesheet" href="core/css/normalize.css">
        <link rel="stylesheet" href="core/css/main.css">

        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="core/js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
    </head>
    <body>
        <div class="main-container">
            <div class="main wrapper clearfix">

                <article>
	                <h1> BathTime{r}</h1>
            
                    <section>
	                    <?php //This div is for error and success messages it  ?>
                         <div class="result 
	                         <?php  if( (isset ($_COOKIE["deviceid"] ) ) && ( isset($_GET[ 'm' ] ) ) ){ echo ("success");}  // on sucess no-js ?>
		                     <?php  if(isset($_GET[ 'e' ] ) ){ echo ("fail");} //on error no-js        ?>
			             ">
	                     	<span class="message"> 
	                     	<?php if( (isset ($_COOKIE["deviceid"] ) ) && ( isset($_GET[ 'm' ] ) ) ){ echo ($_GET['m'] ); } // on sucess no-js?>
						 	<?php if(isset($_GET[ 'e' ] ) ){ echo ($_GET['e'] ); } //on error no-js?>

						 	</span> 
						 	<a href="#" class="reset-timer"> Reset Timer </a>
	                     </div>
	                     
	                     <?php //if we haven't set the access token and device ID that's a great place to start
		                     if ( ( !isset ($_COOKIE["deviceid"] ) ) || ( !isset ($_COOKIE["accesstoken"] ) ) ){  ?>
	                     <div id="setAccess">
	                     <p> Please enter your Device ID and Access Token. Once entered, the values will be set for 60 days or until your cookies are cleared. </p>
	                      <form id="access" action="setAccess.php" method="post">
						 	<label for="d" class=" genericon genericon-cloud"></label>
						 	<input id="deviceid" name="d" type="text" value="<?php if(isset ( $_GET['d'] )) echo $_GET['d']; ?>" placeholder="Device ID" title="Enter Device ID"/> <br/>
						 	<label for="a" class="genericon genericon-key"></label>
						 	<input id="accesstoken" name="a" type="text" value="<?php if ( isset( $_GET['a'] )) echo $_GET['a']; ?>" placeholder="Access Token" title="Enter Access Token"/><br>

						 	<input class="button" type="submit" value="Submit" />
						 	<div class="ease"></div>

						</form>
						</div>

                    </section>
                    <style>#timer{display: none; } </style>

		                     
		                     <?php } //end ID and token check ?>
	                     <section>
		                  <span class="loading-icon"></span>
						 <form id="timer" action="postFallback.php" method="post">
						 	<label for="minutes" class="genericon genericon-time"></label>

						 	<input id="minutes"   name="m" type="text" value="" placeholder="Enter number of minutes" max="99" min="1" inputmode="numeric" maxlength="2" title="Enter number of minutes" />
						 	<input id="deviceid" type="hidden" name="deviceid" value="<?php echo( $_COOKIE["deviceid"] ); ?>">
						 	<input id="accesstoken" type="hidden" name="accesstoken" value="<?php echo( $_COOKIE["accesstoken"] ); ?>">


						 	<input class="button" type="submit" value="Set Timer" />
						 	<div class="ease"></div>
						</form>
                    </section>
                    <section class="photon-data">
	                    <span class="status">
	                    	<span class="genericon genericon-dot"></span> Unknown
	                    </span> 
	                    <span class="battery"></span>
                    </section>
 
            </section>

                </article>



            </div> <!-- #main -->
        </div> <!-- #main-container -->

        <div class="footer-container">
            <footer class="wrapper left">
                <p class="left"> © Brooke Dukes 2015 </p>
                <p class="right"> 
	             <a href="http://brooke.codes"> </a>  
				 <a href="https://twitter.com/bandonrandon">  </a>  
				 <a href="https://github.com/BandonRandon/bathtimer">  </a>
				</p>
                <p class="clearfix"></p>
            </footer>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="core/js/vendor/jquery-2.1.4.min.js"><\/script>')</script>
        <script src="core/js/main.js"></script>
    </body>
</html>
