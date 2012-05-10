
  
  <script type="text/javascript">
	function initMenus() {
	$('ul.menu ul').hide();
	$.each($('ul.menu'), function(){
		$('#' + this.id + '.expandfirst ul:first').show();
	});
	$('ul.menu li a').click(
		function() {
			var checkElement = $(this).next();
			var parent = this.parentNode.parentNode.id;

			if($('#' + parent).hasClass('noaccordion')) {
				$(this).next().slideToggle('normal');
				return false;
			}
			if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
				if($('#' + parent).hasClass('collapsible')) {
					$('#' + parent + ' ul:visible').slideUp('normal');
				}
				return false;
			}
			if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
				$('#' + parent + ' ul:visible').slideUp('normal');
				checkElement.slideDown('normal');
				return false;
			}
		}
	);
}
$(document).ready(function() {initMenus();});
	
	</script>




	
	
	
	<ul id="menu" class="menu noaccordion expandfirst">
		<li>
			<a href="#">Search for bibliographic information
		  <span id="formInfo" title="<div>Please select the type of search you want to perform:<br/>by Author<br/>by Keywords<br/>by ISBN<br/> Then you can select an ISBN in the generated list</div>">
                <img src="img/info.gif" border="0" />
	      </span> </a>
			<ul>
			<li><?php include "books.php";?></li>

			</ul>
		</li>
		<li>
			<a href="#">Search for social information 
			<span id="formInfo2" title="<div>Once a valid ISBN has been selected from the above list<br/> you can also search for social information: <br/> Tags <br/> Reviews <br/> Or both</div>">
                		<img src="img/info.gif" border="0" />
	     	 	</span></a>
			<ul>
			<li><?php include "books2.php";?></li>
				
			</ul>
		</li>
				
	</ul>	


	
