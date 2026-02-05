<!-- Common Head - Include this in all pages -->
<link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<style>
    /* Force Inter font for all elements */
    * {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }
    /* Prevent Google Translate from breaking Material Icons */
    .material-symbols-outlined,
    .material-icons {
        font-family: 'Material Symbols Outlined', 'Material Icons' !important;
    }
</style>
<script>
    // Store original icon names and restore them if Google Translate changes them
    (function() {
        var iconMap = new Map();
        
        function saveIconNames() {
            document.querySelectorAll('.material-symbols-outlined, .material-icons').forEach(function(el) {
                var text = el.textContent.trim();
                // Only save if it looks like a valid icon name (no spaces, underscores allowed)
                if (text && /^[a-z_]+$/.test(text)) {
                    iconMap.set(el, text);
                    el.setAttribute('data-icon', text);
                    el.setAttribute('translate', 'no');
                    el.classList.add('notranslate');
                }
            });
        }
        
        function restoreIconNames() {
            document.querySelectorAll('.material-symbols-outlined[data-icon], .material-icons[data-icon]').forEach(function(el) {
                var originalIcon = el.getAttribute('data-icon');
                if (originalIcon && el.textContent.trim() !== originalIcon) {
                    el.textContent = originalIcon;
                }
            });
        }
        
        // Run on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', saveIconNames);
        } else {
            saveIconNames();
        }
        
        // Also run on window load
        window.addEventListener('load', function() {
            saveIconNames();
            // Check periodically for translation changes
            setTimeout(restoreIconNames, 500);
            setTimeout(restoreIconNames, 1000);
            setTimeout(restoreIconNames, 2000);
        });
        
        // Use MutationObserver to detect when Google Translate modifies the page
        var observer = new MutationObserver(function(mutations) {
            restoreIconNames();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                characterData: true
            });
        });
    })();
</script>
