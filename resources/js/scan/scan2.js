document.addEventListener("DOMContentLoaded", function () {
    const resultContainer = document.querySelector("#qr-reader-results");
    const resultWrapper = document.querySelector("#result-wrapper");
    const messageContainer = document.querySelector("#message");
    const qrReader = document.querySelector("#qr-reader");
    const toggleScanBtn = document.querySelector("#toggle-scan-btn");
    const toggleBtnIcon = document.querySelector("#toggle-scan-btn span");
    const attendeeDetails = document.querySelector("#attendee-details");
    const resultIcon = document.querySelector("#result-icon");
    let isScanning = false;
    let lastScannedCode = null;

    const studentsScanned = new Set(); // To track scanned students

    let videoStream = null;
    let animationFrameId = null;
    let jsQRLoaded = false;
    let videoElement = null;

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Debounced scan success handler
    const debouncedScanSuccess = debounce((decodedText) => {
        const subjectSelect = document.getElementById("subject-select");
        const subjectId = subjectSelect.value;

        if (!subjectId) {
            updateResult("Please select a subject first.", "error");
            return;
        }

        const c = `${decodedText}::${subjectId}`;

        // prevent duplicates should be handled on the server side
        if (lastScannedCode === c) return; // Prevent duplicate scans

        console.log(`Code scanned: ${decodedText}. Subject ID: ${subjectId}`);

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
                    lastScannedCode = c;
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

    function stopScanning() {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        if (videoElement) {
            videoElement.remove();
            videoElement = null;
        }
        isScanning = false;
        toggleBtnIcon.textContent = "videocam";
        document.getElementById("scan-text").textContent = "Start Scanning";
        resultContainer.classList.add("hidden");
        attendeeDetails.innerHTML = "";
        messageContainer.innerHTML = "";
        lastScannedCode = null;
    }

    function scanFrame() {
        if (!videoElement || videoElement.readyState !== videoElement.HAVE_ENOUGH_DATA) {
            animationFrameId = requestAnimationFrame(scanFrame);
            return;
        }
        const canvas = document.createElement("canvas");
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        const context = canvas.getContext("2d");
        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "dontInvert",
        });

        if (code && code.data) {
            debouncedScanSuccess(code.data);
            // Optionally, stop scanning after a successful scan:
            // stopScanning();
        } else {
            animationFrameId = requestAnimationFrame(scanFrame);
        }
    }

    async function startScanning() {
        try {
            toggleBtnIcon.textContent = "videocam_off";
            document.getElementById("scan-text").textContent = "Stop Scanning";

            console.log("Starting QR Code Scanner...");
            
            videoElement = document.createElement("video");
            videoElement.setAttribute("playsinline", true);
            videoElement.style.width = "100%";
            videoElement.style.height = "auto";
            qrReader.innerHTML = "";
            qrReader.appendChild(videoElement);

            videoStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" }
            });
            videoElement.srcObject = videoStream;
            await videoElement.play();

            animationFrameId = requestAnimationFrame(scanFrame);

            isScanning = true;
            
        } catch (err) {
            console.error(`Unable to start scanning: ${err}`);
            updateResult(
                `Error: Unable to access the camera. ${err}`,
                "error"
            );
            toggleBtnIcon.textContent = "videocam";
            document.getElementById("scan-text").textContent = "Start Scanning";
        }
    }

    function loadJsQRAndInit() {
        if (jsQRLoaded) return;
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js";
        script.onload = () => {
            jsQRLoaded = true;
        };
        document.body.appendChild(script);
    }

    toggleScanBtn.addEventListener("click", async () => {
        if (!jsQRLoaded) {
            updateResult("Loading QR scanner library, please wait...", "warning");
            return;
        }
        if (!isScanning) {
            startScanning();
        } else {
            stopScanning();
        }
    });

    // Load jsQR on page load
    loadJsQRAndInit();
});