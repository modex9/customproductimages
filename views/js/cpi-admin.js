$(function () {
    $('#add-custom-image').click(function () {
        const idProduct = $('#form_id_product').val();
        const data = new FormData();
        const buttonSave = $('#add-custom-image');

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
                buttonSave.attr('disabled', 'disbaled')
            },
            success(response) {
                if(typeof(response.success) !== 'undefined')
                {
                    showSuccessMessage(response.success);
                    appendNewImage(response.imageLink);
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
                $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
                    let html = '<ul class="list-unstyled text-danger">';
                    $.each(errors, (errorsKey, error) => {
                        showErrorMessage(error);
                    });
                });
            },
            complete() {
                buttonSave.removeAttr('disabled');
            },
        });
    });

    function replaceEndingIdFromUrl(url, newId) {
        return url.replace(/\/\d+(?!.*\/\d+)((?=\?.*))?/, `/${newId}`);
    }

    function appendNewImage(imageLink) {
        $('#product-custom-images').append(`
            <div class="col-md-4">
                <div class="thumbnail">
                    <img class="w-100" src="${imageLink}">
                </div>
            </div>
        `);
    }
});