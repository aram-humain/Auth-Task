const imageInput = document.getElementById('images');

if (imageInput) {
    let selectedFiles = [];

    imageInput.addEventListener('change', function () {

        const newFiles = Array.from(imageInput.files);

        for (const file of newFiles) {

            const alreadyExists = selectedFiles.some(
                existingFile =>
                    existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified
            );

            if (!alreadyExists) {
                selectedFiles.push(file);
            }
        }

        const dataTransfer = new DataTransfer();

        for (const file of selectedFiles) {
            dataTransfer.items.add(file);
        }

        imageInput.files = dataTransfer.files;
    });
}