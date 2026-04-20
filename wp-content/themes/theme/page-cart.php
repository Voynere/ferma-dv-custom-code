<?php
/*
Template Post Type: post, page, product
Template Name: Корзина
*/
?>

<?php get_header('home'); ?>

<main class="main">
    <div class="container">
        <div class="info_page ferma-checkout">
            <h1 class="page-title">ОФОРМЛЕНИЕ ЗАКАЗА</h1>
            <div class="ferma-checkout__container">
                <?php echo do_shortcode('[woocommerce_checkout]'); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer('home'); ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/imask.js"></script>
<script>
    let maskOptions = {
        mask: '+{7}(#00)0000000',

        definitions: {
            '#': /[01234569]/
        },
        lazy: false,
        placeholderChar: ' '
    };

    var phoneMask = IMask(
        document.getElementById('billing_phone'), {
            mask: '+{7}(#00)0000000',
            definitions: {
                '#': /[01234569]/
            },
            maskOptions
        });
    //$('#billing_phone').mask('+7 (999) 999-99-99');
    // $('#billing_email').inputmask({
    //   mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
    //   greedy: false,
    //   onBeforePaste: function (pastedValue, opts) {
    //     pastedValue = pastedValue.toLowerCase();
    //     return pastedValue.replace("mailto:", "");
    //   },
    //   definitions: {
    //     '*': {
    //       validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
    //       cardinality: 1,
    //       casing: "lower"
    //     }
    //   }
    // });
</script>