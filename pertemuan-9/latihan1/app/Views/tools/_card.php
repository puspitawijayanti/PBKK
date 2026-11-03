<div class="card tool-card h-100 shadow-sm">
    <img src="<?= tool_photo_url($tool['photo']) ?>" class="card-img-top" alt="Foto <?= esc($tool['name'], 'attr') ?>" loading="lazy">
    <div class="card-body d-flex flex-column">
        <span class="badge text-bg-light align-self-start mb-2"><?= esc($tool['category_name']) ?></span>
        <h3 class="h6 card-title fw-bold"><?= esc($tool['name']) ?></h3>
        <p class="card-text small text-muted flex-grow-1"><?= esc(character_limiter((string) $tool['description'], 90)) ?></p>
        <div class="d-flex justify-content-between align-items-center">
            <?php if ((int) $tool['stock'] > 0) : ?>
                <span class="small text-success fw-semibold"><i class="bi bi-check-circle"></i> Stok <?= (int) $tool['stock'] ?></span>
            <?php else : ?>
                <span class="small text-danger fw-semibold"><i class="bi bi-x-circle"></i> Stok habis</span>
            <?php endif ?>
            <a href="<?= site_url('tools/' . $tool['slug']) ?>" class="btn btn-primary btn-sm stretched-link">Detail</a>
        </div>
    </div>
</div>
