$(function(){
  var pageurl = $("#pageurl").attr('content');
  var replace = $("#replace_file").attr('value');
  /*
   * For the sake keeping the code clean and the examples simple this file
   * contains only the plugin configuration & callbacks.
   * 
   * UI functions ui_* can be located in: demo-ui.js
   */
  $('#drag-and-drop-zone').dmUploader({ //
    url: pageurl+'doUpload',
    maxFileSize: 15000000, // 15 Megs 
    onDragEnter: function(){
      // Happens when dragging something over the DnD area
      this.addClass('active');
    },
    onDragLeave: function(){
      // Happens when dragging something OUT of the DnD area
      this.removeClass('active');
    },
    onInit: function(){
      // Plugin is ready to use
      ui_add_log('System Upload initialized :)', 'info');
    },
    onComplete: function(){},
    onNewFile: function(id, file){
      // When a new file is added using the file selector or the DnD area
      ui_multi_add_file(id, file);
    },
    onBeforeUpload: function(id){
      // about tho start uploading a file
      ui_multi_update_file_status(id, 'uploading', 'Uploading...');
      ui_multi_update_file_progress(id, 0, '', true);
    },
    onUploadCanceled: function(id) {
      // Happens when a file is directly canceled by the user.
      ui_multi_update_file_status(id, 'warning', 'Canceled by User');
      ui_multi_update_file_progress(id, 0, 'warning', false);
    },
    onUploadProgress: function(id, percent){
      // Updating file progress
      ui_multi_update_file_progress(id, percent);
    },
    onUploadSuccess: function(id, data){
	  // A file was successfully uploaded
      if(replace != "undefined") {
		  var redir = $("#replace_file").attr('content');
		  ui_add_log('The uploaded file has replace the File. You will be redirected in 3 seconds', 'success');
		  setTimeout(function() {
				window.location.href=redir;
		  }, 3000);
	  } else {
		  ui_add_log(data.message, 'success');
	  }	  
      ui_multi_update_file_status(id, 'success', 'Upload Complete');
      ui_multi_update_file_progress(id, 100, 'success', false);
	  
    },
    onUploadError: function(id, xhr, status, message){
      ui_multi_update_file_status(id, 'danger', message);
      ui_multi_update_file_progress(id, 0, 'danger', false);  
    },
    onFallbackMode: function(){
      // When the browser doesn't support this plugin :(
      ui_add_log('Plugin cant be used here, running Fallback callback', 'danger');
    },
    onFileSizeError: function(file){
      ui_add_log('File \'' + file.name + '\' cannot be added: size excess limit', 'danger');
    }
  });
});