document.getElementById("download-btn").addEventListener("click", function () {
    const captureArea = document.getElementById("capture-area"); // The div to capture
    const studentID = document.getElementById("student-id").textContent; // The div to capture

    html2canvas(captureArea, {
        useCORS: true,
        scale: 2,
    }).then((canvas) => {
        // Convert the canvas to a data URL
        const dataURL = canvas.toDataURL("image/png");

        // Create a temporary link element
        const link = document.createElement("a");
        link.href = dataURL;
        link.download = `student-card-${studentID}.png`; // Set the filename
        link.click(); // Trigger the download
    });
});
