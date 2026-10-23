/**
 * Sistem Inventaris Paroki Atmodirono
 * Custom JS - Smooth page transitions, top glowing progress bar, micro-interactions
 */

"use strict";

(function () {
    // 1. Initialize and Manage Top Glowing Progress Bar
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
        bar.style.width = "28%";

        progressTimer = setTimeout(function () {
            if (bar.classList.contains("active") && !bar.classList.contains("finished")) {
                bar.style.width = "75%";
            }
        }, 150);
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
        }, 320);
    }

    // Page ready
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(finishProgress, 60);
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

    // 2. Intercept Navigation Links for Smooth MPA Page Transitions
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

            // Trigger Smooth Transition!
            document.body.classList.add("page-is-leaving");
            startProgress();
        } catch (err) {
            // Invalid URL, do nothing
        }
    });

    // 3. Smooth Form Submissions (e.g. Filter search forms)
    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!form) return;

        if (form.target === "_blank" || form.classList.contains("no-transition")) {
            return;
        }

        // Don't trigger for confirmed deletes or logouts handled by SweetAlert
        if (form.id === "logout-form" || form.classList.contains("delete-form")) {
            return;
        }

        startProgress();
    });

    // 4. Enhanced Tooltip and Hover feedback
    $(function () {
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip({
                boundary: "window",
                container: "body"
            });
        }
    });
})();
