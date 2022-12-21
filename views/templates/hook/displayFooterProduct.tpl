<section class="custom-image clearfix">
    <p class="h5 text-uppercase">{l s='Checkout customer photos of this product' mod='customproductimages'}</p>
    <div class="product-custom-images">
        {foreach $customProductImageLinks as $imageLink}
        <article class="custom-image">
            <div class="thumbnail-container reviews-loaded">
                <img src="{$imageLink}" loading="lazy" width="250" height="250">
            </div>
        </article>
        {/foreach}
    </div>
</section>