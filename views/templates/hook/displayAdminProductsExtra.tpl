<div class="row">
    {foreach $productCustomImageLinks as $imageLink}
        <div class="col-md-4">
            <div class="thumbnail">
            <img class="w-100" src="{$imageLink}">
        </div>
     </div>
   {/foreach}
</div>
<div class="row">
    <label class="col-lg-3 text-right">
       {l s='Upload new custom image:' mod='customproductimages'}
    </label>
    <div class="col-lg-9">
        <input type="file" id='custom_product_image' name="custom_product_image" class="form-control" />
        <button id='add-custom-image' type="button" class="btn btn-default">
             <i class="icon icon-save"></i>
             {l s='Save' mod='customproductimages'}
         </button>
    </div>
</div>