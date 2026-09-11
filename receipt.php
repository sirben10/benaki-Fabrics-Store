<?php
require __DIR__ . '/includes/bootstrap.php';
$ref = trim((string)($_GET['ref'] ?? ''));
$token = trim((string)($_GET['token'] ?? ''));
$stmt = $pdo->prepare('SELECT * FROM orders WHERE order_ref=? AND receipt_token=? AND payment_status="paid" LIMIT 1');
$stmt->execute([$ref, $token]);
$order = $stmt->fetch();
if (!$order) {
    http_response_code(404);
    $pageTitle = 'Receipt Not Found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="max-w-3xl mx-auto px-4 py-24 text-center"><h1 class="text-3xl font-extrabold text-benaki-navy">Receipt unavailable</h1><p class="text-slate-500 mt-2">The receipt link is invalid or the payment has not been verified.</p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
$itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id=? ORDER BY id');
$itemsStmt->execute([$order['id']]);
$items = $itemsStmt->fetchAll();
$pageTitle = 'Payment Receipt ' . $order['order_ref'];
$active = '';
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white border rounded-3xl overflow-hidden shadow-sm">
        <div class="bg-benaki-navy text-white p-8 sm:p-10 text-center">
            <div class="inline-grid place-items-center w-16 h-16 rounded-full bg-emerald-400/20 text-emerald-300 text-3xl">✓</div>
            <p class="text-xs uppercase tracking-[.25em] text-amber-300 font-bold mt-5">Payment successful</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold mt-2">Thank you for shopping with Benaki Fabrics</h1>
            <p class="text-white/70 mt-3">Your order has been received and payment verified.</p>
        </div>
        <div class="p-6 sm:p-10">
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="rounded-xl bg-slate-50 p-4"><small class="text-slate-500">Order reference</small><b class="block mt-1"><?= e($order['order_ref']) ?></b></div>
                <div class="rounded-xl bg-slate-50 p-4"><small class="text-slate-500">Payment reference</small><b class="block mt-1 break-all"><?= e($order['paystack_reference'] ?: '—') ?></b></div>
                <div class="rounded-xl bg-slate-50 p-4"><small class="text-slate-500">Paid on</small><b class="block mt-1"><?= e($order['paid_at'] ?: $order['created_at']) ?></b></div>
            </div>
            <div class="mt-8">
                <h2 class="text-xl font-extrabold text-benaki-navy">Order items</h2>
                <div class="mt-4 border rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-left p-3">Fabric</th>
                                <th class="text-left p-3">Details</th>
                                <th class="text-right p-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($items as $it): ?><tr class="border-t">
                                    <td class="p-3 font-bold"><?= e($it['product_name']) ?></td>
                                    <td class="p-3 text-slate-500">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            <?php foreach (json_list($it['colors']) as $color): ?>
                                                <span class="inline-flex items-center gap-1.5" title="<?= e((string)$color) ?>">
                                                    <span class="inline-block w-4 h-4 rounded-full border border-slate-300 shadow-inner" style="background-color: <?= e(color_swatch((string)$color)) ?>" aria-hidden="true"></span>
                                                    <span><?= e((string)$color) ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                        <span class="block mt-1 text-xs text-slate-500"><?= e($it['measurement'] === 'yard' ? 'Yard' : 'Trouser length') ?> · <?= e((string)$it['quantity']) ?></span>
                                    </td>
                                    <td class="p-3 text-right font-bold"><?= money((float)$it['line_total']) ?></td>
                                </tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
            <div class="max-w-sm ml-auto mt-6 space-y-3 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><b><?= money((float)$order['subtotal']) ?></b></div>
                <div class="flex justify-between"><span>Anniversary saving</span><b class="text-emerald-700">-<?= money((float)$order['discount']) ?></b></div>
                <div class="flex justify-between border-t pt-4 text-xl text-benaki-navy"><span class="font-extrabold">Paid total</span><b><?= money((float)$order['total_amount']) ?></b></div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4 mt-8">
                <div class="border rounded-2xl p-5">
                    <h3 class="font-extrabold text-benaki-navy">Customer</h3>
                    <p class="text-sm text-slate-600 mt-2"><?= e($order['fullname']) ?><br><?= e($order['email']) ?><br><?= e($order['phone']) ?><br><?= e($order['location']) ?></p>
                </div>
                <div class="border rounded-2xl p-5">
                    <h3 class="font-extrabold text-benaki-navy">Next step</h3>
                    <p class="text-sm text-slate-600 mt-2">Our team will contact you to confirm fabric availability and delivery arrangements.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 mt-8"><button onclick="window.print()" class="bg-benaki-navy text-white px-5 py-3 rounded-xl font-bold">Print / Save Receipt</button><a href="<?= e(base_url('fabrics.php')) ?>" class="border px-5 py-3 rounded-xl font-bold">Continue Shopping</a></div>
        </div>
    </div>
</section>
<style>
    @media print {

        header,
        footer,
        .site-header {
            display: none !important
        }

        body {
            background: #fff !important
        }

        .shadow-sm {
            box-shadow: none !important
        }
    }
</style>
<?php require __DIR__ . '/includes/footer.php'; ?>