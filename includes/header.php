<?php
$pageTitle = $pageTitle ?? 'Benaki Fabrics';
$active = $active ?? '';
$cfg = $config ?? ($GLOBALS['config'] ?? []);
$business = $cfg['business'] ?? [];
$navItems = [
    'home' => ['Home', 'index.php'],
    'fabrics' => ['Fabrics', 'fabrics.php'],
    'about' => ['About Us', 'about.php'],
    'gallery' => ['Gallery', 'gallery.php'],
    'anniversary' => ['Anniversary', 'anniversary.php'],
    'contact' => ['Contact', 'contact.php'],
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | Benaki Fabrics</title>
<meta name="description" content="Benaki Fabrics — premium quality fabrics, sales and supply across South East and South South Nigeria.">
<meta name="theme-color" content="#061a31">
<link rel="icon" href="<?= e(media_url('assets/img/benaki-logo.jpg')) ?>">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{colors:{benaki:{navy:'#061a31',gold:'#d9a11e',cream:'#f7f4ed'}}}}}</script>
<link rel="stylesheet" href="<?= e(base_url('assets/css/styles.css')) ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<a href="#main-content" class="skip-link">Skip to content</a>
<div class="bg-benaki-navy text-white/80 text-[11px] border-b border-white/10">
  <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-between gap-4">
    <div class="hidden sm:flex items-center gap-5"><span>Calabar, Cross River State</span><span>•</span><span><?= e($business['delivery'] ?? 'South East & South South Nigeria') ?></span></div>
    <div class="ml-auto flex items-center gap-4"><a href="tel:<?= e($business['phone1'] ?? '08133314846') ?>" class="hover:text-amber-300">Call <?= e($business['phone1'] ?? '08133314846') ?></a><a href="https://wa.me/<?= e($business['whatsapp'] ?? '2348133314846') ?>" target="_blank" rel="noopener" class="hover:text-amber-300">WhatsApp</a></div>
  </div>
</div>
<header id="siteHeader" class="site-header sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200">
  <div class="max-w-7xl mx-auto px-4">
    <div class="h-[76px] flex items-center justify-between gap-4">
      <a href="<?= e(base_url('index.php')) ?>" class="brand flex items-center gap-3 min-w-0" aria-label="Benaki Fabrics home">
        <img src="<?= e(media_url('assets/img/benaki-logo.jpg')) ?>" class="h-12 w-12 rounded-xl object-cover shadow-sm" alt="Benaki Fabrics logo">
        <span class="leading-none"><strong class="block text-benaki-navy tracking-[.08em] text-sm sm:text-base">BENAKI FABRICS</strong><small class="block mt-1 text-[10px] text-amber-700 tracking-[.16em]">FINEST QUALITY TEXTILES</small></span>
      </a>
      <nav class="desktop-nav hidden lg:flex items-center gap-1" aria-label="Primary navigation">
        <?php foreach ($navItems as $key => [$label,$path]): ?>
          <a href="<?= e(base_url($path)) ?>" class="nav-link <?= $active === $key ? 'is-active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="flex items-center gap-2">
        <a href="<?= e(base_url('fabrics.php')) ?>" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-benaki-navy font-bold text-xs hover:border-amber-400 transition">Browse Store</a>
        <a href="<?= e(base_url('fabrics.php')) ?>" class="order-cta inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-benaki-gold text-benaki-navy font-extrabold text-xs shadow-sm hover:shadow-md transition">Order Now <span aria-hidden="true">→</span></a>
        <button id="menuBtn" type="button" class="lg:hidden h-11 w-11 rounded-xl border border-slate-200 text-benaki-navy grid place-items-center" aria-expanded="false" aria-controls="mobileNav" aria-label="Open menu"><span class="hamburger"><i></i><i></i><i></i></span></button>
      </div>
    </div>
  </div>
  <div id="mobileNav" class="mobile-drawer lg:hidden" aria-hidden="true">
    <div class="mobile-drawer-inner">
      <div class="flex items-center justify-between mb-4"><div><span class="text-[10px] uppercase tracking-[.2em] text-amber-700 font-bold">Menu</span><p class="font-bold text-benaki-navy mt-1">Explore Benaki Fabrics</p></div><button id="closeMobileNav" type="button" class="h-10 w-10 rounded-lg bg-slate-100 text-xl" aria-label="Close menu">×</button></div>
      <nav class="grid gap-1" aria-label="Mobile navigation">
        <?php foreach ($navItems as $key => [$label,$path]): ?><a href="<?= e(base_url($path)) ?>" class="mobile-link <?= $active === $key ? 'is-active' : '' ?>"><span><?= e($label) ?></span><span>→</span></a><?php endforeach; ?>
      </nav>
      <div class="mt-5 p-4 rounded-2xl bg-benaki-navy text-white"><p class="text-xs text-amber-300 font-bold uppercase tracking-widest">Ready to order?</p><p class="text-sm text-white/75 mt-1">Choose a fabric and send your order request.</p><a href="<?= e(base_url('fabrics.php')) ?>" class="mt-3 inline-flex bg-benaki-gold text-benaki-navy font-bold px-4 py-2.5 rounded-lg text-xs">Shop Fabrics</a></div>
    </div>
  </div>
  <div id="navOverlay" class="nav-overlay lg:hidden"></div>
</header>
<main id="main-content">
