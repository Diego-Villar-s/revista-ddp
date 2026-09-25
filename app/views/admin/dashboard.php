<!-- =============================================================
     Dashboard del panel admin (Tabler)
     ============================================================= -->
<div class="row row-deck row-cards">

    <!-- Tarjetas de acceso rápido -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <button class="btn btn-primary btn-icon" type="button" title="Nuevo reportaje">
                        <a href="<?= BASE_URL ?>/admin/reportajes/nuevo" class="text-white"><i class="ti ti-file-plus"></i></a>
                    </button>
                </div>
                <div>
                    <div class="text-secondary">Nuevo reportaje</div>
                    <div class="text-truncate fw-bold">Redactar</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <a href="<?= BASE_URL ?>/admin/noticias/nuevo" class="btn btn-teal btn-icon text-white" title="Nueva noticia"><i class="ti ti-bell-plus"></i></a>
                </div>
                <div>
                    <div class="text-secondary">Nueva noticia</div>
                    <div class="text-truncate fw-bold">Noticia corta</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <a href="<?= BASE_URL ?>/admin/podcasts/nuevo" class="btn btn-red btn-icon text-white" title="Nuevo podcast"><i class="ti ti-microphone"></i></a>
                </div>
                <div>
                    <div class="text-secondary">Nuevo podcast</div>
                    <div class="text-truncate fw-bold">Grabar episodio</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <a href="<?= BASE_URL ?>/admin/videos/nuevo" class="btn btn-cyan btn-icon text-white" title="Nuevo video"><i class="ti ti-video"></i></a>
                </div>
                <div>
                    <div class="text-secondary">Nuevo video</div>
                    <div class="text-truncate fw-bold">Subir material</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteos por estado -->
    <div class="col-12">
        <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total reportajes</div>
                            <div class="ms-auto lh-1"><i class="ti ti-news text-primary" style="font-size:1.6rem"></i></div>
                        </div>
                        <a href="<?= BASE_URL ?>/admin/reportajes" class="h1 mb-1"><?= (int) $conteos['reportajes'] ?></a>
                        <div class="d-flex align-items-center gap-2 mt-2 text-secondary small">
                            <span class="badge bg-success"><?= (int) $conteos['publicados'] ?> publicados</span>
                            <?php if ((int) $conteos['borradores']): ?><span class="badge bg-warning"><?= (int) $conteos['borradores'] ?> borradores</span><?php endif; ?>
                            <?php if ((int) $conteos['archivados']): ?><span class="badge bg-secondary"><?= (int) $conteos['archivados'] ?> archivados</span><?php endif; ?>
                        </div>
                        <?php if ((int) $conteos['destacados']): ?>
                        <div class="mt-2 small text-secondary"><i class="ti ti-star text-yellow me-1"></i><?= (int) $conteos['destacados'] ?> destacados</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Noticias</div>
                            <div class="ms-auto lh-1"><i class="ti ti-bell-ringing text-teal" style="font-size:1.6rem"></i></div>
                        </div>
                        <a href="<?= BASE_URL ?>/admin/noticias" class="h1 mb-1"><?= (int) $conteos['noticias'] ?></a>
                        <div class="mt-2 small text-secondary">Noticias breves con fuente</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Podcasts</div>
                            <div class="ms-auto lh-1"><i class="ti ti-microphone text-red" style="font-size:1.6rem"></i></div>
                        </div>
                        <a href="<?= BASE_URL ?>/admin/podcasts" class="h1 mb-1"><?= (int) $conteos['podcasts'] ?></a>
                        <div class="mt-2 small text-secondary">Episodios publicados y borradores</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Videos y boletines</div>
                            <div class="ms-auto lh-1"><i class="ti ti-video text-cyan" style="font-size:1.6rem"></i></div>
                        </div>
                        <div class="h1 mb-1"><?= (int) $conteos['videos'] ?> <span class="text-secondary fs-4">/</span> <?= (int) $conteos['boletines'] ?></div>
                        <div class="mt-2 small text-secondary">Videos · Boletines NTEP</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos movimientos -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos movimientos</h3>
                <div class="ms-auto text-secondary small"><i class="ti ti-clock me-1"></i>logs_actividad</div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Acción</th>
                            <th>Entidad</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimosLogs)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No hay actividad registrada.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($ultimosLogs as $log): ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary-lt"><?= htmlspecialchars($log['accion']) ?></span>
                                <div class="text-secondary small text-truncate" style="max-width:220px" title="<?= htmlspecialchars($log['detalles'] ?? '') ?>"><?= htmlspecialchars(mb_substr($log['detalles'] ?? '', 0, 48)) ?></div>
                            </td>
                            <td><?= htmlspecialchars($log['entidad']) ?> <span class="text-secondary">#<?= (int) $log['entidad_id'] ?></span></td>
                            <td><?= htmlspecialchars(trim(($log['nombres'] ?? '') . ' ' . ($log['ap_paterno'] ?? ''))) ?: '—' ?></td>
                            <td class="text-secondary"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Últimos reportajes -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimos reportajes</h3>
                <div class="ms-auto">
                    <a href="<?= BASE_URL ?>/admin/reportajes" class="btn btn-sm btn-ghost">Ver todos</a>
                </div>
            </div>
            <div class="list-group list-group-flush list-group-hover">
                <?php if (empty($ultimosReportajes)): ?>
                <div class="list-group-item text-center text-muted py-4">Sin reportajes aún.</div>
                <?php endif; ?>
                <?php foreach ($ultimosReportajes as $r): ?>
                <a class="list-group-item list-group-item-action" href="<?= BASE_URL ?>/admin/reportajes/<?= (int) $r['id'] ?>/editar">
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($r['foto_principal']): ?>
                        <img src="<?= htmlspecialchars(ddpImgUrl($r['foto_principal']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8') ?>" class="rounded" width="48" height="36" style="object-fit:cover">
                        <?php endif; ?>
                        <div class="flex-fill min-w-0">
                            <div class="text-truncate fw-semibold"><?= htmlspecialchars($r['titulo']) ?></div>
                            <div class="text-secondary small">
                                <?= htmlspecialchars(trim(($r['autor_nombres'] ?? '') . ' ' . ($r['autor_ap'] ?? ''))) ?: 'Sin autor' ?> · <?= $r['fecha_publicacion'] ?? '—' ?>
                            </div>
                        </div>
                        <?php
                            $estadoClase = $r['estado'] === 'publicado' ? 'bg-success' : ($r['estado'] === 'borrador' ? 'bg-warning' : 'bg-secondary');
                        ?>
                        <span class="badge badge-estado <?= $estadoClase ?>"><?= htmlspecialchars($r['estado']) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between text-secondary small">
                    <span><i class="ti ti-users me-1"></i><?= (int) $conteos['autores'] ?> autores</span>
                    <span><i class="ti ti-user-shield me-1"></i><?= (int) $conteos['usuarios'] ?> usuarios</span>
                </div>
            </div>
        </div>
    </div>
</div>