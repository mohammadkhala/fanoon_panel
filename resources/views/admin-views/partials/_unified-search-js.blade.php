<script>
(function() {
    var wrap = document.getElementById('unified-search-wrap');
    var input = document.getElementById('unified-search-input');
    var dropdown = document.getElementById('unified-search-dropdown');
    if (!input || !dropdown) return;

    var debounceTimer;
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = (input.value || '').trim();
        if (q.length < 2) {
            dropdown.style.display = 'none';
            return;
        }
        debounceTimer = setTimeout(function() {
            fetch('{{ route("admin.unified-search") }}?q=' + encodeURIComponent(q))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    renderSection('unified-search-orders', data.orders || [], '{{ translate("no_results") ?: "لا توجد نتائج" }}');
                    renderSection('unified-search-products', data.products || [], '{{ translate("no_results") ?: "لا توجد نتائج" }}');
                    renderSection('unified-search-customers', data.customers || [], '{{ translate("no_results") ?: "لا توجد نتائج" }}');
                    dropdown.style.display = 'block';
                })
                .catch(function() { dropdown.style.display = 'none'; });
        }, 300);
    });

    input.addEventListener('focus', function() {
        if (input.value.trim().length >= 2 && dropdown.innerHTML) dropdown.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        var inside = (wrap && wrap.contains(e.target)) || dropdown.contains(e.target);
        if (!inside) dropdown.style.display = 'none';
    });

    function renderSection(id, items, emptyText) {
        var el = document.getElementById(id);
        if (!el) return;
        if (items.length === 0) {
            el.innerHTML = '<div class="dropdown-item-text text-muted small">' + emptyText + '</div>';
        } else {
            el.innerHTML = items.map(function(i) {
                return '<a class="dropdown-item py-2" href="' + (i.url || '#') + '">' + (i.label || '') + '</a>';
            }).join('');
        }
    }
})();
</script>
