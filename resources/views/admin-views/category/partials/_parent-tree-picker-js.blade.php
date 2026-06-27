{{-- سكربت شجرة اختيار التصنيف الأب --}}
(function() {
    var $picker = $('.category-parent-tree-picker');
    if (!$picker.length) return;
    var $input = $picker.find('.category-parent-id-input');

    $picker.on('click', '.category-tree-toggle', function(e) {
        e.stopPropagation();
        $(this).closest('.category-tree-node').toggleClass('collapsed');
    });
    $picker.on('click', '.category-tree-item', function(e) {
        if ($(e.target).closest('.category-tree-toggle').length) return;
        var id = $(this).closest('.category-tree-node').data('id');
        $picker.find('.category-tree-item').removeClass('bg-primary text-white').addClass('hover-bg-light');
        $(this).removeClass('hover-bg-light').addClass('bg-primary text-white');
        $input.val(id).trigger('change');
    });
    $picker.closest('form').on('reset', function() {
        setTimeout(function() {
            $picker.find('.category-tree-item').removeClass('bg-primary text-white').addClass('hover-bg-light');
            $input.val('');
        }, 0);
    });
})();
