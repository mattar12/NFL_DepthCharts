<?php
    $json = file_get_contents('data/depthCharts.json');
    $data = json_decode($json);
    $teams = $data->Teams;
    asort($teams);
    $positions = $data->Positions;
    $depthCharts = $data->DepthCharts;
?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NFL Depth Charts</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <style type="text/css">
        html, body {
    		height: 100%;
    		overflow-x: hidden;
		}
		body {
 	 		/* position: relative */
		}
		.container {    	
    		height: 100%;
			background-color: rgba(0, 50, 0, 0.3);
		}
		#results-panel{
			background: rgba(255, 255, 255, 0.5);
			
		}
		#page-heading > h2{
			margin-top: 5px;
			margin-bottom: 0px;
		}
        .depthChart-panel{
        	padding-left:0px;
        	padding-right:0px;
        	padding-bottom:0px;
        	padding-top:0px;
        	position: 'relative';
    		height: 100%; // Or whatever you want (eg. 400px)
    		overflow: hidden;
            /* background: rgba(255, 255, 255, 0.9); */
        }
        .panel-success{
            margin-bottom: 2px;
        }
        .scroll {
			max-height: 300px;
			overflow: hidden;
			overflow-y: auto;
		} 
    </style>
</head>
<body>
<div class="container col-sm-12">
        <div id="page-heading" class="row">
            <h2 class="text-center "><i class="fa fa-info-circle
        page-header-icon"></i>&nbsp;&nbsp;NFL Depth Charts</h2>
        </div>
        <div class="row text-center">
        <div id="buttons" class="dropdown">
            <div class="btn-group">
                <button id="team-button" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle">Team<span
                        class="caret"></span></button>
                <ul id="team-dropdown" class="col-sm-12 dropdown-menu scroll">
                    <?php
                    foreach($teams as $num=>$teamName){
                        echo '<li><a href="#">' . $teamName . '</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="btn-group">
                <button id="pos-button" data-toggle="dropdown" class="btn btn-primary dropdown-toggle">Position<span
                        class="caret"></span></button>
                <ul id="position-dropdown" name="position" class="dropdown-menu">
                    <?php
                    foreach($positions as $n=>$pos){
                        echo '<li><a href="#" value="'.$n.'">' . $pos . '</a></li>';
                    }
                    echo '<li><a href="#" value="All" class="disabled">All(coming soon)</a></li>';
                    ?>
                </ul>
            </div>
            <button id="submit-btn" class="btn btn-default">Clear</button>
    	</div> <!--End BUTTONS -->
    </div> <!-- End row -->
    <hr>
    <!--div class="panel panel-default col-sm-12" -->
    <div id="results-panel" class="">
	    <div id="results" class=""></div>
	</div>
        


</div> <!-- END CONTAINER -->


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.1/jquery.slimscroll.min.js"></script>
<script>
    $(document).ready( function(){
		$.getJSON( "data/depthCharts.json", function( data ) {
			$("#page-heading").append("<div id=\"lastUpdated\" class=\"text-center\"><small>Updated: " + data.LastUpdated + "</small></div>");
		});

        $('#position-dropdown > li > a').click(function(){
            $(this).attr('selected', true);
            var sel = $(this).text();
            $('#pos-button').html(sel);
            getResult();
        })

        $('#team-dropdown > li > a').click(function(){
            $(this).attr('selected', true);
            var sel = $(this).text();
            $('#team-button').html(sel);
            getResult();
        })
        

        function getResult(){
            var team = $('#team-button').text();
            var pos = $('#pos-button').text();
            
            $.getJSON( "data/depthCharts.json", function( data ) {
            	var team = $('#team-button').text();
            	var pos = $('#pos-button').text();
 				var items = [];
 				items.push( "<div class=\"depthChart-panel col-md-4 col-sm-12\"><div class=\"panel panel-success \">")
 				items.push( "<div class=\"panel-heading\"><span>" + team + ": " + pos + "s</span></div><div class=\"panel-body\">" ); 				
 				items.push("<ol>");
 				
  				$.each( data.DepthCharts[team][pos], function( key, val ) {
    				items.push( "<li id='" + key + "'>" + val + "</li>" );
  				});
  				items.push("</ol>");
  				items.push( "</div></div></div>" );
  				items = items.join( "" );
  				$(items).appendTo( "#results" );
		    
				var elementHeights = $('.depthChart-panel').map(function() {
    				return $(this).height();
			  	}).get();
	  			// Math.max takes a variable number of arguments
  				// `apply` is equivalent to passing each height as an argument
  				var maxHeight = Math.max.apply(null, elementHeights);
	  			// Set each height to the max height
  				$('.depthChart-panel > .panel').height(maxHeight);
			});
			
        }
        
    		$('#submit-btn').click(function(){
    			$( "#results" ).empty();
    			$( "#results-panel" ).removeClass('panel');
    			$( "#results-panel" ).removeClass('panel-default');
    			$( "#results" ).removeClass('panel-body');
    			$('#team-button').html("Select Team");
    			$('#pos-button').html("Select Position");
    		});
    });
    
    $('#results-panel').slimScroll({
    	height: '80%',
    	railVisible: true,
    	alwaysVisible: false,
    	allowPageScroll: false,
    	disableFadeOut: false,
    	color: '#009933',
    	size: '10px',
    	wheelStep: 40,
	});
</script>

<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>                                		
