window.onload = function() {


    var edit_icon = document.querySelectorAll(".icon-edit-txt");
    for (var i = 0; i < edit_icon.length; i++) {
        edit_icon[i].onclick = function () {
            (this.parentNode.parentNode.childNodes[7].childNodes[9]).style.display = 'block';
        };
    }

    var modal = document.querySelectorAll(".modal");
    $( ".close" ).click(function() {
        for (var i = 0; i < modal.length; i++) {
            modal[i].style.display = "none";
        }
    });
}