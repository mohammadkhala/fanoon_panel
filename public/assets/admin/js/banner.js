$('#redirection_type').on('change', function (){
    let type = $(this).val();
    show_item(type);
})

function show_item(type) {
    if (type === 'product') {
        $('.type-product').show();
        $('.type-category').hide();
    } else {
        $('.type-product').hide();
        $('.type-category').show();
    }
}

