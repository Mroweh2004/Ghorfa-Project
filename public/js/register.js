function handleProfileImagePreview() {
    var input = document.querySelector("#profile_image");
    var preview = document.querySelector(".profile-image-preview");
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 100%; border-radius: 50%;">';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
}

document.addEventListener('DOMContentLoaded', handleProfileImagePreview);