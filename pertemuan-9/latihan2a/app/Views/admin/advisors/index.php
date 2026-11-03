<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-xl-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <form action="<?= site_url('admin/advisors') ?>" method="get" class="form-inline">
                    <select name="program" class="custom-select custom-select-sm mr-2 mb-2 mb-sm-0" aria-label="Filter program studi">
                        <option value="">Semua program studi</option>
                        <?php foreach ($programs as $id => $name) : ?>
                            <option value="<?= $id ?>"<?= $programId === (int) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="custom-control custom-checkbox mr-2">
                        <input type="checkbox" class="custom-control-input" id="unassigned" name="unassigned" value="1"<?= $onlyUnassigned ? ' checked' : '' ?>>
                        <label class="custom-control-label small" for="unassigned">Hanya yang belum punya dosen wali</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                </form>
            </div>
            <form action="<?= site_url('admin/advisors') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light"><tr><th>NIM</th><th>Nama</th><th>Prodi</th><th>Dosen Wali</th></tr></thead>
                            <tbody>
                                <?php if ($students === []) : ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada mahasiswa.</td></tr>
                                <?php endif ?>
                                <?php foreach ($students as $student) : ?>
                                    <tr>
                                        <td><code><?= esc($student['student_number']) ?></code></td>
                                        <td><?= esc($student['name']) ?></td>
                                        <td class="small"><?= esc($student['study_program_name']) ?></td>
                                        <td>
                                            <select name="advisor[<?= $student['id'] ?>]" class="custom-select custom-select-sm" aria-label="Dosen wali <?= esc($student['name'], 'attr') ?>">
                                                <option value="">Belum ditentukan</option>
                                                <?php foreach ($lecturers as $lecturer) : ?>
                                                    <option value="<?= $lecturer['id'] ?>"<?= (string) $student['advisor_id'] === (string) $lecturer['id'] ? ' selected' : '' ?>><?= esc($lecturer['name']) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan Penetapan</button>
                    <?= $pager->links('default', 'bootstrap') ?>
                </div>
            </form>
        </div>
    </div>
    <div class="col-xl-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Beban Perwalian Dosen</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($lecturers as $lecturer) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center small">
                        <span><?= esc($lecturer['name']) ?><br><span class="text-muted"><?= esc($lecturer['study_program_name']) ?></span></span>
                        <span class="badge badge-primary badge-pill"><?= (int) $lecturer['advisee_count'] ?></span>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
