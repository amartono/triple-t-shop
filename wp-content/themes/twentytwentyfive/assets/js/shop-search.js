(function () {
  var searchBar = document.querySelector('.ttt-product-search');
  if (!searchBar) return;

  var input = document.getElementById('ttt-search-input');
  var hidden = document.getElementById('ttt-search-input-hidden');
  var wrapper = searchBar;
  var dropdown = null;
  var debounceTimer = null;
  var cache = {};
  var searchUrl = '/index.php?rest_route=/wc/store/v1/products&per_page=5&search=';

  function createDropdown() {
    dropdown = document.createElement('div');
    dropdown.className = 'ttt-search-dropdown';
    dropdown.style.cssText = 'display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-radius:0 0 12px 12px;z-index:1000;max-height:300px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.12)';
    wrapper.style.position = 'relative';
    wrapper.appendChild(dropdown);
  }

  function hideDropdown() {
    if (dropdown) { dropdown.style.display = 'none'; dropdown.innerHTML = ''; }
  }

  function showResults(results, query) {
    if (!dropdown) createDropdown();
    dropdown.innerHTML = '';
    if (!results || results.length === 0) {
      dropdown.innerHTML = '<div style="padding:12px 16px;color:#999;font-size:.875rem">No products found</div>';
    } else {
      results.forEach(function (p) {
        var item = document.createElement('a');
        item.href = p.permalink;
        item.style.cssText = 'display:flex;align-items:center;gap:12px;padding:10px 16px;text-decoration:none;color:#3D0C02;border-bottom:1px solid #f5e6d3;transition:background .2s';
        item.addEventListener('mouseenter', function () { item.style.background = '#fdf3e7'; });
        item.addEventListener('mouseleave', function () { item.style.background = ''; });

        var img = document.createElement('img');
        img.src = p.images && p.images[0] ? p.images[0].thumbnail : '';
        img.alt = p.name;
        img.style.cssText = 'width:36px;height:36px;border-radius:8px;object-fit:cover';

        var info = document.createElement('div');
        info.style.cssText = 'flex:1;min-width:0';

        var nameEl = document.createElement('div');
        nameEl.innerHTML = highlightMatch(p.name, query);
        nameEl.style.cssText = 'font-size:.9rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis';

        var priceEl = document.createElement('div');
        priceEl.textContent = p.prices.price;
        priceEl.style.cssText = 'font-size:.8rem;color:#C6742E;font-weight:600';

        info.appendChild(nameEl);
        info.appendChild(priceEl);
        item.appendChild(img);
        item.appendChild(info);
        dropdown.appendChild(item);
      });
    }
    dropdown.style.display = 'block';
  }

  function highlightMatch(text, query) {
    var idx = text.toLowerCase().indexOf(query.toLowerCase());
    if (idx === -1) return text;
    return text.substring(0, idx) + '<strong style="color:#C6742E">' + text.substring(idx, idx + query.length) + '</strong>' + text.substring(idx + query.length);
  }

  function fetchProducts(query) {
    if (query.length < 3) { hideDropdown(); return; }
    if (cache[query]) { showResults(cache[query], query); return; }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
      fetch(searchUrl + encodeURIComponent(query))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          cache[query] = data;
          showResults(data, query);
        })
        .catch(function () { hideDropdown(); });
    }, 250);
  }

  input.addEventListener('input', function () {
    if (hidden) hidden.value = input.value.trim();
    fetchProducts(input.value.trim());
  });

  input.addEventListener('focus', function () {
    var val = input.value.trim();
    if (val.length >= 3 && cache[val]) showResults(cache[val], val);
  });

  document.addEventListener('click', function (e) {
    if (!wrapper.contains(e.target)) hideDropdown();
  });

  input.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') hideDropdown();
    if (e.key === 'Enter') {
      if (dropdown && dropdown.style.display === 'block') {
        var first = dropdown.querySelector('a');
        if (first) { e.preventDefault(); first.click(); return; }
      }
      if (hidden) hidden.value = input.value.trim();
    }
  });

  // Pre-fill from URL if coming from a search
  var urlParams = new URLSearchParams(window.location.search);
  var existingSearch = urlParams.get('ttt_search');
  if (existingSearch && input) {
    input.value = existingSearch;
    if (hidden) hidden.value = existingSearch;
  }
})();
