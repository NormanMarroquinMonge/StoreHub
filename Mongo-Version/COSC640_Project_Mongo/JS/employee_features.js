document.getElementById('image').addEventListener('change', function(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();

        reader.onload = function(e) {
            var imagePreview = document.getElementById('imagePreview');
            imagePreview.src = e.target.result;  // Set image preview source to selected file
            imagePreview.style.display = 'block';  // Show the image preview
        };

        reader.readAsDataURL(file);  // Read the image file
    }
});
