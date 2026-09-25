/* DDP: modal único de reproducción y carrusel de Especiales. */
(function (window, document) {
    'use strict';

    function findTrigger(node) {
        while (node && node !== document) {
            if (node.getAttribute && (node.getAttribute('data-ddp-media-trigger') !== null || (node.classList && node.classList.contains('ddp-media-trigger')))) {
                return node;
            }
            node = node.parentNode;
        }
        return null;
    }

    function findCloseTarget(node) {
        while (node && node !== document) {
            if (node.getAttribute && node.getAttribute('data-ddp-repro-close') !== null) {
                return node;
            }
            node = node.parentNode;
        }
        return null;
    }

    function initPlayerModal() {
        var modal = document.querySelector('[data-ddp-repro-modal]');
        if (!modal) {
            return;
        }

        var body = modal.querySelector('[data-ddp-repro-body]');
        var title = modal.querySelector('.ddp-repro-title');
        var endpoint = modal.getAttribute('data-endpoint') || '';
        var lastTrigger = null;
        var requestId = 0;

        function stopMedia() {
            if (!body) {
                return;
            }
            var media = body.querySelectorAll('audio, video');
            Array.prototype.forEach.call(media, function (element) {
                try {
                    element.pause();
                    element.removeAttribute('src');
                    element.load();
                } catch (error) {
                    // El elemento puede haber sido retirado ya; el innerHTML siguiente lo limpia.
                }
            });
            body.innerHTML = '';
        }

        function closeModal() {
            requestId++;
            stopMedia();
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('ddp-modal-open');
            if (lastTrigger && document.documentElement.contains(lastTrigger)) {
                lastTrigger.focus();
            }
            lastTrigger = null;
        }

        function showError(message) {
            if (!body) {
                return;
            }
            body.innerHTML = '<p class="ddp-repro-error"></p>';
            body.firstChild.textContent = message;
        }

        function openModal(trigger) {
            var tipo = trigger.getAttribute('data-tipo') || '';
            var id = trigger.getAttribute('data-id') || '';
            var itemTitle = trigger.getAttribute('data-title') || 'Reproduciendo';
            var currentRequest = ++requestId;

            if (!endpoint || !tipo || !id) {
                return;
            }

            lastTrigger = trigger;
            if (title) {
                title.textContent = itemTitle;
            }
            if (body) {
                body.innerHTML = '<p class="ddp-repro-loading">Cargando reproductor…</p>';
            }
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('ddp-modal-open');
            window.setTimeout(function () {
                var close = modal.querySelector('.ddp-repro-close');
                if (close) {
                    close.focus();
                }
            }, 0);

            var separator = endpoint.indexOf('?') === -1 ? '?' : '&';
            var url = endpoint + separator + 'tipo=' + encodeURIComponent(tipo) + '&id=' + encodeURIComponent(id);

            if (!window.fetch) {
                showError('Este navegador no puede cargar el reproductor. Abre la página del contenido.');
                return;
            }

            window.fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            }).then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            }).then(function (result) {
                if (currentRequest !== requestId) {
                    return;
                }
                if (!result.ok || !result.data || result.data.ok !== true) {
                    throw new Error((result.data && result.data.message) || 'No se pudo cargar el reproductor.');
                }
                if (title && result.data.titulo) {
                    title.textContent = result.data.titulo;
                }
                if (body) {
                    body.innerHTML = result.data.html || '<p class="ddp-repro-error">No hay contenido para reproducir.</p>';
                }
            }).catch(function (error) {
                if (currentRequest !== requestId) {
                    return;
                }
                showError(error && error.message ? error.message : 'No se pudo cargar el reproductor.');
            });
        }

        document.addEventListener('click', function (event) {
            var trigger = findTrigger(event.target);
            if (trigger) {
                event.preventDefault();
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                    return;
                }
                openModal(trigger);
                return;
            }
            if (findCloseTarget(event.target)) {
                event.preventDefault();
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                event.preventDefault();
                closeModal();
            }
        });
    }

    function debounce(callback, wait) {
        var timer = null;
        return function () {
            window.clearTimeout(timer);
            timer = window.setTimeout(callback, wait);
        };
    }

    function initSpecialCarousel(root) {
        var track = root.querySelector('[data-ddp-carousel-track]');
        var previous = root.querySelector('[data-ddp-carousel-prev]');
        var next = root.querySelector('[data-ddp-carousel-next]');
        var dots = root.querySelector('[data-ddp-carousel-dots]');
        if (!track || !previous || !next || !dots) {
            return;
        }

        var items = Array.prototype.slice.call(track.children);
        if (!items.length) {
            return;
        }

        var page = 0;
        var perView = 4;
        var gap = 14;
        var touchStartX = null;

        function visibleItems() {
            var width = window.innerWidth || root.clientWidth || 1200;
            if (width >= 992) {
                return 4;
            }
            if (width >= 768) {
                return 3;
            }
            if (width >= 576) {
                return 2;
            }
            return 1;
        }

        function pageCount() {
            return Math.max(1, Math.ceil(items.length / perView));
        }

        function setItemWidths() {
            var computed = window.getComputedStyle(track);
            gap = parseFloat(computed.gap) || 18;
            var basis = (100 / perView) + '% - ' + (((perView - 1) * gap) / perView) + 'px';
            items.forEach(function (item) {
                item.style.flex = '0 0 calc(' + basis + ')';
            });
        }

        function renderDots() {
            dots.innerHTML = '';
            for (var index = 0; index < pageCount(); index++) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'ddp-carousel-dot' + (index === page ? ' is-active' : '');
                dot.setAttribute('data-ddp-carousel-page', String(index));
                dot.setAttribute('aria-label', 'Ir a la página ' + String(index + 1));
                if (index === page) {
                    dot.setAttribute('aria-current', 'true');
                }
                dots.appendChild(dot);
            }
        }

        function update() {
            var first = items[0].getBoundingClientRect();
            var offset = page * perView * (first.width + gap);
            track.style.transform = 'translate3d(' + (-offset) + 'px, 0, 0)';
            previous.disabled = page <= 0;
            next.disabled = page >= pageCount() - 1;
            var active = dots.querySelector('.ddp-carousel-dot.is-active');
            if (active) {
                active.setAttribute('aria-current', 'true');
            }
        }

        function layout() {
            perView = visibleItems();
            page = Math.min(page, pageCount() - 1);
            setItemWidths();
            renderDots();
            update();
        }

        previous.addEventListener('click', function () {
            page = Math.max(0, page - 1);
            renderDots();
            update();
        });
        next.addEventListener('click', function () {
            page = Math.min(pageCount() - 1, page + 1);
            renderDots();
            update();
        });
        dots.addEventListener('click', function (event) {
            var dot = event.target.closest('[data-ddp-carousel-page]');
            if (!dot) {
                return;
            }
            page = Math.max(0, Math.min(pageCount() - 1, parseInt(dot.getAttribute('data-ddp-carousel-page'), 10) || 0));
            renderDots();
            update();
        });
        root.addEventListener('touchstart', function (event) {
            touchStartX = event.changedTouches[0].clientX;
        }, { passive: true });
        root.addEventListener('touchend', function (event) {
            if (touchStartX === null) {
                return;
            }
            var delta = event.changedTouches[0].clientX - touchStartX;
            touchStartX = null;
            if (Math.abs(delta) < 40) {
                return;
            }
            page = delta < 0
                ? Math.min(pageCount() - 1, page + 1)
                : Math.max(0, page - 1);
            renderDots();
            update();
        }, { passive: true });
        window.addEventListener('resize', debounce(layout, 120));
        layout();
    }

    function init() {
        initPlayerModal();
        Array.prototype.forEach.call(document.querySelectorAll('[data-ddp-special-carousel], .ddp-special-carousel'), initSpecialCarousel);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}(window, document));
