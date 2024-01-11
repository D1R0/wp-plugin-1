jQuery(document).ready(function () {
    let nonce = wpApiSettings.nonce;
    jQuery(".deleteProject").on("click", function () {
        let id = jQuery(this).data("id");
        let url = wpApiSettings.root + 'ch4/v1/delete-project/' + id;
        jQuery.ajax({
            url: url,
            method: 'DELETE',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', nonce);
            },
        })
            .done(function (data) {
                location.reload();
            })
            .fail(function (jqXHR, textStatus, error) {
                console.error('Error:', textStatus, error);
            });
    })
    if (

        jQuery(".form-upload-ETD").length
    ) {

        const dropContainer = document.getElementById("dropcontainer")
        const fileInput = document.getElementById("ch4Files")

        dropContainer.addEventListener("dragover", (e) => {
            e.preventDefault()
        }, false)

        dropContainer.addEventListener("dragenter", () => {
            dropContainer.classList.add("drag-active")
        })

        dropContainer.addEventListener("dragleave", () => {
            dropContainer.classList.remove("drag-active")
        })

        dropContainer.addEventListener("drop", (e) => {
            e.preventDefault()
            dropContainer.classList.remove("drag-active")
            fileInput.files = e.dataTransfer.files
        })
    }

})
