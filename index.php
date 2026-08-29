<?php require_once __DIR__ . '/includes/bootstrap.php';
$active = 'home';
$pageTitle = 'Premium Fabrics for Every Occasion';
$slides = $pdo->query("SELECT * FROM hero_slides WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE is_active=1 ORDER BY sort_order,id LIMIT 5")->fetchAll();
require __DIR__ . '/includes/header.php'; ?>
<section class="relative overflow-hidden bg-benaki-navy text-white">
    <div id="heroSlider" class="relative min-h-[620px]"><?php foreach ($slides as $i => $s): ?><article class="hero-slide absolute inset-0 <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image:linear-gradient(90deg,rgba(4,19,38,.96),rgba(4,19,38,.58),rgba(4,19,38,.1)),url('<?= e(media_url($s['image_path'])) ?>')"></div>
                <div class="relative max-w-7xl mx-auto px-4 min-h-[620px] flex items-center">
                    <div class="max-w-2xl py-24"><span class="text-amber-300 font-semibold uppercase tracking-[.25em] text-xs"><?= e($s['eyebrow']) ?></span>
                        <h1 class="text-4xl md:text-6xl font-bold leading-tight mt-4"><?= e($s['title']) ?></h1>
                        <p class="mt-5 text-lg text-slate-200 max-w-xl"><?= e($s['subtitle']) ?></p>
                        <div class="mt-8 flex flex-wrap gap-3"><a href="<?= e(site_url($s['cta_link'] ?: 'fabrics.php')) ?>" class="bg-benaki-gold text-benaki-navy font-bold px-6 py-3 rounded-xl"><?= e($s['cta_text'] ?: 'Explore Fabrics') ?></a><a href="<?= e(base_url('fabrics.php')) ?>" class="border border-white/30 px-6 py-3 rounded-xl">View Collection</a></div>
                    </div>
                </div>
            </article><?php endforeach; ?></div>
    <div class="absolute bottom-7 left-0 right-0 flex justify-center gap-2 z-10"><?php foreach ($slides as $i => $s): ?><button class="hero-dot w-8 h-1 rounded <?= $i === 0 ? 'bg-amber-400' : 'bg-white/40' ?>" data-slide="<?= $i ?>"></button><?php endforeach; ?></div>
</section>
<section class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-2 md:grid-cols-4 gap-5 text-center">
        <div>
            <div class="text-2xl">✦</div><b>Premium Quality</b>
            <p class="text-xs text-slate-500">Fabrics you can trust</p>
        </div>
        <div>
            <div class="text-2xl">₦</div><b>Fair Pricing</b>
            <p class="text-xs text-slate-500">Value for your money</p>
        </div>
        <div>
            <div class="text-2xl">🚚</div><b>Fast Delivery</b>
            <p class="text-xs text-slate-500">South East & South South</p>
        </div>
        <div>
            <div class="text-2xl">♡</div><b>Trusted Service</b>
            <p class="text-xs text-slate-500">We care about you</p>
        </div>
    </div>
</section>
<section id="fabrics" class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex justify-between items-end gap-4 mb-8">
        <div><span class="text-amber-600 font-semibold text-sm">OUR COLLECTION</span>
            <h2 class="text-3xl font-bold mt-2">Fabrics for every style</h2>
        </div><a href="<?= e(base_url('fabrics.php')) ?>" class="font-semibold text-benaki-navy">View all →</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-5"><?php foreach ($products as $p): ?><article class="bg-white rounded-2xl overflow-hidden shadow-sm border hover:-translate-y-1 transition"><img src="<?= e(media_url($p['image_path'])) ?>" class="w-full aspect-[4/3] object-cover" alt="<?= e($p['name']) ?>">
                <div class="p-4">
                    <h3 class="font-bold text-lg"><?= e($p['name']) ?></h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= e($p['description']) ?></p>
                    <div class="mt-3 text-sm font-semibold"><?= money((float)$p['yard_price']) ?> <span class="font-normal text-slate-400">/ yard</span></div><a href="<?= e(base_url('fabric.php?slug=' . rawurlencode($p['slug']))) ?>" class="inline-block mt-3 bg-benaki-navy text-white px-4 py-2 rounded-lg text-xs font-semibold">View Details</a>
                </div>
            </article><?php endforeach; ?></div>
</section>
<section class="bg-benaki-navy text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 flex flex-col md:flex-row items-center justify-between gap-6">
        <div><span class="text-amber-300 text-xs uppercase tracking-widest">Special campaign</span>
            <h2 class="text-3xl font-bold mt-2">Celebrating 1 Year of Benaki Fabrics</h2>
            <p class="text-slate-300 mt-2">Enjoy anniversary savings on qualifying quantities while the campaign lasts.</p>
        </div><a href="<?= e(base_url('anniversary.php')) ?>" class="bg-benaki-gold text-benaki-navy font-bold px-6 py-3 rounded-xl">See Anniversary Offers</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>