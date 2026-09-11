<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Shopping Cart';
$active = 'fabrics';
$cart = cart_items();
$subtotal = 0;
$itemCount = count($cart);
foreach ($cart as $item) {
    $line = (float)$item['unit_price'] * (float)$item['quantity'];
    $subtotal += $line;
}
$discount = cart_discount($cart);
$total = max(0, $subtotal - $discount);
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-7xl mx-auto px-4 py-14"><span>SHOPPING CART</span>
        <h1>Your selected fabrics</h1>
        <p>Review each colour item, quantity and measurement before checkout.</p>
    </div>
</section>
<section class="max-w-7xl mx-auto px-4 py-12">
    <?php if (!$cart): ?><div class="bg-white border rounded-3xl p-12 text-center">
            <div class="text-5xl">🛒</div>
            <h2 class="text-2xl font-extrabold text-benaki-navy mt-4">Your cart is empty</h2>
            <p class="text-slate-500 mt-2">Explore our collection and add fabrics you love.</p><a href="<?= e(base_url('fabrics.php')) ?>" class="inline-flex mt-6 bg-benaki-navy text-white px-6 py-3 rounded-xl font-bold">Browse Fabrics</a>
        </div>
    <?php else: ?>
        <div class="grid lg:grid-cols-[1fr_380px] gap-8 items-start">
            <div class="space-y-4">
                <?php foreach ($cart as $key => $item): $line = (float)$item['unit_price'] * (float)$item['quantity']; ?><article class="bg-white border rounded-2xl p-4 sm:p-5 flex gap-4 items-start"><img src="<?= e(media_url($item['image_path'])) ?>" class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-xl" alt="<?= e($item['name']) ?>">
                        <div class="flex-1">
                            <div class="flex justify-between gap-4">
                                <div>
                                    <h2 class="font-extrabold text-lg text-benaki-navy"><?= e($item['name']) ?></h2>
                                    <p class="text-xs text-slate-500 mt-1"><?= e(implode(', ', $item['colors'])) ?> · <?= e($item['measurement'] === 'yard' ? 'Per yard (36 inches)' : 'Per trouser length (45 inches)') ?></p>
                                </div><button class="cart-remove text-red-500 text-sm font-bold" data-key="<?= e($key) ?>">Remove</button>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-4 mt-5">
                                <div class="flex items-center gap-2"><label class="text-xs font-bold text-slate-500">Qty</label><input class="cart-qty w-24 border rounded-lg px-3 py-2" data-key="<?= e($key) ?>" type="number" min="0.5" step="0.5" value="<?= e((string)$item['quantity']) ?>"></div><strong class="text-xl text-benaki-navy"><?= money($line) ?></strong>
                            </div>
                        </div>
                    </article><?php endforeach; ?>
                <div class="flex justify-between"><a href="<?= e(base_url('fabrics.php')) ?>" class="text-sm font-bold text-benaki-navy">← Continue shopping</a><button id="clearCart" class="text-sm font-bold text-red-600">Clear cart</button></div>
            </div>
            <aside class="bg-benaki-navy text-white rounded-3xl p-6 lg:sticky lg:top-28">
                <p class="text-xs uppercase tracking-widest text-amber-300 font-bold">Order summary</p>
                <p class="text-xs text-white/60 mt-1"><?= e((string)$itemCount) ?> cart item<?= ($itemCount === 1 ? '' : 's') ?></p>
                <div class="flex justify-between mt-6 text-sm"><span>Subtotal</span><strong><?= money($subtotal) ?></strong></div>
                <div class="flex justify-between mt-3 text-sm"><span>Anniversary savings</span><strong class="text-amber-300">-<?= money($discount) ?></strong></div>
                <p class="text-[11px] text-white/60 mt-2">Anniversary savings are calculated for each cart item and applied to its price.</p>
                <div class="flex justify-between border-t border-white/10 pt-4 mt-4 text-xl"><span class="font-bold">Total</span><strong><?= money($total) ?></strong></div><a href="<?= e(base_url('checkout.php')) ?>" class="mt-6 block text-center bg-benaki-gold text-benaki-navy py-3.5 rounded-xl font-extrabold">Proceed to Checkout →</a>
                <p class="text-[11px] text-white/60 mt-4">Secure payment powered by Paystack. Your card details are handled by Paystack.</p>
            </aside>
        </div><?php endif; ?>
</section>
<script>
    window.BENAKI_CART_ENDPOINT = <?= json_encode(base_url('ajax/cart.php')) ?>;
</script>
<script src="<?= e(base_url('assets/js/cart.js')) ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>