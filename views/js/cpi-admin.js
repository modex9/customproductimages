$(function () {
    $('#add-custom-image').click(function () {
        const idProduct = $('#form_id_product').val();
        const data = new FormData();

        if ($('#custom_product_image')[0].files[0]) {
            data.append('custom_product_image', $('#custom_product_image')[0].files[0]);
        }
        else {
            showErrorMessage("No file selected.");
            return;
        }
        $.ajax({
            type: 'POST',
            url: replaceEndingIdFromUrl(add_product_custom_image_url, idProduct),
            data,
            contentType: false,
            processData: false,
            beforeSend() {
                addOverlay();
            },
            success(response) {
                if(typeof(response.success) !== 'undefined')
                {
                    showSuccessMessage(response.success);
                    appendNewImage(response.imageLink, response.id);
                }
                else if(typeof(response.error) !== 'undefined')
                {
                    showErrorMessage(response.error);
                }
                else
                {
                    showErrorMessage('Unexpected response received.');
                }
            },
            error(response) {
                removeOverlay();
                $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
                    $.each(errors, (errorsKey, error) => {
                        showErrorMessage(error);
                    });
                });
            },
            complete() {
                removeOverlay();
            },
        });
    });

    $('#module_customproductimages').on('click', '.custom-image-delete', function () {
        const imageDeleteButton = $(this);
        const idCustomImage = imageDeleteButton.data('custom-image-id');
        if(!idCustomImage) {
            showErrorMessage("No image ID provided.");
        }
        $.ajax({
            type: 'POST',
            url: replaceEndingIdFromUrl(delete_product_custom_image_url, idCustomImage),
            contentType: false,
            processData: false,
            beforeSend() {
                addOverlay();
            },
            success(response) {
                if(typeof(response.success) !== 'undefined')
                {
                    showSuccessMessage(response.success);
                    removeImageContainer(imageDeleteButton);
                }
                else if(typeof(response.error) !== 'undefined')
                {
                    showErrorMessage(response.error);
                }
                else
                {
                    showErrorMessage('Unexpected response received.');
                }
            },
            complete() {
                removeOverlay();
            },
        });
    });

    function removeImageContainer(imageDeleteButton) {
        imageDeleteButton.closest('.custom-image-container').remove();
    }

    function replaceEndingIdFromUrl(url, newId) {
        return url.replace(/\/\d+(?!.*\/\d+)((?=\?.*))?/, `/${newId}`);
    }

    function appendNewImage(imageLink, id) {
        $('#product-custom-images').append(`
            <div class="col-md-4 custom-image-container">
                <button type="button" class="btn btn-sm btn-link custom-image-delete" data-custom-image-id=${id}><i class="material-icons">delete</i>Delete</button>
                <div class="thumbnail">
                    <img class="w-100" src="${imageLink}">
                </div>
            </div>
        `);
    }

    function addOverlay() {
        removeOverlay();
        $('body').append(`
            <div id="cpi-loading-overlay">
                <div class="lds-ellipsis">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>`
        );
    }
    
    function removeOverlay() {
        $('#cpi-loading-overlay').remove();
    }
});