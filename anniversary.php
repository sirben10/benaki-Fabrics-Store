<?php
require __DIR__.'/includes/bootstrap.php';
$active='anniversary';
$pageTitle='1 Year Anniversary Campaign';
$flyers=glob(__DIR__.'/assets/img/flyers/*.{jpg,jpeg,png,webp}',GLOB_BRACE)?:[];
require __DIR__.'/includes/header.php';
?>
<section class="page-hero"><div class="max-w-7xl mx-auto px-4 py-16"><span>CELEBRATING ONE YEAR</span><h1>One year of quality. A special thank-you to our customers.</h1><p>The Benaki Fabrics anniversary is a campaign within our store — while our commitment to quality continues all year round.</p></div></section>
<section class="max-w-7xl mx-auto px-4 py-14">
  <div class="rounded-3xl overflow-hidden bg-benaki-navy text-white p-8 md:p-12 flex flex-col md:flex-row justify-between gap-8 items-center">
    <div><span class="text-amber-300 uppercase text-xs tracking-widest font-bold">Anniversary offer</span><h2 class="text-3xl md:text-4xl font-bold mt-2">Shop more, enjoy more.</h2><p class="text-slate-300 mt-3 max-w-2xl leading-7">Qualifying quantities receive anniversary discounts automatically during the campaign.</p></div>
    <a href="<?=e(base_url('fabrics.php'))?>" class="shrink-0 bg-benaki-gold text-benaki-navy font-extrabold px-6 py-3 rounded-xl">Shop Fabrics →</a>
  </div>
  <div class="mt-14"><span class="text-amber-700 text-xs uppercase tracking-widest font-bold">Campaign gallery</span><h2 class="text-3xl font-bold mt-2">Anniversary designs</h2><p class="text-slate-500 mt-2">A look at the creative materials used to celebrate our first year.</p></div>
  <?php if($flyers): ?><div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-7"><?php foreach($flyers as $f): ?><figure class="bg-white rounded-2xl overflow-hidden border shadow-sm"><img loading="lazy" src="<?=e(media_url(str_replace(__DIR__.'/','',$f)))?>" class="w-full h-auto" alt="Benaki Fabrics anniversary campaign design"></figure><?php endforeach; ?></div><?php else: ?><div class="mt-7 rounded-2xl bg-slate-100 p-10 text-center text-slate-500">Campaign designs will be displayed here.</div><?php endif; ?>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
