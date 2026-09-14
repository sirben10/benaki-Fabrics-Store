<?php require __DIR__ . '/includes/bootstrap.php';
$active = 'gallery';
$pageTitle = 'Fabric Gallery';
$type = $_GET['type'] ?? 'all';
$where = 'g.is_active=1';
$params = [];
if (in_array($type, ['photo', 'video'], true)) {
    $where .= ' AND g.media_type=?';
    $params[] = $type;
}
$s = $pdo->prepare("SELECT g.*,p.name product_name FROM gallery_media g LEFT JOIN products p ON p.id=g.product_id WHERE $where ORDER BY g.sort_order,g.id DESC");
$s->execute($params);
$items = $s->fetchAll();
require __DIR__ . '/includes/header.php'; ?><section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 py-16"><span>MEDIA GALLERY</span>
        <h1>See Benaki Fabrics in motion.</h1>
        <p>Browse fabric photos, showroom moments and videos managed by our team.</p>
    </div>
</section>
<section class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-wrap gap-2 mb-8"><a class="px-4 py-2 rounded-full <?= $type === 'all' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php')) ?>">All</a><a class="px-4 py-2 rounded-full <?= $type === 'photo' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php?type=photo')) ?>">Photos</a><a class="px-4 py-2 rounded-full <?= $type === 'video' ? 'bg-benaki-navy text-white' : 'bg-white border' ?>" href="<?= e(base_url('gallery.php?type=video')) ?>">Videos</a></div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5"><?php foreach ($items as $it): ?><article class="group bg-white rounded-2xl overflow-hidden border shadow-sm"><button type="button" class="gallery-open block w-full text-left cursor-zoom-in" data-type="<?= e($it['media_type']) ?>" data-src="<?= e(media_url($it['media_path'])) ?>" data-poster="<?= e(media_url($it['thumbnail_path'])) ?>" data-title="<?= e($it['title']) ?>" aria-label="Open <?= e($it['title']) ?> preview"><?php if ($it['media_type'] === 'video'): ?><video class="w-full aspect-[4/3] object-cover pointer-events-none" preload="metadata" poster="<?= e(media_url($it['thumbnail_path'])) ?>">
                        <source src="<?= e(media_url($it['media_path'])) ?>">
                    </video><?php else: ?><img loading="lazy" src="<?= e(media_url($it['media_path'])) ?>" class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition" alt="<?= e($it['title']) ?>"><?php endif; ?></button><div class="p-4"><span class="text-[10px] uppercase text-amber-600 font-bold"><?= e($it['media_type']) ?></span>
                    <h2 class="font-bold mt-1"><?= e($it['title']) ?></h2><?php if ($it['product_name']): ?><p class="text-xs text-slate-500 mt-1"><?= e($it['product_name']) ?></p><?php endif; ?>
                </div>
            </article><?php endforeach; ?></div><?php if (!$items): ?><div class="text-center py-20 text-slate-500">No gallery media has been published yet.</div><?php endif; ?>
</section>
<dialog id="galleryDialog" class="w-[min(92vw,1100px)] max-w-none rounded-2xl bg-transparent p-0 backdrop:bg-slate-950/85">
    <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl">
        <button type="button" id="galleryClose" class="absolute right-3 top-3 z-10 w-10 h-10 rounded-full bg-black/70 text-white text-2xl leading-none" aria-label="Close preview">&times;</button>
        <div id="galleryViewer" class="min-h-40 flex items-center justify-center"></div>
        <p id="galleryTitle" class="bg-black text-white px-5 py-3 text-sm"></p>
    </div>
</dialog>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dialog = document.getElementById('galleryDialog');
        const viewer = document.getElementById('galleryViewer');
        const title = document.getElementById('galleryTitle');
        const close = () => {
            viewer.replaceChildren();
            title.textContent = '';
            dialog.close();
        };
        document.querySelectorAll('.gallery-open').forEach((button) => button.addEventListener('click', () => {
            viewer.replaceChildren();
            title.textContent = button.dataset.title || '';
            if (button.dataset.type === 'video') {
                const video = document.createElement('video');
                video.className = 'max-h-[78vh] max-w-full';
                video.controls = true;
                video.autoplay = true;
                video.src = button.dataset.src;
                if (button.dataset.poster) video.poster = button.dataset.poster;
                viewer.append(video);
            } else {
                const image = document.createElement('img');
                image.className = 'max-h-[78vh] max-w-full object-contain';
                image.src = button.dataset.src;
                image.alt = button.dataset.title || 'Gallery preview';
                viewer.append(image);
            }
            dialog.showModal();
        }));
        document.getElementById('galleryClose').addEventListener('click', close);
        dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });
        dialog.addEventListener('cancel', (event) => { event.preventDefault(); close(); });
    });
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>