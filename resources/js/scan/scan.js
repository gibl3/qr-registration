document.addEventListener("DOMContentLoaded", function () {
    const resultContainer = document.querySelector("#qr-reader-results");
    const resultWrapper = document.querySelector("#result-wrapper");
    const messageContainer = document.querySelector("#message");
    const qrReader = document.querySelector("#qr-reader");
    const toggleScanBtn = document.querySelector("#toggle-scan-btn");
    const toggleBtnIcon = document.querySelector("#toggle-scan-btn span");
    const attendeeDetails = document.querySelector("#attendee-details");
    const resultIcon = document.querySelector("#result-icon");
    let html5QrCode;
    let isScanning = false;
    let debounceTimeout = null;
    let lastScannedCode = null;

    // Debounce function
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(debounceTimeout);
                func(...args);
            };
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(later, wait);
        };
    }

    // Debounced scan success handler
    const debouncedScanSuccess = debounce((decodedText) => {
        if (lastScannedCode === decodedText) return; // Prevent duplicate scans
        lastScannedCode = decodedText;

        const subjectSelect = document.getElementById("subject-select");
        const subjectId = subjectSelect.value;

        if (!subjectId) {
            updateResult("Please select a subject first.", "error");
            return;
        }

        console.log(`Code scanned: ${decodedText}`);

        fetch("/instructor/scan/store", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                student_id: decodedText,
                subject_id: subjectId,
            }),
        })
            .then(async (response) => {
                resultContainer.classList.remove("hidden");
                const data = await response.json();

                if (response.status === 201) {
                    // Attendance recorded successfully
                    updateResult(data.message, "success");
                    const attendance = data.attendance;
                    const detailsList = `
                    <ul class="list-disc pl-5">
                        <li><strong>First Name:</strong> ${attendance.first_name}</li>
                        <li><strong>Last Name:</strong> ${attendance.last_name}</li>
                        <li><strong>Date:</strong> ${attendance.date}</li>
                        <li><strong>Time In:</strong> ${attendance.time_in}</li>
                        <li><strong>Status:</strong> ${attendance.status}</li>
                    </ul>
                `;
                    attendeeDetails.innerHTML = detailsList;
                } else if (response.status === 409) {
                    // Attendance already recorded
                    updateResult(data.message, "warning");
                    const student = data.student;
                    const detailsList = `
                    <ul class="list-disc pl-5 text-neutral-700">
                        <li><strong>First Name:</strong> ${student.first_name}</li>
                        <li><strong>Last Name:</strong> ${student.last_name}</li>
                        <li><strong>Program:</strong> ${student.program}</li>
                        <li><strong>Year Level:</strong> ${student.year_level}</li>
                    </ul>
                `;
                    attendeeDetails.innerHTML = detailsList;
                } else if (response.status === 404) {
                    // No student found
                    updateResult(data.message, "error");
                    attendeeDetails.innerHTML = `
                    <p class="text-red-500">Student not registered.</p>
                `;
                } else {
                    // Other errors
                    updateResult(data.message, "error");
                    console.error("Error:", data.error);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                updateResult(
                    "Error recording attendance. Please try again.",
                    "error"
                );
            });
    }, 1000); // 1 second debounce

    function onScanSuccess(decodedText, decodedResult) {
        debouncedScanSuccess(decodedText);
    }

    function onScanError(errorMessage) {
        console.warn(`QR Code scan error: ${errorMessage}`);
    }

    toggleScanBtn.addEventListener("click", () => {
        if (!isScanning) {
            // Start the QR code scanner
            toggleBtnIcon.textContent = "videocam_off";
            document.getElementById("scan-text").textContent = "Stop Scanning";

            html5QrCode = new Html5Qrcode("qr-reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 320,
                    height: 192,
                },
            };

            console.log("Starting QR Code Scanner...");
            html5QrCode
                .start(
                    {
                        facingMode: "environment",
                    },
                    config,
                    onScanSuccess,
                    onScanError
                )
                .catch((err) => {
                    console.error(`Unable to start scanning: ${err}`);
                    updateResult(
                        `Error: Unable to access the camera. ${err}`,
                        "error"
                    );
                    toggleBtnIcon.textContent = "videocam";
                    document.getElementById("scan-text").textContent =
                        "Start Scanning";
                });

            isScanning = true;
        } else {
            // Stop the QR code scanner
            toggleBtnIcon.textContent = "videocam";
            document.getElementById("scan-text").textContent = "Start Scanning";
            resultContainer.classList.add("hidden");

            attendeeDetails.innerHTML = "";
            messageContainer.innerHTML = "";
            lastScannedCode = null; // Reset last scanned code

            if (html5QrCode) {
                html5QrCode
                    .stop()
                    .then(() => {
                        console.log("QR Code Scanner stopped.");
                        // Dynamically add the qr_code_2 icon back
                        const qrScanIcon = document.createElement("span");
                        qrScanIcon.classList.add(
                            "material-symbols-rounded",
                            "qr-scan-icon",
                            "text-neutral-400",
                            "m-auto"
                        );
                        qrScanIcon.textContent = "qr_code_2";
                        qrReader.appendChild(qrScanIcon);
                    })
                    .catch((err) => {
                        console.error(`Unable to stop scanning: ${err}`);
                    });
            }

            isScanning = false;
        }
    });

    function updateResult(message, type = "info") {
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

        // Get the color classes for the given type
        const colors = colorClasses[type] || colorClasses.info;

        // Update the resultWrapper colors
        resultWrapper.className = `rounded-xl p-4 space-y-4 ${colors.wrapper}`;

        // Update the resultIcon colors
        resultIcon.className = `material-symbols-rounded mt-1 ${colors.iconColor}`;

        // Update the messageContainer colors and text
        messageContainer.className = `text-base font-medium ${colors.iconColor}`;
        messageContainer.textContent = message;
    }
});
