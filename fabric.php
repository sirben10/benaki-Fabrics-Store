<?php
require __DIR__ . '/includes/bootstrap.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$s = $pdo->prepare('SELECT * FROM products WHERE slug=? AND is_active=1 LIMIT 1');
$s->execute([$slug]);
$product = $s->fetch();
if (!$product) {
    http_response_code(404);
    $pageTitle = 'Fabric Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="max-w-4xl mx-auto px-4 py-24 text-center"><h1 class="text-3xl font-extrabold text-benaki-navy">Fabric not found</h1><a class="inline-flex mt-6 bg-benaki-navy text-white px-5 py-3 rounded-xl font-bold" href="' . e(base_url('fabrics.php')) . '">Back to fabrics</a></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
$pageTitle = $product['name'] . ' Fabric';
$active = 'fabrics';
$colors = ['White', 'Cream', 'Gold', 'Navy', 'Black', 'Blue', 'Burgundy', 'Green', 'Purple', 'Brown', 'Grey'];
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-wrap gap-2 text-xs text-slate-500 mb-7"><a href="<?= e(base_url('index.php')) ?>">Home</a><span>/</span><a href="<?= e(base_url('fabrics.php')) ?>">Fabrics</a><span>/</span><b class="text-slate-900"><?= e($product['name']) ?></b></div>
    <div class="grid lg:grid-cols-[1fr_.9fr] gap-10 items-start">
        <div class="lg:sticky lg:top-28">
            <div class="rounded-3xl overflow-hidden bg-white border shadow-sm"><img src="<?= e(media_url($product['image_path'])) ?>" class="w-full aspect-square object-cover" alt="<?= e($product['name']) ?> fabric"></div>
        </div>
        <div><span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-[10px] uppercase tracking-[.18em] font-extrabold text-amber-700">Premium fabric</span>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mt-3 text-benaki-navy"><?= e($product['name']) ?></h1>
            <p class="mt-4 text-slate-600 leading-7"><?= e($product['description']) ?></p>
            <div class="grid sm:grid-cols-2 gap-4 mt-7">
                <div class="rounded-2xl border bg-white p-5"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">36 inches · per yard</span><strong class="block text-2xl mt-2 text-benaki-navy"><?= money((float)$product['yard_price']) ?></strong></div>
                <div class="rounded-2xl border bg-white p-5"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">45 inches · trouser length</span><strong class="block text-2xl mt-2 text-benaki-navy"><?= money((float)$product['trouser_price']) ?></strong></div>
            </div>
            <div id="order" class="mt-8 bg-white border rounded-3xl p-6 shadow-sm">
                <h2 class="text-2xl font-extrabold text-benaki-navy">Add to shopping cart</h2>
                <p class="text-sm text-slate-500 mt-1">Choose your colours, measurement and quantity.</p>
                <form id="addCartForm" class="mt-6 space-y-5"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= e((string)$product['id']) ?>">
                    <div><label class="label">Select colour(s) *</label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-2"><?php foreach ($colors as $c): ?><label class="border rounded-lg px-2.5 py-2 cursor-pointer hover:border-amber-400 text-xs"><input type="checkbox" name="colors[]" value="<?= e($c) ?>"> <?= e($c) ?></label><?php endforeach; ?></div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="label">Measurement *</label><select name="measurement" id="measurement" class="input">
                                <option value="yard">Per Yard (36 inches)</option>
                                <option value="trouser_length">Per Trouser Length (45 inches)</option>
                            </select></div>
                        <div><label class="label">Quantity *</label><input name="quantity" id="quantity" type="number" min="0.5" step="0.5" value="1" class="input"></div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border p-4">
                        <div class="flex justify-between text-sm"><span>Estimated subtotal</span><strong id="lineTotal">₦<?= number_format((float)$product['yard_price']) ?></strong></div>
                        <p class="text-[11px] text-slate-500 mt-2">Anniversary savings are applied at checkout based on qualifying quantity.</p>
                    </div>
                    <div id="cartResult" class="text-sm" aria-live="polite"></div><button type="submit" class="w-full bg-benaki-gold text-benaki-navy py-3.5 rounded-xl font-extrabold">Add to Cart</button>
                </form>
            </div>
            <div class="mt-8 grid gap-5">
                <div>
                    <h2 class="text-xl font-extrabold text-benaki-navy">Description</h2>
                    <p class="text-slate-600 leading-7 mt-2"><?= nl2br(e($product['long_description'] ?: $product['description'])) ?></p>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-benaki-navy">Details</h2>
                    <p class="text-slate-600 leading-7 mt-2"><?= nl2br(e($product['details'] ?: 'Premium finish, versatile styling and suitable for a range of outfits.')) ?></p>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-benaki-navy">Care instructions</h2>
                    <p class="text-slate-600 leading-7 mt-2"><?= nl2br(e($product['care_instructions'] ?: 'Follow garment-maker and fabric care instructions.')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    window.BENAKI_PRODUCT = {
        yard: <?= json_encode((float)$product['yard_price']) ?>,
        trouser: <?= json_encode((float)$product['trouser_price']) ?>,
        endpoint: <?= json_encode(base_url('ajax/cart.php')) ?>
    };
    document.addEventListener('DOMContentLoaded', () => {
        const f = document.getElementById('addCartForm'),
            q = document.getElementById('quantity'),
            m = document.getElementById('measurement'),
            total = document.getElementById('lineTotal'),
            res = document.getElementById('cartResult'),
            fmt = n => '₦' + Number(n).toLocaleString('en-NG');
        const calc = () => {
            const u = m.value === 'yard' ? BENAKI_PRODUCT.yard : BENAKI_PRODUCT.trouser;
            total.textContent = fmt((Number(q.value) || 0) * u)
        };
        q.addEventListener('input', calc);
        m.addEventListener('change', calc);
        f.addEventListener('submit', async e => {
            e.preventDefault();
            res.className = 'text-sm text-slate-500';
            res.textContent = 'Adding to cart…';
            const btn = f.querySelector('button');
            btn.disabled = true;
            try {
                const r = await fetch(BENAKI_PRODUCT.endpoint, {
                    method: 'POST',
                    body: new FormData(f),
                    headers: {
                        Accept: 'application/json'
                    },
                    credentials: 'same-origin'
                });
                const d = await r.json();
                if (!d.ok) throw new Error(d.message);
                res.className = 'text-sm text-emerald-700 font-bold';
                res.textContent = 'Added to your cart.';
                setTimeout(() => location.href = d.cart_url, 450);
            } catch (err) {
                res.className = 'text-sm text-red-600';
                res.textContent = err.message || 'Could not add to cart.';
                btn.disabled = false;
            }
        });
        calc();
    });
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>