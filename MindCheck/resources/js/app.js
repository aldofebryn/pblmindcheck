import "./bootstrap";

// ── CSRF token untuk semua fetch/XHR ────────────────────────────
const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
if (token) {
    window.axios &&
        (window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token);
}

// ── Auto-dismiss flash messages setelah 4 detik ─────────────────
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-flash]").forEach((el) => {
        setTimeout(
            () =>
                el.classList.add(
                    "opacity-0",
                    "transition-opacity",
                    "duration-500",
                ),
            4000,
        );
        setTimeout(() => el.remove(), 4600);
    });
});
