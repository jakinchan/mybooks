/**
 * GoodWorld Online Books - pdf.js CMap shim
 *
 * Free PDF to Flipbook 同梱の pdf.js は getDocument を URL 文字列だけで呼び出すため、
 * 日本語など CID キー font に必要な CMap / 標準 font データの取得先が設定されません。
 * その結果、表組みなどのレイアウトは描画されても文字が一切表示されません。
 *
 * pdfjsLib.getDocument は getter かつ configurable:false で直接差し替えできないため、
 * グローバルの window.pdfjsLib を Proxy でラップし、getDocument 呼び出し時に
 * cMapUrl / cMapPacked / standardFontDataUrl を注入する。Free PDF to Flipbook 本体は改造しない。
 * pdf.js の後・flipbook-js の前に読み込む必要がある（enqueue 依存で保証）。
 */
(function () {
    if (typeof window === 'undefined') {
        return;
    }

    var cfg = window.GWOB_PDFJS || {};

    function wrap() {
        var lib = window.pdfjsLib;
        if (!lib || lib.__gwobCmapWrapped) {
            return !!(lib && lib.__gwobCmapWrapped);
        }
        if (typeof Proxy === 'undefined') {
            return false;
        }

        try {
            if (cfg.workerSrc && lib.GlobalWorkerOptions && !lib.GlobalWorkerOptions.workerSrc) {
                lib.GlobalWorkerOptions.workerSrc = cfg.workerSrc;
            }

            // 元オブジェクト側に印を付け、二重ラップを防ぐ。
            try {
                Object.defineProperty(lib, '__gwobCmapWrapped', { value: true });
            } catch (ignore) {
                lib.__gwobCmapWrapped = true;
            }

            var proxy = new Proxy(lib, {
                get: function (target, prop) {
                    if (prop === 'getDocument') {
                        return function (src) {
                            var params;
                            if (typeof src === 'string' || (typeof URL !== 'undefined' && src instanceof URL)) {
                                params = { url: src };
                            } else if (src && typeof src === 'object') {
                                params = src;
                            } else {
                                params = {};
                            }

                            if (cfg.cMapUrl && !params.cMapUrl) {
                                params.cMapUrl = cfg.cMapUrl;
                                params.cMapPacked = true;
                            }
                            if (cfg.standardFontDataUrl && !params.standardFontDataUrl) {
                                params.standardFontDataUrl = cfg.standardFontDataUrl;
                            }

                            return target.getDocument(params);
                        };
                    }
                    return target[prop];
                }
            });

            window.pdfjsLib = proxy;
            return true;
        } catch (e) {
            return false;
        }
    }

    // pdf.js は同期的に pdfjsLib を公開するため通常は即時成功するが、
    // 読み込み順が前後した場合に備えて短時間リトライする。
    if (!wrap()) {
        var tries = 0;
        var timer = setInterval(function () {
            tries += 1;
            if (wrap() || tries > 100) {
                clearInterval(timer);
            }
        }, 10);
    }
})();
