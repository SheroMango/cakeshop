////////////////////load相关////////////////////
function preLoad() {

}
function loadFailed() {

}
function Loaded() {

}

////////////////////file dialog 相关////////////////////
//打开file dialog
function fileDialogStart()
{

}
//加入上传队列
//每个文件调用一次
function fileQueued(file)
{
	try {
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setStatus("准备上传...");
		progress.toggleCancel(true, this);
	} catch (ex) {
		this.debug(ex);
	}
}
//加入队列错误
//每个文件调用一次
function fileQueueError(file, errorCode, message)
{

}

//文件选择完成
function fileDialogComplete(numFilesSelected, numFilesQueued, numFilesTotal)
{
	if (this.getStats().files_queued > 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = false;
	}
	//this.startUpload();
}


////////////////////upload相关////////////////////
function uploadResizeStart()
{
	
}

//开始上传
function uploadStart()
{
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("上传中...");
		progress.toggleCancel(true, this);
	} catch (ex) {
		;
	}
	return true;
}

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
			document.getElementById("hidFileID").value = serverData;
		}
	} catch (e) {
		;
	}
}

function uploadError(file, errorCode, message)
{
	try {
		var progress = new FileProgress(file, this.customSettings.progress_target);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
			case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
				progress.setStatus("上传错误: " + message);
				this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
				progress.setStatus("Configuration Error");
				this.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
				progress.setStatus("上传失败.");
				this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.IO_ERROR:
				progress.setStatus("Server (IO) Error");
				this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
				progress.setStatus("Security Error");
				this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				progress.setStatus("Upload limit exceeded.");
				this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
				progress.setStatus("文件不存在.");
				this.debug("Error Code: The file was not found, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
				progress.setStatus("Failed Validation.  Upload skipped.");
				this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			//取消上传
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				if (this.getStats().files_queued === 0) {
					document.getElementById(this.customSettings.cancelButtonId).disabled = true;
				}
				progress.setStatus("Cancelled");
				//progress.setCancelled();
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				progress.setStatus("Stopped");
				break;
			default:
				progress.setStatus("Unhandled Error: " + error_code);
				this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {

}