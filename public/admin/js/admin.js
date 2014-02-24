////////////////////后台相关js////////////////////
//鼠标移动表格效果
$(document).ready(function(){
	$(".listTable tr[overstyle='on']").hover(
	  function () {
		$(this).addClass("bg_hover");
	  },
	  function () {
		$(this).removeClass("bg_hover");
	  }
	);
});

function checkon(o){
	if( o.checked == true ){
		$(o).parents('tr').addClass('bg_on') ;
	}else{
		$(o).parents('tr').removeClass('bg_on') ;
	}
}

function checkAll(o){
	if( o.checked == true ){
		$('input[name="checkbox"]').attr('checked','true');
		$('tr[overstyle="on"]').addClass("bg_on");
	}else{
		$('input[name="checkbox"]').removeAttr('checked');
		$('tr[overstyle="on"]').removeClass("bg_on");
	}
}

//获取已选择的ID数组
function getChecked() {
	var ids = new Array();
	$.each($('table input:checked'), function(i, n){
		ids.push( $(n).val() );
	});
	return ids;
}



////////////////////地点相关///////////////
function delPlace(ids)
{
	var length = 0;
	if(ids) {
		length = 1;    		
	}else {
		ids = getChecked();
		length = ids.length;
		ids = ids.toString();
	}	
	if(ids == '') {
		ui.error('请先选择一个地点');
		return ;
	}
	if(confirm('您将删除'+length+'个地点，确定删除？')) {
		var url = 'index.php?g=Moderator&m=Dest&a=doDelPlace';
		$.post(url, {ids: ids}, function(res){
			if(res=='1') {
				ui.success('删除成功');
				removePlace(ids);
			}else {
				ui.error('删除失败');
			}
		});
	}
}

function removePlace(ids) {
	ids = ids.split(',');
    for(i = 0; i < ids.length; i++) {
    	$('#place_'+ids[i]).remove();
	}
}

////////////////////图片相关///////////////
function delPhoto(ids)
{
	var length = 0;
	if(ids) {
		length = 1;    		
	}else {
		ids = getChecked();
		length = ids.length;
		ids = ids.toString();
	}	
	if(ids == '') {
		ui.error('请先选择一张图片');
		return ;
	}
	if(confirm('您将删除'+length+'张图片，确定删除？')) {
		var url = 'index.php?g=Moderator&m=Place&a=doDelPhoto';
		$.post(url, {ids: ids}, function(res){
			if(res=='1') {
				ui.success('删除成功');
				removePhoto(ids);
			}else {
				ui.error('删除失败');
			}
		});
	}
}

function removePhoto(ids) {
	ids = ids.split(',');
    for(i = 0; i < ids.length; i++) {
    	$('#photo_'+ids[i]).remove();
	}
}

////////////////////单页相关///////////////