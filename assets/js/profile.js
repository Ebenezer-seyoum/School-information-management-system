
    document.addEventListener('DOMContentLoaded', function() {
        const imageUpload = document.getElementById('imageUpload');
        const removeImage = document.getElementById('removeImage');
        const profileImage = document.querySelector('.profile-images');

        if (removeImage && profileImage && imageUpload) {
            removeImage.addEventListener('click', function() {
                profileImage.src = '../assets/img/no.png';
                imageUpload.value = '';
            });
        }

        if (imageUpload && profileImage) {
            imageUpload.addEventListener('change', function() {
                if (imageUpload.files && imageUpload.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                    };
                    reader.readAsDataURL(imageUpload.files[0]);
                }
            });
        }
    });
