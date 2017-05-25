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
    var icon_rename_file = document.querySelectorAll(".icon-rename-file");
    for (var i = 0; i < icon_rename_file.length; i++){
        icon_rename_file[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].childNodes[1].style.display = 'block';
        };
    }
    var icon_replace_file = document.querySelectorAll(".icon-replace-file");
    for (var i = 0; i < icon_replace_file.length; i++){
        icon_replace_file[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].childNodes[3].style.display = 'block';
        };
    }
    var icon_move_file = document.querySelectorAll(".icon-move-file");
    for (var i = 0; i < icon_move_file.length; i++){
        icon_move_file[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].childNodes[5].style.display = 'block';
        };
    }
    var icon_rename_folder = document.querySelectorAll(".icon-rename-folder");
    for (var i = 0; i < icon_rename_folder.length; i++){
        icon_rename_folder[i].onclick = function () {
            this.parentNode.parentNode.childNodes[5].childNodes[3].style.display = 'block';
        };
    }
}