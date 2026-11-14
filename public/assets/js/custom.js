/**
 * Sistem Inventaris Paroki Atmodirono
 * Custom JS - Ultra-smooth page transitions, speculative prefetching, top glowing progress bar, micro-interactions
 */

"use strict";

(function () {
    // -------------------------------------------------------------------------
    // 1. Initialize and Manage Top Glowing Progress Bar
    // -------------------------------------------------------------------------
    function getProgressBar() {
        let bar = document.getElementById("app-progress-bar");
        if (!bar) {
            bar = document.createElement("div");
            bar.id = "app-progress-bar";
            document.body.prepend(bar);
        }
        return bar;
    }

    let progressTimer = null;

    function startProgress() {
        const bar = getProgressBar();
        clearTimeout(progressTimer);
        bar.classList.remove("finished");
        bar.classList.add("active");
        bar.style.width = "32%";

        progressTimer = setTimeout(function () {
            if (bar.classList.contains("active") && !bar.classList.contains("finished")) {
                bar.style.width = "78%";
            }
        }, 120);
    }

    function finishProgress() {
        const bar = getProgressBar();
        clearTimeout(progressTimer);
        bar.style.width = "100%";
        bar.classList.add("finished");

        setTimeout(function () {
            bar.classList.remove("active", "finished");
            bar.style.width = "0%";
            document.body.classList.remove("page-is-leaving");
        }, 280);
    }

    // Page ready
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(finishProgress, 50);
    } else {
        document.addEventListener("DOMContentLoaded", function () {
            finishProgress();
        });
    }

    // Handle browser Back & Forward cache (bfcache)
    window.addEventListener("pageshow", function (event) {
        document.body.classList.remove("page-is-leaving");
        finishProgress();
    });

    // -------------------------------------------------------------------------
    // 2. Intelligent Speculative Link Prefetching
    // Prefetches target page HTML on hover (65ms intent) or touchstart,
    // making subsequent clicks load instantaneously from cache.
    // -------------------------------------------------------------------------
    const prefetchedUrls = new Set();
    let prefetchTimeout = null;

    function isEligibleForPrefetch(anchor) {
        if (!anchor) return false;
        const href = anchor.getAttribute("href");
        if (!href || href === "#" || href.startsWith("#") || href.startsWith("javascript:")) return false;
        if (anchor.target === "_blank") return false;
        if (anchor.hasAttribute("download")) return false;
        if (anchor.classList.contains("no-transition") || anchor.classList.contains("delete-button") || anchor.classList.contains("logout")) return false;

        try {
            const url = new URL(anchor.href, window.location.origin);
            if (url.origin !== window.location.origin) return false;
            if (url.pathname === window.location.pathname && url.search === window.location.search) return false;

            const path = url.pathname.toLowerCase();
            if (
                path.includes("/logout") ||
                path.includes("/export") ||
                path.includes("/import") ||
                path.includes("/pdf") ||
                path.includes("/download") ||
                path.endsWith(".pdf") ||
                path.endsWith(".xlsx") ||
                path.endsWith(".xls") ||
                path.endsWith(".csv")
            ) {
                return false;
            }

            return true;
        } catch (e) {
            return false;
        }
    }

    function prefetchUrl(urlStr) {
        if (!urlStr || prefetchedUrls.has(urlStr)) return;

        try {
            prefetchedUrls.add(urlStr);
            const link = document.createElement("link");
            link.rel = "prefetch";
            link.href = urlStr;
            link.as = "document";
            document.head.appendChild(link);
        } catch (e) {
            // Ignore
        }
    }

    // Prefetch on pointer hover with 65ms intent delay
    document.addEventListener("pointerover", function (e) {
        const anchor = e.target.closest("a");
        if (!isEligibleForPrefetch(anchor)) return;

        clearTimeout(prefetchTimeout);
        prefetchTimeout = setTimeout(function () {
            prefetchUrl(anchor.href);
        }, 65);
    }, { passive: true });

    document.addEventListener("pointerout", function () {
        clearTimeout(prefetchTimeout);
    }, { passive: true });

    // Touchstart: user is about to tap on mobile, prefetch immediately
    document.addEventListener("touchstart", function (e) {
        const anchor = e.target.closest("a");
        if (isEligibleForPrefetch(anchor)) {
            prefetchUrl(anchor.href);
        }
    }, { passive: true });

    // -------------------------------------------------------------------------
    // 3. Intercept Navigation Links for Smooth MPA Page Transitions
    // -------------------------------------------------------------------------
    document.addEventListener("click", function (e) {
        const link = e.target.closest("a");
        if (!link) return;

        const href = link.getAttribute("href");

        // Ignore empty, anchor, or javascript links
        if (!href || href === "#" || href.startsWith("#") || href.startsWith("javascript:")) {
            return;
        }

        // Ignore modifier keys or middle-click (open in new tab/window)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) {
            return;
        }

        // Ignore target="_blank"
        if (link.target === "_blank") {
            return;
        }

        // Ignore UI triggers and downloads
        const dataToggle = link.getAttribute("data-toggle");
        if (
            link.hasAttribute("download") ||
            (dataToggle && dataToggle !== "tooltip") ||
            link.hasAttribute("data-target") ||
            link.hasAttribute("data-dismiss") ||
            link.classList.contains("no-transition") ||
            link.classList.contains("delete-button") ||
            link.classList.contains("logout") ||
            link.classList.contains("show-modal")
        ) {
            return;
        }

        // Check origin
        try {
            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) {
                return;
            }

            // Same URL with hash change only
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                return;
            }

            // Instant visual feedback for sidebar items (0ms perceived latency)
            const sidebarItem = link.closest(".sidebar-menu li");
            if (sidebarItem && !sidebarItem.classList.contains("active")) {
                document.querySelectorAll(".sidebar-menu li.active").forEach(function (el) {
                    el.classList.remove("active");
                });
                sidebarItem.classList.add("active");
            }

            // Trigger Smooth Transition!
            document.body.classList.add("page-is-leaving");
            startProgress();
        } catch (err) {
            // Invalid URL, do nothing
        }
    });

    // -------------------------------------------------------------------------
    // 4. Smooth Form Submissions (e.g. Filter search forms)
    // -------------------------------------------------------------------------
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!form) return;

        if (form.target === "_blank" || form.classList.contains("no-transition")) {
            return;
        }

        // Don't trigger for confirmed deletes or logouts handled by SweetAlert
        if (form.id === "logout-form" || form.id === "logout-form-mini" || form.classList.contains("delete-form")) {
            return;
        }

        startProgress();
    });

    // -------------------------------------------------------------------------
    // 5. Enhanced Mobile Sidebar Behavior & Touch Interactions
    // -------------------------------------------------------------------------
    $(function () {
        // Auto-close sidebar on mobile when navigating to another page
        $(document).on("click", ".main-sidebar .sidebar-menu a:not(.has-dropdown)", function () {
            if (window.innerWidth <= 1024 && $("body").hasClass("sidebar-show")) {
                $("body").removeClass("sidebar-show").addClass("sidebar-gone");
            }
        });

        // Fast tap outside on backdrop to close mobile sidebar immediately
        $(document).on("click touchend", function (e) {
            if ($("body").hasClass("sidebar-show")) {
                if (!$(e.target).closest(".main-sidebar, [data-toggle='sidebar']").length) {
                    $("body").removeClass("sidebar-show").addClass("sidebar-gone");
                }
            }
        });

        // Enhanced Tooltip for desktop only (avoid sticky tooltip on mobile)
        if ($.fn.tooltip && window.matchMedia("(hover: hover)").matches) {
            $('[data-toggle="tooltip"]').tooltip({
                boundary: "window",
                container: "body"
            });
        }
    });
})();
