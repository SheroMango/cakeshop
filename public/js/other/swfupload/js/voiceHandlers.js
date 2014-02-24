//load相关
function preLoad() {}
function loadFailed() {}
function Loaded() {}

//file dialog 相关
function fileDialogStart(){}

function fileQueued(file)
{
	try {
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setStatus("准备上传...");
		//progress.toggleCancel(true, this);
	} catch (ex) {
		this.debug(ex);
	}
}

function fileQueueError($file, errorCode, message){}

function fileDialogComplete(numFilesSelected, numFilesQueued, numFilesTotal)
{
	this.startUpload();
}


//upload相关
function uploadResizeStart(){}
//开始上传
function uploadStart(){}

//上传过程
function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setProgress(percent);
		progress.setStatus("上传中...");
	} catch (e) {
		;
	}
}

//上传成功
function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setComplete();
		progress.setStatus("上传成功");
		progress.toggleCancel(false);
		if (serverData === " ") {
			this.customSettings.upload_successful = false;
		} else {
			this.customSettings.upload_successful = true;
			var audio = eval('('+serverData+')');
			$('#voiceAudio').val(audio.savename);
		}
	} catch (e) {
		;
	}
}

function uploadError(){}

function uploadComplete(file) {}