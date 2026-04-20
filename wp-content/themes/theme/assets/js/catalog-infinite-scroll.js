/**
 * Каталог: подгрузка следующих страниц (по 12 товаров) при скролле.
 * Запрашивает те же URL, что и пагинация WooCommerce.
 */
(function () {
	if (typeof fermaCatalogInfinite === 'undefined' || !fermaCatalogInfinite.pageUrls || !fermaCatalogInfinite.pageUrls.length) {
		return;
	}

	var list = document.querySelector('ul.products');
	if (!list) {
		return;
	}

	var urls = fermaCatalogInfinite.pageUrls;
	var idx = 0;
	var loading = false;
	var finished = false;

	var sentinel = document.createElement('div');
	sentinel.className = 'ferma-catalog-infinite-sentinel';
	sentinel.setAttribute('aria-hidden', 'true');
	sentinel.innerHTML =
		'<div class="ferma-catalog-infinite-loader" style="display:none;text-align:center;padding:28px 0 40px;color:#666;font-size:14px;">' +
		'<span class="ferma-catalog-infinite-spinner" aria-hidden="true"></span> ' +
		'<span class="ferma-catalog-infinite-loading-text"></span></div>' +
		'<p class="ferma-catalog-infinite-end" style="display:none;text-align:center;color:#aaa;font-size:14px;padding:20px 0 32px;"></p>';

	var parent = list.parentNode;
	if (parent) {
		parent.insertBefore(sentinel, list.nextSibling);
	} else {
		return;
	}

	var loaderWrap = sentinel.querySelector('.ferma-catalog-infinite-loader');
	var loadingText = sentinel.querySelector('.ferma-catalog-infinite-loading-text');
	var endEl = sentinel.querySelector('.ferma-catalog-infinite-end');
	if (loadingText && fermaCatalogInfinite.i18nLoading) {
		loadingText.textContent = fermaCatalogInfinite.i18nLoading;
	}

	function showLoader(on) {
		if (loaderWrap) {
			loaderWrap.style.display = on ? 'block' : 'none';
		}
	}

	function showDone() {
		finished = true;
		showLoader(false);
		if (endEl && fermaCatalogInfinite.i18nDone) {
			endEl.style.display = 'block';
			endEl.textContent = fermaCatalogInfinite.i18nDone;
		}
		if (obs) {
			obs.disconnect();
		}
	}

	function appendFromHtml(html) {
		var doc = new DOMParser().parseFromString(html, 'text/html');
		var fromList = doc.querySelector('ul.products');
		if (!fromList) {
			return 0;
		}
		var items = fromList.querySelectorAll(':scope > li.product');
		var n = 0;
		items.forEach(function (li) {
			list.appendChild(li);
			n++;
		});
		return n;
	}

	function loadNext() {
		if (loading || finished || idx >= urls.length) {
			if (idx >= urls.length) {
				showDone();
			}
			return;
		}
		loading = true;
		showLoader(true);

		fetch(urls[idx], {
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
			redirect: 'follow',
		})
			.then(function (res) {
				if (!res.ok) {
					throw new Error('HTTP ' + res.status);
				}
				return res.text();
			})
			.then(function (html) {
				appendFromHtml(html);
				idx++;
				loading = false;
				showLoader(false);
				if (idx >= urls.length) {
					showDone();
				}

				if (window.jQuery && window.jQuery(document.body).trigger) {
					window.jQuery(document.body).trigger('wc_fragment_refresh');
					window.jQuery(document.body).trigger('ferma_catalog_infinite_loaded');
				}
			})
			.catch(function () {
				loading = false;
				showLoader(false);
				finished = true;
				if (endEl && fermaCatalogInfinite.i18nError) {
					endEl.style.display = 'block';
					endEl.textContent = fermaCatalogInfinite.i18nError;
				}
				if (obs) {
					obs.disconnect();
				}
			});
	}

	var obs = null;
	if ('IntersectionObserver' in window) {
		obs = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting && !loading && !finished && idx < urls.length) {
						loadNext();
					}
				});
			},
			{ root: null, rootMargin: '500px 0px 400px', threshold: 0 }
		);
		obs.observe(sentinel);
	} else {
		var scrollTimer;
		window.addEventListener(
			'scroll',
			function () {
				if (loading || finished) {
					return;
				}
				clearTimeout(scrollTimer);
				scrollTimer = setTimeout(function () {
					var rect = sentinel.getBoundingClientRect();
					if (rect.top < window.innerHeight + 500 && idx < urls.length) {
						loadNext();
					}
				}, 120);
			},
			{ passive: true }
		);
	}
})();
