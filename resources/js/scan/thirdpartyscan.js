function updateResult(message, type = "info") {
    const resultIcon = document.getElementById("result-icon");
    const resultWrapper = document.getElementById("result-wrapper");
    const messageContainer = document.getElementById("message");

    // Define color mappings for each type
    const colorClasses = {
        success: {
            wrapper: "bg-green-50",
            iconColor: "text-green-600",
            message: "text-neutral-700",
            icon: "check_circle",
        },
        warning: {
            wrapper: "bg-yellow-50",
            iconColor: "text-yellow-600",
            message: "text-neutral-700",
            icon: "info",
        },
        error: {
            wrapper: "bg-red-50",
            iconColor: "text-red-600",
            message: "text-neutral-700",
            icon: "warning",
        },
    };

    switch (type) {
        case "success":
            resultIcon.textContent = "check_circle";
            break;
        case "warning":
            resultIcon.textContent = "info";
            break;
        case "error":
            resultIcon.textContent = "warning";
            break;
        default:
            resultIcon.textContent = "info"; // Default to info icon
    }

    // Get the color classes for the given type
    const colors = colorClasses[type] || colorClasses.info;

    // Update the resultWrapper colors
    resultWrapper.className = `rounded-xl p-4 space-y-4 ${colors.wrapper}`;

    // Update the resultIcon colors
    resultIcon.className = `material-symbols-rounded mt-1 ${colors.iconColor}`;

    // Update the messageContainer colors and text
    messageContainer.className = `text-base font-medium ${colors.iconColor}`;
    document.getElementById("qr-reader-results").classList.remove("hidden");
    messageContainer.textContent = message;
}

function confirmScan() {
    const subjectID = document.getElementById("subject-select").value;
    const studentID = document.getElementById("student-id").value;

    if (!subjectID) {
        updateResult("Please select a subject.", "warning");
        return;
    }

    fetch("/instructor/scan/store/other", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            student_id: studentID,
            subject_id: subjectID,
        }),
    }).then(async (response) => {
        const data = await response.json();

        if (response.status === 201) {
            // redirect to attendance page
            window.location.href = `/instructor/attendance`;
            return;
        }

        if (response.status === 409) {
            // Attendance already recorded
            updateResult(data.message, "warning");
            return;
        }

        if (response.status === 404) {
            // No student found
            updateResult(data.message, "error");
            return;
        }

        updateResult(data.message, "error");
        console.error("Error:", data.error);
    }).catch((error) => {
        console.error("Error:", error);
        updateResult(
            "Error recording attendance. Please try again.",
            "error"
        );
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("confirm-scan-btn");
    btn.onclick = confirmScan;
});