</div></div></main>
<script>
(function () {
    /* Auto-slug from title field */
    function slugify(s) {
        return s.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }
    var title = document.getElementById('title');
    var slug  = document.getElementById('slug');
    if (title && slug) {
        var locked = slug.value !== '';
        title.addEventListener('input', function () {
            if (!locked) slug.value = slugify(this.value);
        });
        slug.addEventListener('input', function () {
            locked = this.value !== '';
        });
    }
    /* Confirm delete buttons */
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || 'Delete this item?')) e.preventDefault();
        });
    });
})();
</script>
</body>
</html>
