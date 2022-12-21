<section class="product-miniature clearfix">
    <p class="h5 text-uppercase">{l s='Checkout customer photos of this product' mod='customproductimages'}</p>
    <div class="products">
        {foreach $customProductImageLinks as $imageLink}
        <article class="product-miniature">
            <div class="thumbnail-container reviews-loaded">
                <img src="{$imageLink}" loading="lazy" width="250" height="250">
            </div>
        </article>
        {/foreach}
    </div>
</section>