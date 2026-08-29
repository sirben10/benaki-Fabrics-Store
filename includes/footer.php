</main>
<footer class="mt-20 bg-benaki-navy text-white">
  <div class="max-w-7xl mx-auto px-4 py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
    <div class="lg:col-span-1"><div class="flex items-center gap-3"><img src="<?= e(media_url('assets/img/benaki-logo.jpg')) ?>" class="w-12 h-12 rounded-xl object-cover" alt="Benaki Fabrics"><div><b class="tracking-wider">BENAKI FABRICS</b><small class="block text-[9px] text-amber-300 tracking-widest mt-1">FINEST QUALITY TEXTILES</small></div></div><p class="text-sm leading-7 text-slate-300 mt-5">Quality fabrics for every occasion, with dependable supply and delivery from Calabar to customers across South East and South South Nigeria.</p></div>
    <div><h3 class="footer-title">Explore</h3><div class="mt-4 grid gap-3 text-sm text-slate-300"><a href="<?= e(base_url('index.php')) ?>" class="footer-link">Home</a><a href="<?= e(base_url('fabrics.php')) ?>" class="footer-link">Fabrics</a><a href="<?= e(base_url('about.php')) ?>" class="footer-link">About Us</a><a href="<?= e(base_url('gallery.php')) ?>" class="footer-link">Gallery</a></div></div>
    <div><h3 class="footer-title">Customer Care</h3><div class="mt-4 grid gap-3 text-sm text-slate-300"><a href="<?= e(base_url('contact.php')) ?>" class="footer-link">Contact Us</a><a href="<?= e(base_url('anniversary.php')) ?>" class="footer-link">Anniversary Campaign</a><a href="tel:<?= e($business['phone1'] ?? '08133314846') ?>" class="footer-link">Call Us</a><a href="https://wa.me/<?= e($business['whatsapp'] ?? '2348133314846') ?>" target="_blank" rel="noopener" class="footer-link">WhatsApp</a></div></div>
    <div><h3 class="footer-title">Visit / Delivery</h3><p class="mt-4 text-sm leading-7 text-slate-300">Calabar, Cross River State<br><?= e($business['delivery'] ?? 'South East & South South Nigeria') ?></p><a href="<?= e(base_url('fabrics.php')) ?>" class="mt-5 inline-flex bg-benaki-gold text-benaki-navy px-5 py-3 rounded-xl font-extrabold text-xs">Shop the Collection →</a></div>
  </div>
  <div class="border-t border-white/10"><div class="max-w-7xl mx-auto px-4 py-5 flex flex-col sm:flex-row justify-between gap-2 text-xs text-slate-400"><span>© <?= date('Y') ?> Benaki Fabrics. All rights reserved.</span><span>Quality fabrics. Exceptional service. Always.</span></div></div>
</footer>
<script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body></html>
