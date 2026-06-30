/**
 * mybooks マーケティングページ用の最小限の挙動。
 * モバイルナビ（ハンバーガー）の開閉のみ。外部依存なし。
 */
(function () {
    'use strict';

    function init() {
        var toggle = document.querySelector('[data-mb-nav-toggle]');
        var panel = document.querySelector('[data-mb-mobile-nav]');
        if (!toggle || !panel) {
            return;
        }

        function close() {
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'メニューを開く');
            toggle.classList.remove('is-open');
        }

        function open() {
            panel.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'メニューを閉じる');
            toggle.classList.add('is-open');
        }

        toggle.addEventListener('click', function () {
            if (panel.hidden) {
                open();
            } else {
                close();
            }
        });

        // パネル内のリンクをタップしたら閉じる
        panel.addEventListener('click', function (e) {
            if (e.target.closest('a')) {
                close();
            }
        });

        // Esc で閉じる
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) {
                close();
                toggle.focus();
            }
        });

        // デスクトップ幅に戻ったら必ず閉じてリセット
        window.addEventListener('resize', function () {
            if (window.innerWidth > 760 && !panel.hidden) {
                close();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
