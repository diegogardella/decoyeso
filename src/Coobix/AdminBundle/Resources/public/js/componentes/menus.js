

$(window).load(function(){

	$('.menusDropDownSeccion').hover(
			function(){
				$('#'+this.id+' ul').css('display','');
				$('#'+this.id+' a').eq(0).css('background-color','#24AEE0');
			},
			function(){
				$('#'+this.id+' ul').css('display','none');
				$('#'+this.id+' a').eq(0).css('background-color','#306D89');
			}
	);
	
	
	
	
	
});






