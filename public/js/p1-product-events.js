(function () {
    'use strict';

    window.dataLayer = window.dataLayer || [];

    const PATH = window.location.pathname;

    const TOOL_MAP = {
        '/araclar/seramik-maliyet-hesaplayici': 'seramik_v4',
        '/araclar/beton-hesaplayici': 'beton_hesaplayici',
        '/araclar/boya-hesaplayici': 'boya_hesaplayici'
    };

    const BODY_MAP = {
        'tile-calculator': 'seramik_v4',
        'seramik-maliyet-hesaplayici': 'seramik_v4',
        'beton-calculator': 'beton_hesaplayici',
        'beton-hesaplayici': 'beton_hesaplayici',
        'paint-calculator': 'boya_hesaplayici',
        'boya-hesaplayici': 'boya_hesaplayici'
    };

    function deviceType() {
        const w = window.innerWidth || screen.width;
        if (w <= 767) return 'mobile';
        if (w <= 1024) return 'tablet';
        return 'desktop';
    }

    function currentTool() {
        const bodySlug = document.body?.dataset?.toolSlug || '';
        return TOOL_MAP[PATH] || BODY_MAP[bodySlug] || null;
    }

    function backendSlug() {
        return document.body?.dataset?.toolSlug ||
            Object.keys(TOOL_MAP).find(p => p === PATH)?.split('/').pop() ||
            'unknown';
    }

    function entryInfo() {
        const key = 'ws_p1_entry:' + PATH;

        try {
            const saved = JSON.parse(sessionStorage.getItem(key) || 'null');

            if (
                saved &&
                saved.source_page &&
                Date.now() - saved.at < 30 * 60 * 1000
            ) {
                return {
                    source_page: saved.source_page,
                    entry_source: 'internal'
                };
            }
        } catch (_) {}

        if (!document.referrer) {
            return {
                source_page: PATH,
                entry_source: 'direct'
            };
        }

        try {
            const ref = new URL(document.referrer);

            if (ref.origin === location.origin) {
                return {
                    source_page: ref.pathname,
                    entry_source: 'internal'
                };
            }

            return {
                source_page: PATH,
                entry_source: 'external'
            };
        } catch (_) {
            return {
                source_page: PATH,
                entry_source: 'direct'
            };
        }
    }

    function push(eventName, payload = {}) {
        const tool = payload.tool || currentTool() || 'unknown';

        const eventPayload = {
            event: eventName,
            tool: tool,
            device_type: deviceType(),
            ...payload
        };

        /* GTM/DataLayer contract */
        window.dataLayer.push(eventPayload);

        /*
         * Mevcut WORKSITEM kurulumu doğrudan gtag.js kullanıyor.
         * GTM kurulmadan da özel eventlerin GA4'e ulaşmasını sağlar.
         */
        if (typeof window.gtag === 'function') {
            const gaPayload = { ...eventPayload };
            delete gaPayload.event;

            window.gtag(
                'event',
                eventName,
                gaPayload
            );
        }
    }

    function uuid() {
        if (window.crypto?.randomUUID) {
            return window.crypto.randomUUID();
        }

        return 'client_' + Date.now() + '_' +
            Math.random().toString(36).slice(2, 12);
    }

    function saveCurrentCalculation(data) {
        try {
            sessionStorage.setItem(
                'ws_p1_current_calculation',
                JSON.stringify(data)
            );
        } catch (_) {}
    }

    function currentCalculation() {
        try {
            return JSON.parse(
                sessionStorage.getItem(
                    'ws_p1_current_calculation'
                ) || 'null'
            );
        } catch (_) {
            return null;
        }
    }

    function backendStart(metadata) {
        if (window.WorksitemEvents) {
            window.WorksitemEvents.trackCalculatorStart(
                backendSlug(),
                metadata
            );
        }
    }

    function backendComplete(metadata) {
        if (window.WorksitemEvents) {
            window.WorksitemEvents.trackCalculatorComplete(
                backendSlug(),
                metadata
            );
        }
    }

    /* P1.1 INTERNAL LINK */
    document.addEventListener('click', function (e) {
        const link = e.target.closest('[data-ws-internal-link]');
        if (!link) return;

        let targetPath = link.getAttribute('href') || '';

        try {
            targetPath = new URL(
                targetPath,
                location.origin
            ).pathname;
        } catch (_) {}

        try {
            sessionStorage.setItem(
                'ws_p1_entry:' + targetPath,
                JSON.stringify({
                    source_page: location.pathname,
                    at: Date.now()
                })
            );
        } catch (_) {}

        push('internal_link_click', {
            tool:
                link.dataset.wsTargetTool ||
                TOOL_MAP[targetPath] ||
                'unknown',
            source_page: location.pathname,
            target_url: targetPath,
            anchor_text:
                (link.textContent || '').trim().slice(0, 180)
        });
    });

    /* SERAMIK: gerçek dropdown açılışı + delta seçimi */
    if (currentTool() === 'seramik_v4') {
        const select = document.getElementById('tileSize');

        if (select) {
            let previous = select.value;

            select.addEventListener('pointerdown', function () {
                const entry = entryInfo();

                push('tile_size_open', {
                    ...entry
                });
            });

            select.addEventListener('keydown', function (e) {
                if (
                    ['Enter', ' ', 'ArrowDown', 'ArrowUp']
                        .includes(e.key)
                ) {
                    const entry = entryInfo();

                    push('tile_size_open', {
                        ...entry
                    });
                }
            });

            select.addEventListener('change', function () {
                if (select.value === previous) return;

                previous = select.value;

                const entry = entryInfo();

                push('tile_size_select', {
                    ...entry,
                    tile_size:
                        select.value === 'custom'
                            ? 'custom'
                            : select.value + ' cm'
                });
            });
        }

        /* Server-verified hesap endpointini izle */
        if (!window.__wsP1FetchWrapped) {
            window.__wsP1FetchWrapped = true;

            const nativeFetch = window.fetch;

            window.fetch = async function (input, init) {
                const url =
                    typeof input === 'string'
                        ? input
                        : (input && input.url) || '';

                const isTileCalc =
                    url.includes('/hesaplama/seramik-maliyeti');

                let requestData = {};

                if (isTileCalc && init?.body) {
                    try {
                        requestData =
                            JSON.parse(init.body);
                    } catch (_) {}

                    const entry = entryInfo();

                    push('calculation_started', {
                        ...entry,
                        mode: requestData.mode || null,
                        tile_size:
                            requestData.tile_size
                                ? requestData.tile_size + ' cm'
                                : null
                    });

                    backendStart({
                        mode: requestData.mode || null,
                        tile_size:
                            requestData.tile_size || null
                    });
                }

                const response =
                    await nativeFetch.apply(this, arguments);

                if (isTileCalc && response.ok) {
                    try {
                        const data =
                            await response.clone().json();

                        if (
                            data &&
                            data.calculation_id &&
                            data.estimate
                        ) {
                            const estimate = data.estimate;
                            const entry = entryInfo();

                            saveCurrentCalculation({
                                tool: 'seramik_v4',
                                calculation_id:
                                    data.calculation_id,
                                source_page:
                                    entry.source_page,
                                at: Date.now()
                            });

                            push(
                                'calculation_completed',
                                {
                                    ...entry,
                                    calculation_id:
                                        data.calculation_id,
                                    result_status:
                                        'server_verified',
                                    mode:
                                        estimate.mode ||
                                        requestData.mode ||
                                        null,
                                    tile_size:
                                        estimate.tile_size
                                            ? estimate.tile_size +
                                              ' cm'
                                            : null,
                                    waste_rate_percent:
                                        estimate.waste_rate ??
                                        null
                                }
                            );

                            backendComplete({
                                calculation_id:
                                    data.calculation_id,
                                result_status:
                                    'server_verified'
                            });
                        }
                    } catch (_) {}
                }

                return response;
            };
        }
    }

    /* BETON + BOYA: UI sonucu gerçekten oluşursa complete */
    const clientTools = {
        beton_hesaplayici: {
            button: '#calcBtn',
            result: '#resBox',
            value: '#resPrice'
        },
        boya_hesaplayici: {
            button: '#calculateBtn',
            result: '#resultBox',
            value: '#totalPrice'
        }
    };

    const tool = currentTool();
    const cfg = clientTools[tool];

    if (cfg) {
        const button = document.querySelector(cfg.button);

        if (button) {
            button.addEventListener('click', function () {
                const calculationId = uuid();
                const entry = entryInfo();

                push('calculation_started', {
                    ...entry,
                    calculation_id: calculationId
                });

                backendStart({
                    calculation_id: calculationId
                });

                setTimeout(function () {
                    const result =
                        document.querySelector(cfg.result);

                    const value =
                        document.querySelector(cfg.value);

                    if (!result || !value) return;

                    const visible =
                        getComputedStyle(result).display !==
                        'none';

                    const text =
                        (value.textContent || '').trim();

                    if (!visible || !text) return;

                    saveCurrentCalculation({
                        tool: tool,
                        calculation_id:
                            calculationId,
                        source_page:
                            entry.source_page,
                        at: Date.now()
                    });

                    push('calculation_completed', {
                        ...entry,
                        calculation_id:
                            calculationId,
                        result_status:
                            'client_success'
                    });

                    backendComplete({
                        calculation_id:
                            calculationId,
                        result_status:
                            'client_success'
                    });
                }, 0);
            });
        }
    }

    /* Lead: submit anında sadece pending kaydet.
       Conversion yalnız başarı sayfasında yazılır. */
    document.addEventListener(
        'submit',
        function (e) {
            const form = e.target;

            if (!(form instanceof HTMLFormElement)) return;

            const tool = currentTool();

            if (
                ![
                    'seramik_v4',
                    'beton_hesaplayici',
                    'boya_hesaplayici'
                ].includes(tool)
            ) {
                return;
            }

            const current =
                currentCalculation() || {};

            const calculationInput =
                form.querySelector(
                    '[name="calculation_id"]'
                );

            const timeline =
                form.querySelector(
                    '[name="timeline"]'
                );

            const entry = entryInfo();

            try {
                sessionStorage.setItem(
                    'ws_p1_pending_lead',
                    JSON.stringify({
                        tool: tool,
                        calculation_id:
                            calculationInput?.value ||
                            current.calculation_id ||
                            null,
                        project_timeline:
                            timeline?.value || null,
                        source_page:
                            entry.source_page,
                        at: Date.now()
                    })
                );
            } catch (_) {}
        },
        true
    );

    /* Backend success redirect sonrası gerçek lead dönüşümü */
    if (
        PATH === '/maliyet-hesapla/basarili'
    ) {
        try {
            const raw =
                sessionStorage.getItem(
                    'ws_p1_pending_lead'
                );

            const pending =
                raw ? JSON.parse(raw) : null;

            if (
                pending &&
                Date.now() - pending.at <
                    45 * 60 * 1000
            ) {
                push('lead_submitted', {
                    tool: pending.tool,
                    source_page:
                        pending.source_page,
                    calculation_id:
                        pending.calculation_id,
                    project_timeline:
                        pending.project_timeline
                });
            }

            sessionStorage.removeItem(
                'ws_p1_pending_lead'
            );
        } catch (_) {}
    }

    window.WorksitemP1 = {
        push: push,
        deviceType: deviceType,
        entryInfo: entryInfo
    };
})();
