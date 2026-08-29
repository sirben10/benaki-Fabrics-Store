const prices = {
  Jonkoso: { yard: 3000, trouser_length: 4000 },
  Crepe: { yard: 2500, trouser_length: 3000 },
  Stock: { yard: 4000, trouser_length: 5000 },
  Vintage: { yard: 2000, trouser_length: 2000 },
  Chinos: { yard: 3000, trouser_length: 3000 }
};

const naira = new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', maximumFractionDigits: 0 });
const $ = (s, p = document) => p.querySelector(s);
const $$ = (s, p = document) => [...p.querySelectorAll(s)];

function openModal(id) { const el = document.getElementById(id); if (el) { el.classList.add('is-open'); document.body.classList.add('modal-open'); } }
function closeModal(id) { const el = document.getElementById(id); if (el) { el.classList.remove('is-open'); if (!document.querySelector('.modal.is-open')) document.body.classList.remove('modal-open'); } }

window.openModal = openModal; window.closeModal = closeModal;

document.addEventListener('DOMContentLoaded', () => {
  // Mobile navigation
  const navBtn = $('#navToggle'); const nav = $('#mobileNav');
  navBtn?.addEventListener('click', () => nav.classList.toggle('hidden'));
  $$('#mobileNav a').forEach(a => a.addEventListener('click', () => nav.classList.add('hidden')));

  // Header CTA buttons
  $$('#orderCta, #orderCta2, [data-order]').forEach(btn => btn.addEventListener('click', () => openModal('orderModal')));
  $$('#contactCta').forEach(btn => btn.addEventListener('click', () => openModal('contactModal')));
  $$('.modal').forEach(modal => modal.addEventListener('click', e => { if (e.target === modal) closeModal(modal.id); }));
  $$('[data-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.close)));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') $$('.modal.is-open').forEach(m => closeModal(m.id)); });

  // Flyer rotator: random at first load, then a different design every 15 seconds.
  const flyerModal = $('#flyerModal'); const flyers = $$('.flyer-design'); let current = -1;
  function showFlyer(forceOpen = true) {
    if (!flyers.length) return;
    let next = Math.floor(Math.random() * flyers.length);
    if (flyers.length > 1 && next === current) next = (next + 1) % flyers.length;
    current = next;
    flyers.forEach((f, i) => f.classList.toggle('active', i === current));
    if (forceOpen) openModal('flyerModal');
  }
  setTimeout(() => showFlyer(true), 900);
  setInterval(() => showFlyer(true), 15000);
  $('#nextFlyer')?.addEventListener('click', () => showFlyer(true));

  // Order calculator
  const form = $('#orderForm');
  const fabric = $('#fabric_type'); const measurementInputs = $$('input[name="measurement"]'); const qty = $('#quantity');
  function getMeasurement() {
    return measurementInputs.find(input => input.checked)?.value || '';
  }
  function updateOrderTotal() {
    const f = fabric?.value || ''; const m = getMeasurement(); const q = parseFloat(qty?.value) || 0;
    const unit = prices[f]?.[m] || 0; const total = unit * q;
    $('#unitPrice').textContent = naira.format(unit);
    $('#lineTotal').textContent = naira.format(total);
    $('#summaryFabric').textContent = f || '—';
    $('#summaryMeasurement').textContent = m === 'trouser_length' ? '45-inch trouser length' : m === 'yard' ? 'Per yard (36 inches)' : '—';
    $('#summaryQty').textContent = q || '—';
    $('#summaryTotal').textContent = naira.format(total);
  }
  fabric?.addEventListener('change', updateOrderTotal);
  qty?.addEventListener('input', updateOrderTotal);
  measurementInputs.forEach(el => el.addEventListener('change', updateOrderTotal));
  updateOrderTotal();

  form?.addEventListener('submit', async e => {
    e.preventDefault();
    if (!form.reportValidity()) return;

    const selectedColors = $$('input[name="colors[]"]:checked', form);
    if (!selectedColors.length) {
      alert('Please select at least one colour.');
      return;
    }

    const btn = $('#orderSubmit');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Sending...';

    try {
      const res = await fetch('ajax/order.php', {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const contentType = res.headers.get('content-type') || '';
      if (!contentType.includes('application/json')) {
        throw new Error('The server returned an unexpected response. Please check your PHP/database configuration.');
      }

      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error(data.message || 'Unable to submit order.');

      form.classList.add('hidden');
      $('#orderSuccess').classList.remove('hidden');
      $('#orderSuccessRef').textContent = `Order Reference: ${data.order_ref}`;
      $('#orderSuccessTotal').textContent = `Estimated total: ${data.currency}${data.total}`;
    } catch (err) {
      alert(err.message || 'Unable to submit order. Please try again.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  });

  $('#newOrder')?.addEventListener('click', () => { form.reset(); form.classList.remove('hidden'); $('#orderSuccess').classList.add('hidden'); updateOrderTotal(); });

  // Contact AJAX
  const contact = $('#contactForm');
  contact?.addEventListener('submit', async e => {
    e.preventDefault(); const btn = $('#contactSubmit'); btn.disabled = true; btn.textContent = 'Sending...';
    try {
      const res = await fetch('ajax/contact.php', { method: 'POST', body: new FormData(contact) });
      const data = await res.json();
      if (!data.ok) throw new Error(data.message);
      contact.reset(); $('#contactSuccess').classList.remove('hidden');
    } catch (err) { alert(err.message || 'Unable to send message.'); }
    finally { btn.disabled = false; btn.textContent = 'Send Message'; }
  });
});
