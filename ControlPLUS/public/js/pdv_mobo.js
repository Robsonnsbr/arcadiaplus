$('.pdv-cat').on('click', function() {
    $('.pdv-cat').removeClass('active')
    $(this).addClass('active')
    let cat = $(this).data('cat')
    console.log("Categoria:", cat)
})

$('.pdv-card').on('click', function() {
    let id = $(this).data('id')
    console.log("Produto selecionado:", id)
})