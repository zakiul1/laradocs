/* import "./bootstrap"; */
import Alpine from "alpinejs";

window.Alpine = Alpine;

/**
 * Global Toast Store for Alpine
 * Used for notifications across all pages.
 */
Alpine.store("toast", {
    show: false,
    type: "success", // success | error | info
    message: "",
    trigger(type, message) {
        this.type = type || "info";
        this.message = message || "";
        this.show = true;
        setTimeout(() => (this.show = false), 3000);
    },
});

/**
 * Global helper function for JS scripts.
 * Call this inside XHR handlers: window.safeToast('success', 'Employee created!');
 */
window.safeToast = function (type, message) {
    try {
        const store = Alpine.store("toast");
        if (store && typeof store.trigger === "function") {
            store.trigger(type, message);
            return;
        }
    } catch (e) {}
    console.log(`[toast:${type}]`, message);
};

// Optional: your existing layout store
Alpine.store("layout", {
    sidebarOpen: true,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
});

Alpine.start();
