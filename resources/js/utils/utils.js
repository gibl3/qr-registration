/**
 * Sets up a password toggle button for a password input.
 * @param {HTMLElement} toggleButton - The toggle button element.
 * @param {HTMLElement} passwordInput - The password input element.
 */
export function setupPasswordToggle(toggleButton, passwordInput) {
    if (
        !(toggleButton instanceof HTMLElement) ||
        !(passwordInput instanceof HTMLElement)
    ) {
        throw new Error(
            "setupPasswordToggle expects two HTMLElement arguments."
        );
    }

    toggleButton.addEventListener("click", (e) => {
        e.preventDefault();
        const icon = toggleButton.querySelector(".material-symbols-rounded");
        if (!icon) return;

        const type =
            passwordInput.getAttribute("type") === "password"
                ? "text"
                : "password";
        passwordInput.setAttribute("type", type);
        icon.textContent =
            type === "password" ? "visibility" : "visibility_off";
    });
}
