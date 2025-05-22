document.addEventListener("DOMContentLoaded", function () {
    const deleteButton = document.querySelector("#delete-btn");
    const checkboxes = document.querySelectorAll("input[type='checkbox']");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    deleteButton.addEventListener("click", function () {
        // Collect all selected attendance IDs
        const selectedIds = Array.from(checkboxes)
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.dataset.id);

        if (selectedIds.length === 0) {
            alert("Please select at least one attendance record to delete.");
            return;
        }

        // Confirm deletion
        if (!confirm("Are you sure you want to delete the selected records?")) {
            return;
        }

        // Send DELETE request to the server
        fetch("/admin/instructor/delete", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ ids: selectedIds }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);

                    // Remove the deleted rows from the table
                    selectedIds.forEach((id) => {
                        const row = document.querySelector(
                            `tr[data-id="${id}"]`
                        );
                        if (row) {
                            row.remove();
                        }
                    });
                } else {
                    alert("An error occurred while deleting records.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
            });
    });
});
