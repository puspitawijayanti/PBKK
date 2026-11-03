document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    var preview = document.getElementById(input.dataset.preview);
    var label = input.parentElement.querySelector('.custom-file-label');
    var originalSource = preview ? preview.src : '';
    var maxBytes = 2 * 1024 * 1024;

    input.addEventListener('change', function () {
        var file = input.files[0];

        if (!file) {
            if (preview) {
                preview.src = originalSource;
            }
            if (label) {
                label.textContent = 'Pilih foto...';
            }
            return;
        }

        if (label) {
            label.textContent = file.name;
        }

        if (file.size > maxBytes) {
            alert('Ukuran foto melebihi 2 MB. Silakan pilih file lain.');
            input.value = '';
            if (preview) {
                preview.src = originalSource;
            }
            return;
        }

        if (preview) {
            preview.src = URL.createObjectURL(file);
        }
    });

    var removeCheckbox = document.getElementById('remove_photo');

    if (removeCheckbox && preview) {
        removeCheckbox.addEventListener('change', function () {
            preview.src = removeCheckbox.checked ? preview.dataset.placeholder : originalSource;
        });
    }
});
