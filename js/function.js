/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function deleteInstruction(id)
{

    $.ajax({
        type: 'GET',
        url: '<?= APPLICATION_URL ?>myaccount/deleteinstruction/file/' + id,
        async: false,
        success: function(data) {

            alert("File sucessfully deleted");
            window.location = window.location.href;

            //			window.location.href = window.location;
        }
    });

}

function addAttribute() {
    var Input = '<input type="text" class="mws-textinput" name="customAttribute[]"><br>';
    $('#customAttrib').append(Input);
}

function removeAttrib(_this){
    var c = confirm("Are you sure you want to remove this attribute?");
    if(!c) return;
    var $this = $(_this);
    var $elem = $this.prev('input');
    $elem.attr('data-value',$elem.val());
    $elem.val('').attr('disabled','disabled');
}