document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.querySelector("#login-form");
    const errorsDiv = document.querySelector("#errors-div");

    let errorTimeout = null;

    loginForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(loginForm);

        try {
            const response = await fetch("/login/auth", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    // Handle validation errors
                    displayValidationErrors(data.message);
                } else {
                    // Handle other errors
                    displayValidationErrors(data.message);
                }
            } else {
                handleSuccess(data);
            }
        } catch (error) {
            displayValidationErrors("An error occurred. Please try again.");
        }
    });

    function handleSuccess(data) {
        window.location.href = data.redirect;
    }

    function displayValidationErrors(errors) {
        // Clear previous errors
        errorsDiv.innerHTML = "";
        errorsDiv.classList.remove("hidden");

        if (typeof errors === "object") {
            // Handle validation errors
            Object.values(errors).forEach((messages) => {
                const error = document.createElement("p");
                error.classList.add("text-red-500", "text-sm", "error-message");
                error.textContent = messages[0]; // Display the first error message
                errorsDiv.appendChild(error);
            });
        } else {
            // Handle single error message
            const error = document.createElement("p");
            error.classList.add("text-red-500", "text-sm", "error-message");
            error.textContent = errors;
            errorsDiv.appendChild(error);
        }

        // Clear any previous timeout
        if (errorTimeout) clearTimeout(errorTimeout);

        // Hide after 3 seconds
        errorTimeout = setTimeout(() => {
            errorsDiv.classList.add("hidden");
        }, 3000);
    }
});
