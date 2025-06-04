document.addEventListener("DOMContentLoaded", function () {
    const studentForm = document.querySelector("#student-form");
    let errorTimeout;

    studentForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(studentForm);

        // Clear previous error messages
        document
            .querySelectorAll(".error-message")
            .forEach((el) => el.remove());

        try {
            const response = await fetch("/student/store", {
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
                handleSuccess(data);
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
            throw new Error("Failed to register student.");
        }
    }

    function handleSuccess(data) {
        // Redirect to the URL provided by the server
        window.location.href = data.redirect;
    }

    function displayValidationErrors(errors) {
        console.log(errors)
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

                    errorTimeout = setTimeout(() => {
                        errorDiv.classList.add("hidden");
                    }, 3000);
                }
            }
        } else {
            // Handle other errors
            alert("An error occurred while registering the student.");
        }
    }
});
