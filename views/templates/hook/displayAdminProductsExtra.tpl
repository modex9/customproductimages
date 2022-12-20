<div id="product-custom-images" class="row">
    {foreach $customProductImageLinks as $id => $imageLink}
        <div class="col-md-4 custom-image-container">
            <button type="button" class="btn btn-sm btn-link custom-image-delete" data-custom-image-id={$id}><i class="material-icons">delete</i>{l s='Delete' mod='customproductimages'}</button>
            <div class="thumbnail">
                <img class="w-100" src="{$imageLink}">
            </div>
        </div>
   {/foreach}
</div>
<div class="row mt-5">
    <label class="col-lg-2 text-right">
       {l s='Upload new custom image:' mod='customproductimages'}
    </label>
    <div class="col-lg-8">
        <input type="file" id='custom_product_image' name="custom_product_image" class="form-control" />
    </div>
    <div class="col-lg-2">
        <button id='add-custom-image' type="button" class="btn btn-success">
             <i class="icon icon-save"></i>
             {l s='Save' mod='customproductimages'}
         </button>
    </div>
</div>