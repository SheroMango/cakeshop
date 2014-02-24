//load相关
function preLoad() {
	//console.log('preload...');
}
function loadFailed() {
	//console.log('loadFailed...');
}
function Loaded() {
	//console.log('loaded...');
}

//mouse相关
function mouseClick()
{
	//console.log('mouse click...');
}

function mouseOver()
{
	//console.log('mouse over...');
}

function mouseOut()
{
	//console.log('mouse out...');
}


//file dialog 相关
function fileDialogStart()
{
	//console.log('file dialog start...');	
}

function fileQueued()
{
	//console.log('file queued...');
}

function fileQueueError($file, errorCode, message)
{
	//console.log('file queue error...');
}

function fileDialogComplete(numFilesSelected, numFilesQueued, numFilesTotal)
{
	console.log('////////////////////file dialog complete//////////');
	console.log(numFilesSelected);
	console.log(numFilesQueued);
	console.log(numFilesTotal);
	this.startUpload();
}


//upload相关
function uploadResizeStart()
{
	//console.log('upload resize start ...');
}
//开始上传
function uploadStart()
{
	//console.log('upload resize start ...');
}

//上传过程
function uploadProgress(file, bytesLoaded, bytesTotal) {
	
	console.log('////////////////////upload progress//////////');
	
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		file.id = "singlefile";	
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setProgress(percent);
		progress.setStatus("Uploading...");
	} catch (e) {
		;
	}
}
//上传成功
function uploadSuccess(file, serverData) {
	
	console.log('////////////////////upload success//////////');

	try {
		file.id = "singlefile";
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setComplete();
		progress.setStatus("Complete.");
		progress.toggleCancel(false);
		
		if (serverData === " ") {
			this.customSettings.upload_successful = false;
		} else {
			this.customSettings.upload_successful = true;
			document.getElementById("hidFileID").value = serverData;
		}
		
	} catch (e) {
		;
	}
}
function uploadError()
{
	//console.log('upload success...');
}

function uploadComplete(file) {
	//console.log('upload complete...');
}