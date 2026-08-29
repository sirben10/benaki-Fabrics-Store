<?php
require __DIR__.'/includes/bootstrap.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ? AND is_active = 1 LIMIT 1');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Fabric Not Found';
    require __DIR__.'/includes/header.php';
    ?>
    <section class="max-w-4xl mx-auto px-4 py-24 text-center">
      <div class="inline-grid place-items-center w-16 h-16 rounded-2xl bg-amber-50 text-amber-700 text-2xl">!</div>
      <h1 class="text-3xl font-extrabold mt-6">Fabric not found</h1>
      <p class="text-slate-500 mt-2">The fabric may have been removed or is currently unavailable.</p>
      <a class="inline-flex mt-6 bg-benaki-navy text-white px-5 py-3 rounded-xl font-bold" href="<?=e(base_url('fabrics.php'))?>">← Back to fabrics</a>
    </section>
    <?php
    require __DIR__.'/includes/footer.php';
    exit;
}

$pageTitle = $product['name'].' Fabric';
$active = 'fabrics';
require __DIR__.'/includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mb-7">
    <a href="<?=e(base_url('index.php'))?>" class="hover:text-amber-700">Home</a><span>/</span>
    <a href="<?=e(base_url('fabrics.php'))?>" class="hover:text-amber-700">Fabrics</a><span>/</span>
    <span class="text-slate-900 font-semibold"><?=e($product['name'])?></span>
  </div>
  <div class="grid lg:grid-cols-[1.05fr_.95fr] gap-10 items-start">
    <div class="lg:sticky lg:top-28">
      <div class="rounded-3xl overflow-hidden bg-white border shadow-sm">
        <img src="<?=e(media_url($product['image_path']))?>" class="w-full aspect-[4/3] lg:aspect-square object-cover" alt="<?=e($product['name'])?> fabric">
      </div>
      <div class="grid grid-cols-3 gap-3 mt-3">
        <div class="rounded-xl border bg-white p-2 text-center text-[10px] font-bold text-slate-500">Premium quality</div>
        <div class="rounded-xl border bg-white p-2 text-center text-[10px] font-bold text-slate-500">Multiple colours</div>
        <div class="rounded-xl border bg-white p-2 text-center text-[10px] font-bold text-slate-500">Regional delivery</div>
      </div>
    </div>
    <div>
      <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-[10px] uppercase tracking-[.18em] font-extrabold text-amber-700">Premium fabric</span>
      <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mt-3 text-benaki-navy"><?=e($product['name'])?></h1>
      <p class="mt-4 text-slate-600 leading-7 text-base"><?=e($product['description'])?></p>
      <div class="grid sm:grid-cols-2 gap-4 mt-7">
        <div class="rounded-2xl border bg-white p-5"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">36 inches · per yard</span><strong class="block text-2xl mt-2 text-benaki-navy"><?=money((float)$product['yard_price'])?></strong></div>
        <div class="rounded-2xl border bg-white p-5"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">45 inches · trouser length</span><strong class="block text-2xl mt-2 text-benaki-navy"><?=money((float)$product['trouser_price'])?></strong></div>
      </div>
      <div class="mt-8 rounded-2xl bg-white border p-5 shadow-sm">
        <div class="flex flex-wrap gap-2 border-b pb-4 mb-5 text-xs font-extrabold text-slate-600"><span class="px-3 py-1 rounded-full bg-slate-100">Description</span><span class="px-3 py-1 rounded-full bg-slate-100">Details</span><span class="px-3 py-1 rounded-full bg-slate-100">Care</span></div>
        <div class="space-y-5 text-sm text-slate-600 leading-7">
          <div><?=nl2br(e($product['long_description'] ?: $product['description']))?></div>
          <?php if(!empty($product['details'])): ?><div><h2 class="font-bold text-slate-900 mb-1">Details</h2><?=nl2br(e($product['details']))?></div><?php endif; ?>
          <?php if(!empty($product['care_instructions'])): ?><div><h2 class="font-bold text-slate-900 mb-1">Care Instructions</h2><?=nl2br(e($product['care_instructions']))?></div><?php endif; ?>
        </div>
      </div>
      <button data-order-product="<?=e((string)$product['id'])?>" data-order-name="<?=e($product['name'])?>" class="mt-6 w-full bg-benaki-gold text-benaki-navy py-4 rounded-xl font-extrabold shadow-sm hover:shadow-md transition">Order <?=e($product['name'])?> Now →</button>
    </div>
  </div>
</section>

