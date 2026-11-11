// Interaksi kecil di sisi browser. Semua validasi penting tetap dilakukan di server.
document.addEventListener('DOMContentLoaded', () => {
    // Konfirmasi sebelum aksi penting, misalnya hapus atau batalkan pesanan.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        });
    });

    // Cegah submit ganda: tombol dinonaktifkan setelah form dikirim.
    document.querySelectorAll('form[data-once]').forEach((form) => {
        // Dijalankan setelah data form terkumpul, agar nilai tombol yang diklik tetap ikut terkirim.
        form.addEventListener('submit', (event) => {
            setTimeout(() => {
                form.querySelectorAll('button[type="submit"]').forEach((button) => {
                    button.disabled = true;
                    if (button === event.submitter) {
                        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memproses...';
                    }
                });
            }, 0);
        });
    });

    // Pratinjau gambar sebelum diunggah.
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        const preview = document.getElementById(input.dataset.preview);
        input.addEventListener('change', () => {
            const [file] = input.files;
            if (file && preview) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        });
    });
});
