<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $errors = session('errors') ?? [] ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">Daftar akun customer</h1>
                    <p class="text-body-secondary small mb-4">Isi nama, email, dan password untuk mulai berbelanja.</p>

                    <form method="post" action="<?= url_to('register') ?>" data-once>
                        <?= csrf_field() ?>
                        <?php foreach ([
                            ['name', 'Nama lengkap', 'text', 'name', true, ''],
                            ['email', 'Email', 'email', 'email', true, ''],
                            ['phone', 'Nomor HP (opsional)', 'tel', 'tel', false, 'Contoh: 081234567890'],
                        ] as [$field, $label, $type, $autocomplete, $required, $help]) : ?>
                            <div class="mb-3">
                                <label class="form-label" for="<?= $field ?>"><?= $label ?></label>
                                <input class="form-control<?= isset($errors[$field]) ? ' is-invalid' : '' ?>" type="<?= $type ?>" id="<?= $field ?>" name="<?= $field ?>" value="<?= esc(old($field, ''), 'attr') ?>" autocomplete="<?= $autocomplete ?>" <?= $required ? 'required' : '' ?> aria-describedby="<?= $field ?>Help">
                                <div id="<?= $field ?>Help" class="<?= isset($errors[$field]) ? 'invalid-feedback' : 'form-text' ?>"><?= esc($errors[$field] ?? $help) ?></div>
                            </div>
                        <?php endforeach ?>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" type="password" id="password" name="password" autocomplete="new-password" minlength="8" aria-describedby="passwordHelp" required>
                            <div id="passwordHelp" class="<?= isset($errors['password']) ? 'invalid-feedback' : 'form-text' ?>"><?= esc($errors['password'] ?? 'Minimal 8 karakter.') ?></div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password_confirm">Ulangi password</label>
                            <input class="form-control<?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>" type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" required>
                            <?php if (isset($errors['password_confirm'])) : ?><div class="invalid-feedback"><?= esc($errors['password_confirm']) ?></div><?php endif ?>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Daftar</button>
                    </form>

                    <p class="small text-center mt-4 mb-0">Sudah punya akun? <a href="<?= url_to('login') ?>">Masuk</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
