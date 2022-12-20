$(function () {
    $('#add-custom-image').click(function () {
        const idProduct = $('#form_id_product').val();
        const data = new FormData();

        if ($('#custom_product_image')[0].files[0]) {
            data.append('custom_product_image', $('#custom_product_image')[0].files[0]);
        }
        $.ajax({
            type: 'POST',
            url: replaceEndingIdFromUrl(add_product_custom_image_url, idProduct),
            data,
            contentType: false,
            processData: false,
            beforeSend() {
                $('ul.text-danger').remove();
                $('*.has-danger').removeClass('has-danger');
            },
            success(response) {
                // inject new attachment in attachment list
                // if (response.id) {
                //   /* eslint-disable */
                //   const row = `<tr>\
                //     <td class="col-md-3"><input type="checkbox" name="form[step6][attachments][]" value="${response.id}" checked="checked"> ${response.real_name}</td>\
                //     <td class="col-md-6">${response.file_name}</td>\
                //     <td class="col-md-2">${response.mime}</td>\
                //   </tr>`;
                //   /* eslint-enable */

                //   $('#product-attachment-file tbody').append(row);
                //   $('.js-options-no-attachments').addClass('hide');
                //   $('.js-options-with-attachments').removeClass('hide');
                // }
            },
            error(response) {
                $.each(jQuery.parseJSON(response.responseText), (key, errors) => {
                    let html = '<ul class="list-unstyled text-danger">';
                    $.each(errors, (errorsKey, error) => {
                        html += `<li>${error}</li>`;
                    });
                    html += '</ul>';

                    $(`#form_step6_attachment_product_${key}`).parent().append(html);
                    $(`#form_step6_attachment_product_${key}`).parent().addClass('has-danger');
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
});