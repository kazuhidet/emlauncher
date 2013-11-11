<div class="media">
  <p class="pull-left">
    <a href="<?=url("/app?id={$app->getId()}")?>">
	  <img class="app-icon media-object img-rounded" src="<?=$app->getIconUrl()?>">
    </a>
  </p>
  <div class="media-body">
    <h2 class="media-hedding"><?=$app->getTitle()?></h2>
    <p><?=$app->getDescription()?></p>
  </div>
</div>

<div class="row">
  <div class="col-sm-4 col-md-3 hidden-xs">
    <?=block('app_infopanel',array('act'=>'upload'))?>
  </div>

  <div class="col-xs-12 col-sm-8 col-md-9">
    <form class="form-horizontal" method="post" action="<?=url("/app/upload_post?id={$app->getId()}")?>">
      <div class="form-group">
        <input type="file" class="hidden" id="file-selector">
        <input type="hidden" id="temp-file-name" name="temp_file_name" value="">
        <div class="well well-lg droparea text-center hidden-xs">
          Drop your apk/ipa file here.
        </div>
        <label class="control-label col-md-2">File</label>
        <div class="col-md-10">
          <div id="alert-nofile" class="alert alert-danger hidden">
            ファイルがアップロードされていません
          </div>
          <div class="input-group"  id="input-group-icon">
            <div class="form-control droparea" id="file-name" readonly="readonly"></div>
            <a id="file-browse" class="input-group-addon btn btn-default">Browse</a>
          </div>
          <div class="help-block">
            <div class="progress progress-striped active">
              <div id="progress-bar" class="progress-bar" style="width:0%"></div>
            </div>
            <span id="file-info">size: -; type: -</span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="title" class="control-label col-md-2">Title</label>
        <div class="col-md-10">
          <div id="alert-notitle" class="alert alert-danger hidden">
            タイトルが入力されていません
          </div>
          <input type="text" class="form-control" name="title" id="title">
        </div>
      </div>

      <div class="form-group">
        <label for="description" class="control-label col-md-2">Description</label>
        <div class="col-md-10">
          <textarea class="form-control" row="3" id="description" name="description"></textarea>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-md-2">Tags</label>
        <div class="col-md-10">

          <input type="checkbox" class="hidden" name="tags[]" value="ほげ">
          <button class="btn btn-default tags" data-toggle="button">ほげ</button>
          <input type="checkbox" class="hidden" name="tags[]" value="ふが">
          <button class="btn btn-default tags" data-toggle="button">ふが</button>

          <div id="tag-template" class="hidden">
            <input type="checkbox" class="hidden" name="tags[]" value="">
            <button class="btn btn-default tags" data-toggle="button"></button>
          </div>

          <div class="btn-group">
            <a class="btn btn-default dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-plus"></i></a>
            <div id="new-tag-form" class="dropdown-menu">
              <div class="container">
                <input type="text" id="new-tag-name" class="form-control">
                <button id="new-tag-create" class="btn btn-primary">Create</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <input type="submit" class="btn btn-primary" disabled="disabled" value="Upload">
        </div>
      </div>

    </form>
  </div>
</div>

<div class="visible-xs">
  <?=block('app_infopanel',array('act'=>'upload'))?>
</div>

<script type="text/javascript">

(function(){
  var current_xhr = null;
  function uploadPackageFile(file){
    if(current_xhr){
      current_xhr.abort();
    }
    $('input[type="submit"]').attr('disabled','disabled');
    $('#temp-file-name').val(null);
    $('#file-name').html('<i class="fa fa-spinner fa-spin"></i> uploading...');
    $('#progress-bar').css('width', '0%');
    $('#progress-bar').removeClass('progress-bar-success progress-bar-danger');
    $('#progress-bar').parent().addClass('progress-striped active');
    $('#file-info').text('size: -; type: -');

    current_xhr = $.ajax({
      url: "<?=url('/api/upload_package_temporary?name=')?>"+file.name,
      type: "POST",
      contentType: file.type,
      data: file,
      processData: false,
      xhr: function(){
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.addEventListener('progress',function(event){
          var p = Math.floor(event.loaded*100/event.total);
          if(current_xhr){
            $('#progress-bar').css('width',p.toString()+'%');
          }
        });
        return xhr;
      },
      success: function(data){
        $('#temp-file-name').val(data.temp_name);
        $('#file-name').html('<i class="fa fa-check success"></i> '+data.temp_name);
        $('#file-info').text('size: '+file.size.toLocaleString()+' bytes; type: '+data.type);
        $('input[type="submit"]').removeAttr('disabled');
        $('#progress-bar').parent().removeClass('progress-striped active');
        $('#progress-bar').css('width','100%');
        $('#progress-bar').addClass('progress-bar-success');
        current_xhr = null;
      },
      error: function(){
        $('#file-name').html('<i class="fa fa-times error"></i> upload failed.');
        $('#progress-bar').addClass('progress-bar-danger');
        current_xhr = null;
      },
    });
  }
  $('#file-selector').on('change',function(event){
    var file = event.target.files[0];
    return uploadPackageFile(file);
  });
  $('.droparea').on('drop',function(event){
    var file = event.originalEvent.dataTransfer.files[0];
    $('.droparea').removeClass('dragover');
    return uploadPackageFile(file);
  });
})();

// file select dialog
$('#file-browse').on('click',function(event){
  $('#file-selector').click();
  return false;
});

// drag and drop
$(document).on('drop dragover',function(e){
  e.preventDefault();
});
$('.droparea').on('dragenter',function(event){
  $('.droparea').removeClass('dragover');
  $(this).addClass('dragover');
});
$('.droparea').on('dragleave',function(event){
  $(this).removeClass('dragover');
});

// initialize tags button state
$('input[name="tags[]"]').each(function(i,val){
  if($(val).prop('checked')){
    $(val).next().addClass('active');
  }
});
// toggle tags checkbox
$('.btn.tags').on('click',function(event){
  $(this).prev().prop('checked',!$(this).hasClass('active'));
});

// don't close dropdown
$('#new-tag-form').click(function(event){
  event.stopPropagation();
});

// click create button by enter key
$('#new-tag-name').keydown(function(event){
  if(event.keyCode==13){
    $('#new-tag-create').click();
    return false;
  }
  return true;
});

// create new tag button
$('#new-tag-create').on('click',function(event){
  var $tagname = $('#new-tag-name');
  var tag = $tagname.val();
  if(tag){
    var $tmpl = $('#tag-template');
    var $c = $tmpl.children().clone(true);

    $($c[0]).attr('value',tag).prop('checked',true);
    $($c[1]).text(tag).addClass('active')

    $tmpl.before($c);
    $tmpl.before(' ');

    $tagname.val(null);
  }
  $('.dropdown-toggle').parent().removeClass('open');
  return false;
});

// handle enter key on #title
$('#title').keydown(function(event){
  if(event.keyCode==13){
    $('form').submit();
    return false;
  }
  return true;
});


// form validation
$('form').submit(function(){
  var valid = true;
  $('.alert').addClass('hidden');
  if(!$('#temp-file-name').val()){
    $('#alert-nofile').removeClass('hidden');
    valid = false;
  }
  if($('#title').val()==''){
    $('#alert-notitle').removeClass('hidden');
    valid = false;
  }
  return valid;
});


</script>