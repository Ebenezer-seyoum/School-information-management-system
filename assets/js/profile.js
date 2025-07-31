
    document.addEventListener('DOMContentLoaded', function() {
        const imageUpload = document.getElementById('imageUpload');
        const removeImage = document.getElementById('removeImage');
        const profileImage = document.querySelector('.profile-images');

        removeImage.addEventListener('click', function() {
            profileImage.src = '../assets/img/no.png'; // Clear the image preview
            imageUpload.value = ''; // Clear the file input
        });

        imageUpload.addEventListener('change', function() {
            if (imageUpload.files && imageUpload.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result; // Update the image preview
                };
                reader.readAsDataURL(imageUpload.files[0]);
            }
        });
    });
