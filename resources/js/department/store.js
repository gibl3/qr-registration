document.addEventListener("DOMContentLoaded", function() {
    const storeForm = document.querySelector("#store-department-form");

    // storeForm.addEventListener("submit", async function(e) {
    //     e.preventDefault();

    //     const formData = new FormData(storeForm);

    //     // Clear previous error messages
    //     document.querySelectorAll(".error-message").forEach(el => el.remove());
    //     alert('/admin/department/store');
    //     try {
    //         const response = await fetch("/admin/department/store", {
    //             method: "POST",
    //             headers: {
    //                 "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
    //             },
    //             body: formData,
    //         });

    //         if (!response.ok) {
    //             await handleErrors(response);
    //         } else {
    //             const data = await response.json();

    //             const successMessage = document.createElement("p");
    //             successMessage.textContent = data.message;
    //             document.querySelector("#response-box").classList.remove("hidden");
    //             document.querySelector("#response-box").appendChild(successMessage);

    //             storeForm.reset();

    //             setTimeout(() => {
    //                 document.querySelector("#response-box").classList.add("hidden");
    //             }, 3000);
    //         }
    //     } catch (errors) {
    //         displayValidationErrors(errors);
    //     }
    // });
})