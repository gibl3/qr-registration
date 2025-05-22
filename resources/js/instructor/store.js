document.addEventListener("DOMContentLoaded", function () {
    const instructorForm = document.querySelector("#instructor-form");
    const responseBox = document.querySelector("#response-box");

    instructorForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(instructorForm);

        // Clear previous error messages
        document
            .querySelectorAll(".error-message")
            .forEach((el) => el.remove());

        try {
            const response = await fetch("/admin/instructor/store", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: formData,
            });

            if (!response.ok) {
                await handleErrors(response);
            } else {
                const data = await response.json();

                const successMessage = document.createElement("p");
                successMessage.textContent = data.message;
                responseBox.classList.remove("hidden");
                responseBox.appendChild(successMessage);

                instructorForm.reset();

                setTimeout(() => {
                    responseBox.classList.add("hidden");
                }, 3000);
            }
        } catch (errors) {
            displayValidationErrors(errors);
        }
    });

    async function handleErrors(response) {
        if (response.status === 422) {
            const data = await response.json();
            throw data.errors; // Throw validation errors
        } else {
            throw new Error("Failed to add instructor.");
        }
    }

    function displayValidationErrors(errors) {
        if (errors) {
            // Display validation errors
            for (const [field, messages] of Object.entries(errors)) {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement("p");
                    errorDiv.classList.add(
                        "text-red-500",
                        "text-sm",
                        "error-message"
                    );
                    errorDiv.textContent = messages[0]; // Display the first error message
                    input.parentElement.appendChild(errorDiv);

                    setTimeout(() => {
                        errorDiv.classList.add("hidden");
                    }, 3000);
                }
            }
        } else {
            alert("An error occurred while registering the student.");
        }
    }
});
