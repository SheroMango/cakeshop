
$(function(){

 /*选型号*/
 $('.hgauto li').click(function(){
	 $(this).addClass('selected');
	 $(this).siblings('li').removeClass('selected');
	 });
 
 /*数量加减*/
	$('.price em.add-btn').click(function(){
		 var nums=$(this).siblings('input').val();
		$(this).siblings('input').val(parseInt(nums)+1);
	 });
	$('.price em.subtract-btn').click(function(){
		 var nums=$(this).siblings('input').val();
		 if(nums>1){
		  $(this).siblings('input').val(parseInt(nums)-1);
		 }
	 });
  /*筛选*/
  $('.shuaixuan').click(function(){
	    if($('.popup').css("display")=="none"){ 
		  $('.popup').show();
		}else{
	      $('.popup').hide();
      }
  });	  
  $('.popup dt').eq(0).addClass('select');
  $('.popup dt').eq(0).siblings('dd').show();
  $('.popup dt').click(function(){
	    if($(this).siblings('dd').css("display")=="none"){ 
		  $(this).addClass('select');
		  $(this).siblings('dd').slideDown(200);
		  $(this).parent('dl').siblings('dl').children('dd').slideUp(200);
		  
		}else{
		 $(this).removeClass('select');	
		 $(this).siblings('dd').slideUp(200);
			}
		});
  $('.popup dd p').click(function(){
	  var zhi=$(this).text();
	  var hideinput = $(this).find(':input:eq(0)').val();
      $(this).parent('dd').siblings('dt').children('p').children('em').text(zhi);
      $(this).parent('dd').siblings('dt').children('p').children('input').val(hideinput);
	  });		 	 
/*城市搜索*/	
  $('.seek-city em').click(function(){
	  $(this).siblings('.shuru').val(' ');
	  });  
  	  
	  		 	 
	 
}); 
 