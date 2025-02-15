(function () {
    const htmlElement = document.documentElement;

    // Default configuration
    const defaultConfig = {
        theme: "dark",
        topbar: { color: "light" },
        menu: { size: "default", color: "light" }
    };

    // Retrieve existing configuration from sessionStorage or use defaults
    const storedConfig = sessionStorage.getItem("__Darkone_CONFIG__");
    const config = storedConfig ? JSON.parse(storedConfig) : { ...defaultConfig };

    // Apply configuration from attributes or defaults
    config.theme = htmlElement.getAttribute("data-bs-theme") || defaultConfig.theme;
    config.topbar.color = htmlElement.getAttribute("data-topbar-color") || defaultConfig.topbar.color;
    config.menu.color = htmlElement.getAttribute("data-sidebar-color") || defaultConfig.menu.color;
    config.menu.size = window.innerWidth <= 1140 ? "hidden" : htmlElement.getAttribute("data-sidebar-size") || defaultConfig.menu.size;

    // Save configuration globally
    window.defaultConfig = { ...defaultConfig };
    window.config = config;

    // Apply configuration to the HTML element
    htmlElement.setAttribute("data-bs-theme", config.theme);
    htmlElement.setAttribute("data-topbar-color", config.topbar.color);
    htmlElement.setAttribute("data-sidebar-color", config.menu.color);
    htmlElement.setAttribute("data-sidebar-size", config.menu.size);
})();
