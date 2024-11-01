jQuery(document).ready(function($) {
    // Loop through each product wrapper
    $(".sona-payments-product-wrapper-stripe, .sona-payments-product-wrapper-paypal").each(function() {
        const stripe_block = $(this);
        const blockId = stripe_block.data("id");

        // Find the carousel container based on the block ID
        const container = stripe_block.find("#myCarousel-" + blockId);

        // Check if the container element exists
        if (container.length) {
            // Options for the carousel
            const options = {
                Dots: false,
                Thumbs: {
                    type: "classic",
                },
            };

            // Initialize the carousel for this specific block
            new Carousel(container[0], options, { Thumbs });
        }

        Fancybox.bind('[data-fancybox="gallery-' + blockId + '"]', {
            Thumbs: {
                autoStart: true,
            },
        });
    });

});
