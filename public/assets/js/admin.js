/* ============================================================
   DDP Panel - JS del panel admin (Tabler)
   Incluye: contadores con semáforo, TinyMCE, subidas AJAX,
   vista previa, toggle destacado, confirmaciones
   ============================================================ */
'use strict';

const DDP = {
    BASE: window.DDP_BASE_URL || '',
};

document.addEventListener('DOMContentLoaded', function () {
    iniciarContadores();
    iniciarSlugAutomatico();
    iniciarConfirmaciones();
    iniciarSubidaImagenes();
    iniciarToggleDestacado();
    iniciarVistaPrevia();
    iniciarTinyMCE();
    iniciarEstadoBoletin();
});

/* ============================================================
   1. Contador de caracteres con semáforo visual
   ============================================================ */
function iniciarContadores() {
    document.querySelectorAll('[data-contador]').forEach(function (input) {
        const max = parseInt(input.dataset.contador, 10);
        const wrap = document.createElement('div');
        wrap.className = 'char-counter';
        input.parentNode.appendChild(wrap);
        const bar = document.createElement('div');
        bar.className = 'char-progress';
        bar.innerHTML = '<span></span>';
        input.parentNode.appendChild(bar);

        function actualizar() {
            const len = input.value.length;
            const pct = Math.min(100, Math.round((len / max) * 100));
            const sobra = len > max;

            wrap.textContent = len + ' / ' + max + (sobra ? ' (excede ' + (len - max) + ') ' : '');
            wrap.className = 'char-counter ' + (sobra ? 'bad' : (pct >= 90 ? 'warn' : 'ok'));
            bar.querySelector('span').style.width = pct + '%';
            bar.querySelector('span').style.background = sobra ? '#d63939' : (pct >= 90 ? '#f59f00' : '#2fb344');
        }
        input.addEventListener('input', actualizar);
        actualizar();
    });

    // Semáforo especial para meta_descripcion (rango recomendado 120-160)
    document.querySelectorAll('[data-meta-desc]').forEach(function (input) {
        const wrap = document.createElement('div');
        wrap.className = 'char-counter';
        input.parentNode.appendChild(wrap);

        function actualizar() {
            const len = input.value.length;
            if (len === 0) {
                wrap.textContent = 'Opcional. Se genera del desarrollo si se deja vacío.';
                wrap.className = 'char-counter ok';
            } else if (len < 120) {
                wrap.textContent = len + ' caracteres. Recomendado: entre 120 y 160 (actualmente insuficiente).';
                wrap.className = 'char-counter warn';
            } else if (len <= 160) {
                wrap.textContent = len + ' caracteres. Rango recomendado.';
                wrap.className = 'char-counter ok';
            } else {
                wrap.textContent = len + ' caracteres. Se excede el límite recomendado de 160.';
                wrap.className = 'char-counter bad';
            }
        }
        input.addEventListener('input', actualizar);
        actualizar();
    });
}

/* ============================================================
   2. Slug automático a partir del título
   ============================================================ */
function iniciarSlugAutomatico() {
    const titulo = document.getElementById('f_titulo');
    const slug = document.getElementById('f_slug');
    if (!titulo || !slug) return;

    titulo.addEventListener('input', function () {
        if (slug.dataset.touched === '1') return;
        const base = titulo.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        slug.value = base;
    });
    slug.addEventListener('input', function () { slug.dataset.touched = '1'; });
}

/* ============================================================
   3. Confirmación en modales de eliminación
   ============================================================ */
function iniciarConfirmaciones() {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const msg = form.dataset.confirm || '¿Está seguro de eliminar este registro? Esta acción no se puede deshacer.';
            if (!window.confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
}

/* ============================================================
   4. Subida de imágenes con vista previa y barra de progreso
   ============================================================ */
function iniciarSubidaImagenes() {
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (fileInput) {
        fileInput.addEventListener('change', function () {
            const previewBox = document.getElementById(fileInput.dataset.preview);
            if (!previewBox || !fileInput.files.length) return;
            previewBox.innerHTML = '';
            for (const f of fileInput.files) {
                const url = URL.createObjectURL(f);
                const img = document.createElement('img');
                img.src = url;
                previewBox.appendChild(img);
            }
        });
    });

    const galleryForm = document.getElementById('formGaleria');
    if (galleryForm) {
        galleryForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const progress = document.getElementById('galeriaProgreso');
            const bar = document.getElementById('galeriaBar');
            if (!progress || !bar) return;
            progress.hidden = false;
            bar.style.width = '0%';
            const request = new XMLHttpRequest();
            request.open('POST', DDP.BASE + '/admin/reportajes/fotos/subir');
            request.setRequestHeader('X-CSRF', csrfToken());
            request.upload.addEventListener('progress', function (uploadEvent) {
                if (uploadEvent.lengthComputable) {
                    bar.style.width = Math.round((uploadEvent.loaded / uploadEvent.total) * 100) + '%';
                }
            });
            request.addEventListener('load', function () {
                let response = {};
                try { response = JSON.parse(request.responseText); } catch (ignore) {}
                if (request.status >= 200 && request.status < 300 && response.ok) {
                    window.location.reload();
                } else {
                    window.alert(response.message || 'No se pudo subir la imagen.');
                }
            });
            request.addEventListener('error', function () { window.alert('Error de conexión al subir la foto.'); });
            request.send(new FormData(galleryForm));
        });
    }
}

/* ============================================================
   5. Toggle de destacado (AJAX)
   ============================================================ */
