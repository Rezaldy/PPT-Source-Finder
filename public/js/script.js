Dropzone.autoDiscover = false;

$(document).ready(function(){
    $("#copyConfirm").click(function(){
        $("#confirm").select();
        document.execCommand('copy');
        alert("Sources copied to clipboard!");
    });
    
    var myDropzone = new Dropzone("div#dropzone-form", {
        url: "/upload",
        init: function() {
            this.on('error', function(file,response){
                $('#dropzone-errors').html(response);
            });
            this.on('success', function(file,response){
                $('#result-images').empty();
                var images = response.images;
                var fileHash = response.filename;
                $('#result-images').append('<input type="hidden" name="_token" value="' + document.querySelectorAll('meta[name=csrf-token]')[0].getAttributeNode('content').value + '"><input type="hidden" name="filehash" value="' + fileHash + '" />');
                images.forEach(function(image){
                    var source = image['source'] || '';
                    $('#result-images').append('<div class="image-block"><img width="50%" src=' + image['path'] + '/><div class="textblock"><label>Source<br><textarea width="50%" name="' + image['file'] + '" >' + source + '</textarea></label></div></div><hr/>');
                });
                $('#result-images').append('<input type="submit" id="submit-button" value="Opslaan" />');
            });
        },
        headers: {
                    'X-CSRF-TOKEN': document.querySelectorAll('meta[name=csrf-token]')[0].getAttributeNode('content').value,
                },
        acceptedFiles: ".ppt,.pptx",
    });
});