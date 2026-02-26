(function () {
    if (window.__dropdownTableZIndexFixInitialized) {
        return;
    }
    window.__dropdownTableZIndexFixInitialized = true;

    var TABLE_RESPONSIVE_SELECTOR = [
        ".table-responsive",
        ".table-responsive-sm",
        ".table-responsive-md",
        ".table-responsive-lg",
        ".table-responsive-xl",
        ".table-responsive-xxl"
    ].join(", ");

    var DROPDOWN_TOGGLE_SELECTOR = "[data-bs-toggle=\"dropdown\"]";

    function toElement(node) {
        return node && node.nodeType === 1 ? node : null;
    }

    function markInlineStyle(el, prop, datasetKey, value) {
        if (!el) return;
        if (el.dataset[datasetKey] === undefined) {
            el.dataset[datasetKey] = el.style[prop] || "";
        }
        el.style[prop] = value;
    }

    function restoreInlineStyle(el, prop, datasetKey) {
        if (!el) return;
        if (el.dataset[datasetKey] === undefined) return;
        el.style[prop] = el.dataset[datasetKey];
        delete el.dataset[datasetKey];
    }

    function incCounter(el, key) {
        var current = parseInt(el.dataset[key] || "0", 10);
        current += 1;
        el.dataset[key] = String(current);
        return current;
    }

    function decCounter(el, key) {
        var current = parseInt(el.dataset[key] || "0", 10);
        current = Math.max(0, current - 1);
        if (current === 0) {
            delete el.dataset[key];
        } else {
            el.dataset[key] = String(current);
        }
        return current;
    }

    function findDropdownRoot(source) {
        var el = toElement(source);
        if (!el) return null;
        return el.closest(".dropdown");
    }

    function ensureTableDropdownBoundary(root) {
        var context = root === document ? document : toElement(root);
        if (!context) return;

        var toggles = [];
        if (context === document) {
            toggles = Array.prototype.slice.call(document.querySelectorAll(DROPDOWN_TOGGLE_SELECTOR));
        } else {
            if (context.matches && context.matches(DROPDOWN_TOGGLE_SELECTOR)) {
                toggles.push(context);
            }
            if (context.querySelectorAll) {
                toggles = toggles.concat(Array.prototype.slice.call(context.querySelectorAll(DROPDOWN_TOGGLE_SELECTOR)));
            }
        }

        toggles.forEach(function (toggle) {
            if (!toggle.closest("table, .table")) {
                return;
            }

            if (!toggle.hasAttribute("data-bs-boundary")) {
                toggle.setAttribute("data-bs-boundary", "viewport");
            }

            var responsiveWrapper = toggle.closest(TABLE_RESPONSIVE_SELECTOR);
            if (responsiveWrapper) {
                responsiveWrapper.classList.add("dropdown-fix");
            }
        });
    }

    function activateTableDropdownFix(eventTarget) {
        var dropdown = findDropdownRoot(eventTarget);
        if (!dropdown) return;

        var tr = dropdown.closest("tr");
        if (!tr) {
            return;
        }

        ensureTableDropdownBoundary(dropdown);

        var trCount = incCounter(tr, "dropdownTableFixOpenCount");
        if (trCount === 1) {
            tr.classList.add("dropdown-row-zindex-active");
            markInlineStyle(tr, "position", "dropdownTableFixPrevPosition", "relative");
            markInlineStyle(tr, "zIndex", "dropdownTableFixPrevZindex", "1060");
        }

        var ddCount = incCounter(dropdown, "dropdownTableFixOpenCount");
        if (ddCount === 1) {
            dropdown.classList.add("dropdown-table-active");
            markInlineStyle(dropdown, "position", "dropdownTableFixPrevPosition", "relative");
            markInlineStyle(dropdown, "zIndex", "dropdownTableFixPrevZindex", "1061");
        }
    }

    function deactivateTableDropdownFix(eventTarget) {
        var dropdown = findDropdownRoot(eventTarget);
        if (!dropdown) return;

        var tr = dropdown.closest("tr");
        if (!tr) {
            return;
        }

        var trCount = decCounter(tr, "dropdownTableFixOpenCount");
        if (trCount === 0) {
            tr.classList.remove("dropdown-row-zindex-active");
            restoreInlineStyle(tr, "position", "dropdownTableFixPrevPosition");
            restoreInlineStyle(tr, "zIndex", "dropdownTableFixPrevZindex");
        }

        var ddCount = decCounter(dropdown, "dropdownTableFixOpenCount");
        if (ddCount === 0) {
            dropdown.classList.remove("dropdown-table-active");
            restoreInlineStyle(dropdown, "position", "dropdownTableFixPrevPosition");
            restoreInlineStyle(dropdown, "zIndex", "dropdownTableFixPrevZindex");
        }
    }

    function init() {
        ensureTableDropdownBoundary(document);

        document.addEventListener("show.bs.dropdown", function (event) {
            activateTableDropdownFix(event.target);
        }, true);

        document.addEventListener("hidden.bs.dropdown", function (event) {
            deactivateTableDropdownFix(event.target);
        }, true);

        if (window.MutationObserver && document.body) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        var el = toElement(node);
                        if (!el) return;
                        ensureTableDropdownBoundary(el);
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init, { once: true });
    } else {
        init();
    }
})();
