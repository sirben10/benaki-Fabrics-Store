<?php require __DIR__ . '/includes/bootstrap.php';
$active = 'gallery';
$pageTitle = 'Fabric Gallery';
$type = $_GET['type'] ?? 'all';
$where = 'is_active=1';
$params = [];
if (in_array($type, ['photo', 'video'], true)) {
    $where .= ' AND media_type=?';
    $params[] = $type;
}
$s = $pdo->prepare("SELECT g.*,p.name product_name FROM gallery_media g LEFT JOIN products p ON p.id=g.product_id WHERE g.$where ORDER BY g.sort_order,g.id DESC");
$s->execute($params);
$items = $s->fetchAll();
require_once __DIR__ . '/includes/header.php'; ?><section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 py-16"><span>MEDIA GALLERY</span>
        <h1>See Benaki Fabrics in motion.</h1>
        <p>Browse fabric photos, showroom moments and videos managed by our team.</p>
    </div>
</section>
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-wrap gap-2 mb-8"><a class="px-4 py-2 rounded-full <?= $type === 'all' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php')) ?>">All</a><a class="px-4 py-2 rounded-full <?= $type === 'photo' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php?type=photo')) ?>">Photos</a><a class="px-4 py-2 rounded-full <?= $type === 'video' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php?type=video')) ?>">Videos</a></div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5"><?php foreach ($items as $it): ?><article class="group bg-white rounded-2xl overflow-hidden border shadow-sm"><?php if ($it['media_type'] === 'video'): ?><video class="w-full aspect-[4/3] object-cover" controls preload="metadata" poster="<?= e(media_url($it['thumbnail_path'])) ?>">
                        <source src="<?= e(media_url($it['media_path'])) ?>">
                    </video><?php else: ?><img loading="lazy" src="<?= e(media_url($it['media_path'])) ?>" class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition" alt="<?= e($it['title']) ?>"><?php endif; ?><div class="p-4"><span class="text-[10px] uppercase text-amber-600 font-bold"><?= e($it['media_type']) ?></span>
                    <h2 class="font-bold mt-1"><?= e($it['title']) ?></h2><?php if ($it['product_name']): ?><p class="text-xs text-slate-500 mt-1"><?= e($it['product_name']) ?></p><?php endif; ?>
                </div>
            </article><?php endforeach; ?></div><?php if (!$items): ?><div class="text-center py-20 text-slate-500">No gallery media has been published yet.</div><?php endif; ?>
</section><?php require __DIR__ . '/includes/footer.php'; ?>