function iniciarToggleDestacado() {
    document.querySelectorAll('[data-toggle-destacado]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = btn.dataset.toggleDestacado;
            const url = DDP.BASE + '/admin/reportajes/' + id + '/toggle-destacado';
            btn.disabled = true;
            fetch(url, { method: 'POST', headers: { 'X-CSRF': csrfToken() } })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        const badge = btn.closest('tr').querySelector('[data-estado-destacado]');
                        if (data.destacado) {
                            badge.textContent = 'Sí';
                            badge.className = 'badge bg-success';
                            btn.innerHTML = '<i class="ti ti-star-off"></i>';
                        } else {
                            badge.textContent = 'No';
                            badge.className = 'badge bg-secondary';
                            btn.innerHTML = '<i class="ti ti-star"></i>';
                        }
                        if (data.mensaje) {
                            window.alert(data.mensaje);
                        }
                    }
                    btn.disabled = false;
                })
                .catch(() => { btn.disabled = false; window.alert('Error al cambiar el estado de destacado.'); });
        });
    });
}

/* ============================================================
   6. Vista previa antes de publicar (modal AJAX)
   ============================================================ */
function iniciarVistaPrevia() {
    const boton = document.getElementById('btnPreview');
    if (!boton) return;

    boton.addEventListener('click', function (e) {
        e.preventDefault();
        const form = document.getElementById('formReportaje');
        if (!form) return;

        const data = new FormData(form);
        data.set('preview', '1');
        const body = new URLSearchParams();
        for (const [k, v] of data) body.append(k, v);

        fetch(boton.dataset.url, { method: 'POST', headers: { 'X-CSRF': csrfToken() }, body })
            .then(r => r.json())
            .then(res => {
                const modal = document.getElementById('modalPreview');
                if (!modal) return;
                modal.querySelector('.preview-html').innerHTML = res.html || '<p>Sin contenido.</p>';
                const inst = bootstrap.Modal.getOrCreateInstance(modal);
                inst.show();
            })
            .catch(() => window.alert('No se pudo generar la vista previa.'));
    });

    // Checklist de revisión antes de publicar
    const formPub = document.getElementById('formReportaje');
    if (formPub) {
        const estadoSelect = document.getElementById('f_estado');
        if (estadoSelect) {
            estadoSelect.addEventListener('change', function () {
                if (estadoSelect.value === 'publicado') {
                    revisarChecklistPublicacion();
                }
            });
        }
        const revisarBtn = document.getElementById('btnRevisarChecklist');
        if (revisarBtn) revisarBtn.addEventListener('click', revisarChecklistPublicacion);
    }
}

function revisarChecklistPublicacion() {
    const checks = {
        foto: document.getElementById('f_foto_principal') ? true : false,
        meta: document.getElementById('f_meta_descripcion').value.trim().length >= 120,
        autor: document.getElementById('f_autor_id').value !== '',
        slug: document.getElementById('f_slug').value.trim() !== '',
    };
    const resumen = {
        foto: 'Foto principal requerida (con Alt)',
        meta: 'Meta descripción requerida (120-160 caracteres)',
        autor: 'Autor asignado o "Redacción"',
        slug: 'Slug único generado',
    };
    const estado = document.getElementById('checklistResultado');
    if (!estado) return;
    let html = '';
    for (const key in checks) {
        const ok = checks[key];
        html += `<div class="d-flex gap-2 ${ok ? 'check-ok' : 'check-bad'}">
                    <i class="ti ti-${ok ? 'circle-check' : 'square-off'} mt-1"></i>
                    <span>${resumen[key]}</span>
                </div>`;
    }
    estado.innerHTML = `<div class="checklist-visor">${html}</div>`;
}

/* ============================================================
   7. Inicialización de TinyMCE (editor enriquecido)
   ============================================================ */
function iniciarTinyMCE() {
    if (!document.getElementById('f_desarrollo')) return;

    // Cargar TinyMCE bajo demanda (solo si existe el textarea en la página)
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js';
    script.onload = function () {
        setTimeout(function () {
            tinymce.init({
                selector: '#f_desarrollo',
                language: 'es',
                height: 520,
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
                toolbar: 'undo redo | blocks | bold italic underline strikethrough blockquote | bullist numlist | link image media | alignleft aligncenter alignright | forecolor backcolor | code preview fullscreen',
                menubar: 'edit insert view format table tools',
                relative_urls: false,
                branding: false,
                content_css: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                setup: function (editor) {
                    // Sincronizar contenido en el formulario al guardar
                    editor.on('change', function () { editor.save(); });
                },
            });
        }, 100);
    };
    script.onerror = function () { window.console.warn('TinyMCE no se pudo cargar.'); };
    document.head.appendChild(script);
}

/* ============================================================
   8. Estado boletín: ajusta visibilidad de campos PDF
   ============================================================ */
function iniciarEstadoBoletin() {
    const tipoSelect = document.querySelector('[data-tipomedio]');
    if (!tipoSelect) return;

    const archivoField = document.getElementById('archivoField');
    const embedField = document.getElementById('embedField');

    function actualizar() {
        const t = tipoSelect.value;
        if (archivoField) archivoField.style.display = t === 'archivo' ? '' : 'none';
        if (embedField) embedField.style.display = t === 'embed' ? '' : 'none';
    }
    tipoSelect.addEventListener('change', actualizar);
    actualizar();
}

/* ============================================================
   Utilidades
   ============================================================ */
function csrfToken() {
    return document.querySelector('input[name="' + (window.DDP_CSRF_NAME || 'csrf_token') + '"]').value;
}