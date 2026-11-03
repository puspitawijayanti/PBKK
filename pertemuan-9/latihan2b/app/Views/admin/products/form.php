<?php $isEdit = $product !== null ?>
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/products') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm"></i> Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= $isEdit ? site_url('admin/products/' . $product['id']) : site_url('admin/products') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" class="form-control<?= invalid_class('name') ?>" id="name" name="name" value="<?= old('name', $product['name'] ?? '') ?>" required>
                        <?= field_error('name') ?>
                        <?= field_error('slug') ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="category_id">Kategori</label>
                            <select class="custom-select<?= invalid_class('category_id') ?>" id="category_id" name="category_id" required>
                                <option value="">Pilih kategori</option>
                                <?php foreach ($categories as $id => $name) : ?>
                                    <option value="<?= $id ?>"<?= (string) old('category_id', $product['category_id'] ?? '') === (string) $id ? ' selected' : '' ?>><?= esc($name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('category_id') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="origin">Asal Daerah</label>
                            <input type="text" class="form-control<?= invalid_class('origin') ?>" id="origin" name="origin" value="<?= old('origin', $product['origin'] ?? '') ?>" placeholder="Gayo, Aceh" required>
                            <?= field_error('origin') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="process">Proses</label>
                            <select class="custom-select<?= invalid_class('process') ?>" id="process" name="process">
                                <?php foreach (\App\Models\ProductModel::PROCESSES as $process) : ?>
                                    <option value="<?= $process ?>"<?= old('process', $product['process'] ?? 'washed') === $process ? ' selected' : '' ?>><?= esc(process_label($process)) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('process') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="roast_level">Tingkat Sangrai</label>
                            <select class="custom-select<?= invalid_class('roast_level') ?>" id="roast_level" name="roast_level">
                                <?php foreach (\App\Models\ProductModel::ROAST_LEVELS as $roast) : ?>
                                    <option value="<?= $roast ?>"<?= old('roast_level', $product['roast_level'] ?? 'medium') === $roast ? ' selected' : '' ?>><?= esc(roast_label($roast)) ?></option>
                                <?php endforeach ?>
                            </select>
                            <?= field_error('roast_level') ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="weight_gram">Berat (gram)</label>
                            <input type="number" class="form-control<?= invalid_class('weight_gram') ?>" id="weight_gram" name="weight_gram" min="1" max="5000" value="<?= old('weight_gram', $product['weight_gram'] ?? 250) ?>" required>
                            <?= field_error('weight_gram') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="price">Harga (Rp)</label>
                            <input type="number" class="form-control<?= invalid_class('price') ?>" id="price" name="price" min="1" step="500" value="<?= old('price', $product['price'] ?? '') ?>" required>
                            <?= field_error('price') ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stock">Stok</label>
                            <input type="number" class="form-control<?= invalid_class('stock') ?>" id="stock" name="stock" min="0" value="<?= old('stock', $product['stock'] ?? 0) ?>" required>
                            <?= field_error('stock') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi dan Catatan Rasa</label>
                        <textarea class="form-control<?= invalid_class('description') ?>" id="description" name="description" rows="5"><?= old('description', $product['description'] ?? '') ?></textarea>
                        <?= field_error('description') ?>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"<?= (string) old('is_active', $product['is_active'] ?? '1') === '1' ? ' checked' : '' ?>>
                        <label class="custom-control-label" for="is_active">Tampilkan di katalog</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Foto Produk</h6></div>
                <div class="card-body text-center">
                    <img src="<?= product_photo_url($product['photo'] ?? null) ?>" id="photoPreview" data-placeholder="<?= base_url('assets/img/no-photo.svg') ?>" class="img-fluid rounded border mb-3 photo-preview" alt="Pratinjau foto">
                    <div class="custom-file text-left">
                        <input type="file" class="custom-file-input<?= invalid_class('photo') ?>" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" data-preview="photoPreview">
                        <label class="custom-file-label" for="photo" data-browse="Pilih">Pilih foto...</label>
                    </div>
                    <?= field_error('photo') ?>
                    <small class="form-text text-muted text-left">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    <?php if ($isEdit && ! empty($product['photo'])) : ?>
                        <div class="custom-control custom-checkbox text-left mt-2">
                            <input type="checkbox" class="custom-control-input" id="remove_photo" name="remove_photo" value="1">
                            <label class="custom-control-label small" for="remove_photo">Hapus foto saat ini</label>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan Produk</button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/photo-preview.js') ?>"></script>
<?= $this->endSection() ?>