<div id="orderModal" class="hidden fixed inset-0 z-[100] bg-slate-950/70 p-4 items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="orderTitle">
  <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] overflow-auto p-6 md:p-7 relative shadow-2xl">
    <button id="closeOrder" type="button" class="absolute top-4 right-4 h-10 w-10 rounded-xl bg-slate-100 text-xl" aria-label="Close order form">×</button>
    <span class="text-[10px] uppercase tracking-[.2em] text-amber-700 font-extrabold">Benaki Fabrics</span>
    <h2 id="orderTitle" class="text-2xl font-extrabold mt-2 text-benaki-navy">Place Your Order</h2>
    <p id="orderProductLabel" class="text-sm text-slate-500 mt-1"></p>
    <form id="orderForm" class="mt-6 space-y-4">
      <input type="hidden" name="product_id" id="productId">
      <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
      <div><label class="label">Colours *</label><div class="grid grid-cols-3 sm:grid-cols-5 gap-2 text-xs"><?php foreach(['White','Cream','Gold','Navy','Black','Blue','Burgundy','Green','Purple','Brown','Grey'] as $color): ?><label class="border rounded-lg px-2.5 py-2 cursor-pointer hover:border-amber-400"><input type="checkbox" name="colors[]" value="<?=e($color)?>"> <?=e($color)?></label><?php endforeach; ?></div></div>
      <div class="grid sm:grid-cols-2 gap-4"><div><label class="label">Measurement *</label><select name="measurement" id="measurement" class="input"><option value="yard">Per Yard (36 inches)</option><option value="trouser_length">Per Trouser Length (45 inches)</option></select></div><div><label class="label">Quantity *</label><input name="quantity" id="quantity" type="number" min="0.5" step="0.5" value="1" class="input"></div></div>
      <div class="grid sm:grid-cols-2 gap-4"><div><label class="label">Full Name *</label><input name="fullname" required class="input"></div><div><label class="label">Phone *</label><input name="phone" required class="input"></div></div>
      <div><label class="label">Location *</label><input name="location" required class="input"></div>
      <div><label class="label">Order Description</label><textarea name="description" rows="3" class="input" placeholder="Tell us anything else we should know..."></textarea></div>
      <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4"><div class="flex justify-between text-sm"><span>Subtotal</span><strong id="subtotal">₦0</strong></div><div class="flex justify-between text-sm mt-2"><span>Anniversary discount</span><strong id="discount">₦0</strong></div><div class="flex justify-between text-lg mt-3 pt-3 border-t border-emerald-200"><span class="font-bold">Estimated total</span><strong id="total">₦0</strong></div></div>
      <div id="orderResult" class="text-sm" aria-live="polite"></div>
      <button type="submit" class="w-full bg-benaki-navy text-white py-3.5 rounded-xl font-extrabold">Submit Order Request</button>
    </form>
  </div>
</div>

<script>
window.BENAKI_ORDER={id:<?=json_encode((int)$product['id'])?>,name:<?=json_encode($product['name'])?>,yard:<?=json_encode((float)$product['yard_price'])?>,trouser:<?=json_encode((float)$product['trouser_price'])?>};
window.BENAKI_ORDER_ENDPOINT=<?=json_encode(base_url('ajax/order.php'))?>;
document.addEventListener('DOMContentLoaded',()=>{
 const modal=document.getElementById('orderModal'),form=document.getElementById('orderForm'),q=document.getElementById('quantity'),m=document.getElementById('measurement'),result=document.getElementById('orderResult');
 const fmt=n=>'₦'+Number(n).toLocaleString('en-NG');
 const calc=()=>{const qty=Number(q.value)||0,unit=m.value==='yard'?BENAKI_ORDER.yard:BENAKI_ORDER.trouser,sub=qty*unit;let dis=0;if(qty>=5&&qty<=9.5)dis=1000;else if(qty>9.5&&qty<=14.5)dis=1500;else if(qty>14.5&&qty<=30)dis=2000;else if(qty>30)dis=3000;document.getElementById('subtotal').textContent=fmt(sub);document.getElementById('discount').textContent=dis?'-'+fmt(dis):fmt(0);document.getElementById('total').textContent=fmt(Math.max(0,sub-dis));};
 document.querySelector('[data-order-product]')?.addEventListener('click',()=>{document.getElementById('productId').value=BENAKI_ORDER.id;document.getElementById('orderProductLabel').textContent=BENAKI_ORDER.name;modal.classList.remove('hidden');modal.classList.add('flex');document.body.classList.add('menu-open');calc();});
 const close=()=>{modal.classList.add('hidden');modal.classList.remove('flex');document.body.classList.remove('menu-open');};
 document.getElementById('closeOrder')?.addEventListener('click',close);modal?.addEventListener('click',e=>{if(e.target===modal)close();});q?.addEventListener('input',calc);m?.addEventListener('change',calc);
 form?.addEventListener('submit',async e=>{e.preventDefault();result.className='text-sm text-slate-500';result.textContent='Submitting order…';const btn=form.querySelector('button[type=submit]');if(btn)btn.disabled=true;try{const res=await fetch(window.BENAKI_ORDER_ENDPOINT,{method:'POST',body:new FormData(form),headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});const raw=await res.text();let data;try{data=JSON.parse(raw);}catch(_){throw new Error('The server returned an unexpected response. Please check PHP/database configuration.');}if(!data.ok)throw new Error(data.message||'Could not submit order.');result.className='text-sm text-emerald-700 font-semibold';result.textContent=data.message+' Reference: '+data.order_ref;form.reset();document.getElementById('productId').value=BENAKI_ORDER.id;calc();}catch(err){result.className='text-sm text-red-600';result.textContent=err.message||'Could not submit order.';}finally{if(btn)btn.disabled=false;}});
});
</script>
<?php require __DIR__.'/includes/footer.php'; ?>
