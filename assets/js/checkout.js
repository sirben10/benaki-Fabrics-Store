document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('checkoutForm');
	const button = document.getElementById('payBtn');
	const result = document.getElementById('checkoutResult');
	if (!form) return;

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		button.disabled = true;
		const originalLabel = button.textContent;
		button.textContent = 'Preparing secure payment...';
		result.textContent = '';

		try {
			const response = await fetch(BENAKI_CHECKOUT.endpoint, {
				method: 'POST',
				body: new FormData(form),
				headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
				credentials: 'same-origin'
			});
			const data = await response.json();
			if (!response.ok || !data.ok) throw new Error(data.message || 'Unable to initialize payment.');
			if (!data.authorization_url) throw new Error('Paystack did not return a checkout URL.');
			window.location.assign(data.authorization_url);
		} catch (error) {
			result.className = 'text-sm text-red-600';
			result.textContent = error.message || 'Unable to start payment.';
			button.disabled = false;
			button.textContent = originalLabel;
		}
	});
});
