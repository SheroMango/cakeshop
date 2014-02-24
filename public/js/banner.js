 
             var nubm=$('#thelist').children('li').length;
			 for(i=1;i<=nubm;i++){
				$('#indicator').prepend('<li> </li>'); 
				 }
			$('#indicator').children('li:first').addClass('active');	 
 
    var count = document.getElementById("thelist").getElementsByTagName("img").length;
    if(document.body.clientWidth>=640){
	  var kuani= $('.container').clientWidth;
	 }else{
	 var kuani= document.body.clientWidth; 
		 }
	for (i = 0; i < count; i++) {
        document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:" + kuani + "px";
    }
	
    document.getElementById("scroller").style.cssText = " width:" + document.body.clientWidth * count + "px";
    setInterval(function() {
        myScroll.scrollToPage('next', 0, 400, count);
    },
	3000);
    window.onresize = function() {
        for (i = 0; i < count; i++) {
            document.getElementById("thelist").getElementsByTagName("img").item(i).style.cssText = " width:" + document.body.clientWidth + "px";
        }
        document.getElementById("scroller").style.cssText = " width:" + document.body.clientWidth * count + "px";
    };
 