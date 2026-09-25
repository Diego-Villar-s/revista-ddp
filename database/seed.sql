SET NAMES utf8mb4;

-- DDP Noticias / datos de demostración
-- Importar después de schema.sql. La contraseña real se documenta solo en README.md.

USE revista_digital;

INSERT INTO usuarios (nombres, ap_paterno, ap_materno, email, password_hash, rol, activo) VALUES
('Carlos', 'Mendoza', 'Ríos', 'admin@dialogoydesarrollo.com.pe', '$2y$10$g/B.ppuCwsV9eGlcqtaNhuifxKwv74.4.DeT..Z3UuEUeNq49cyIi', 'admin', 1),
('María', 'Torres', 'Vargas', 'editor@dialogoydesarrollo.com.pe', '$2y$10$UcUqWjZtB9KhBEWmzURYFOn2MJRyN29k4WY8IGvHZcfre9h13mlGS', 'editor', 1),
('Juan', 'Paredes', 'Soto', 'redactor@dialogoydesarrollo.com.pe', '$2y$10$Os.Gy2BV60oMrZxwS2VK3.en5FjGYp7p2fTcd.xKlw5Zp9w2gxhHC', 'redactor', 1);

INSERT INTO autores (nombres, ap_paterno, ap_materno, nickname, es_nickname) VALUES
('Carlos', 'Mendoza', 'Ríos', 'Carlos Mendoza', 0),
('María', 'Torres', 'Vargas', 'María Torres', 0),
('Juan', 'Paredes', 'Soto', '', 0),
('Ana', 'Quispe', 'Mamani', 'Ana Q.', 1),
('Redacción', 'DDP', '', 'Redacción DDP', 1);

INSERT INTO reportajes
(titulo, slug, resumen_corto, desarrollo, foto_principal, alt_foto_principal, fecha_publicacion, estado, es_destacado, meta_titulo, meta_descripcion, autor_id, usuario_id)
VALUES
('La minería artesanal en Madre de Dios: entre la informalidad y la esperanza',
 'mineria-artesanal-madre-de-dios',
 'Las familias mineras de Madre de Dios buscan formalizarse sin perder su identidad territorial.',
 '<h2>Un oficio ancestral en tiempos modernos</h2><p>En las orillas del río Inambari, cientos de familias dependen de la minería artesanal. El proceso de formalización continúa y las nuevas reglas ambientales exigen un camino más seguro.</p><h2>Alternativas productivas</h2><p>Las cooperativas están comprando equipos seguros y creando rutas de venta directa. La educación ambiental y la formalización de la actividad son pasos hacia una minería más sostenible.</p>',
 'assets/img/reportaje-28-08-26.jpg', 'Mineros artesanales trabajando junto a un río en Madre de Dios', '2026-09-05', 'publicado', 1,
 'La minería artesanal en Madre de Dios | DDP Noticias', 'Un reportaje sobre las alternativas ecológicas y la seguridad minera en la región.', 1, 1),
('Educación intercultural bilingüe: el reto de enseñar en lenguas originarias',
 'educacion-intercultural-bilingue-reto',
 'El Perú trabaja para preservar sus lenguas originarias dentro de la escuela pública.',
 '<h2>El contexto peruano</h2><p>Más de cuatro millones de personas hablan una lengua originaria. Las comunidades exigen materiales y docentes preparados.</p><h2>Una escuela cercana</h2><p>Los programas interculturales combinan la lengua local con la enseñanza de ciencias y ciudadanía.</p>',
 'assets/img/reportaje-18-08-26.jpg', 'Docente y estudiantes en un aula intercultural bilingüe', '2026-08-28', 'publicado', 1,
 'Educación intercultural bilingüe en el Perú | DDP Noticias', 'Analizamos el desafío de enseñar en lenguas originarias y preparar a docentes.', 2, 1),
('Agua y saneamiento en comunidades rurales: un derecho pendiente',
 'agua-saneamiento-comunidades-rurales',
 'La infraestructura de agua sigue llegando con lentitud a las comunidades de la sierra.',
 '<h2>La brecha territorial</h2><p>En algunas comunidades, las familias todavía no reciben agua potable de forma segura. Los proyectos de saneamiento deben diseñarse con las comunidades.</p><h2>Respuestas locales</h2><p>Los comités de agua verifican la operación de los sistemas y promueven un consumo responsable.</p>',
 'assets/img/reportaje-12-08-26.jpg', 'Comunidad rural reunida junto a su sistema de agua', '2026-09-01', 'publicado', 0,
 'Agua y saneamiento en comunidades rurales | DDP Noticias', 'Un análisis de las brechas de agua potable y saneamiento en el Perú rural.', 1, 1),
('El comercio justo en los Andes: cooperativas que transforman comunidades',
 'comercio-justo-andes-cooperativas',
 'Las cooperativas andinas usan el comercio justo para mejorar ingresos y proteger sus cultivos.',
 '<h2>Producir con identidad</h2><p>La papa, la quinua y el café forman parte de una estrategia de producción con identidad. Las cooperativas negocian precios más justos para sus socios.</p><h2>El valor del territorio</h2><p>La experiencia demuestra que la conectividad y la formación para exportar son claves para el desarrollo.</p>',
 'assets/img/bannerimg.jpg', 'Productores de papa nativa trabajando en la sierra', '2026-08-15', 'publicado', 0,
 'El comercio justo en los Andes | DDP Noticias', 'Las cooperativas andinas que transforman la economía de sus comunidades.', 2, 1),
('Tecnología y campo: aplicaciones móviles que revolucionan la agricultura familiar',
 'tecnologia-campo-aplicaciones-agricultura',
 'Las aplicaciones llevan precios, clima y alertas de plagas a los pequeños productores.',
 '<h2>Conectarse para producir</h2><p>La telefonía móvil permite consultar el clima y los precios en tiempo real. Las herramientas digitales llegan a zonas con poca conectividad.</p><h2>El desafío pendiente</h2><p>La brecha de cobertura sigue siendo una barrera para muchas comunidades rurales.</p>',
 'assets/img/reportaje-12-08-26.jpg', 'Productor consultando una aplicación móvil en el campo', '2026-09-08', 'publicado', 1,
 'Tecnología y campo: aplicaciones para la agricultura | DDP Noticias', 'Cómo las aplicaciones móviles apoyan a la agricultura familiar peruana.', 3, 1),
('Mujeres rurales emprendedoras: historias de superación en Ayacucho',
 'mujeres-rurales-emprendedoras-ayacucho',
 'Organizaciones de mujeres de Ayacucho impulsan textiles, gastronomía y turismo comunitario.',
 '<h2>Liderazgo femenino en los Andes</h2><p>Las tejedoras y grupos de gastronomía han convertido técnicas familiares en negocios sostenibles. La formación y el acceso a mercados son clave.</p><h2>Una red de apoyo</h2><p>Las asociaciones funcionan como espacios de acompañamiento y financiamiento para sus asociados.</p>',
 'assets/img/bannerimg.jpg', 'Artesanas trabajando con textiles en una comunidad de Ayacucho', '2026-08-20', 'publicado', 0,
 'Mujeres rurales emprendedoras en Ayacucho | DDP Noticias', 'Historias de mujeres rurales que lideran proyectos económicos.', 1, 1),
('El turismo comunitario como herramienta de desarrollo sostenible',
 'turismo-comunitario-desarrollo-sostenible',
 'Las comunidades reciben a los visitantes y comparten su cultura con respeto.',
 '<h2>Turismo con identidad</h2><p>Caminatas, talleres y alojamiento comunitario generan nuevas fuentes de ingreso. Las comunidades organizan la visita para cuidar su territorio.</p><h2>Capacitación y respeto</h2><p>La formación en atención al visitante y la conciliación de beneficios es esencial.</p>',
 'assets/img/bannerimg.jpg', 'Turistas recorriendo un paisaje comunitario andino', '2026-09-10', 'publicado', 0,
 'El turismo comunitario como desarrollo sostenible | DDP Noticias', 'El turismo comunitario ofrece nuevas oportunidades económicas a los territorios.', 2, 2),
('El desafío de la seguridad alimentaria en zonas urbanas marginales',
 'seguridad-alimentaria-zonas-urbanas',
 'Los comedores populares y la agricultura urbana combate la inseguridad alimentaria en Lima.',
 '<h2>La red invisible</h2><p>Los comedores populares atienden a miles de familias y conectan con productores locales. La ciudad exige respuestas concretas para reducir brechas.</p><h2>Alimentos y dignidad</h2><p>Una alimentación sana también requiere acceso a productos accesibles producidos por pequeñas superficies.</p>',
 'assets/img/bannerimg.jpg', 'Comedor popular de Lima durante el servicio de comidas', '2026-08-10', 'publicado', 0,
 'La seguridad alimentaria en zonas urbanas | DDP Noticias', 'Comedores populares y seguridad alimentaria en los barrios de Lima.', 3, 2),
('La reforestación comunitaria en la Amazonía: sembrando futuro',
 'reforestacion-comunitaria-amazonia',
 'Las comunidades amazónicas restauran bosques y crean cadenas de ingreso con especies nativas.',
 '<h2>Restaurar el bosque</h2><p>Los proyectos de forestación combinan conocimiento local con el cuidado de especies nativas. La protección de cuencas es un objetivo compartido.</p><h2>Ingresos que se quedan</h2><p>El uso responsable de los recursos del bosque permite que las comunidades obtengan ingresos y mantengan el control de sus recursos.</p>',
 'assets/img/reportaje-18-08-26.jpg', 'Árboles nativos creciendo en un territorio de la Amazonía', '2026-09-03', 'publicado', 0,
 'La reforestación comunitaria en la Amazonía | DDP Noticias', 'Comunidades amazónicas restauran bosques y generan identidad económica.', 1, 1),
('Movilidad urbana sostenible: el caso del transporte en Arequipa',
 'movilidad-urbana-sostenible-arequipa',
 'El sistema de transporte de Arequipa busca reducir emisiones y mejorar la vida cotidiana.',
 '<h2>La ciudad blanca</h2><p>La renovación de flota y la integración tarifaria forman parte de un nuevo sistema. Los usuarios prueban una nueva forma de desplazarse por la ciudad.</p><h2>Un cambio cotidiano</h2><p>La infraestructura también requiere educación y participación ciudadana.</p>',
 'assets/img/bannerimg.jpg', 'Autobuses del sistema integrado de transporte de Arequipa', '2026-08-25', 'publicado', 0,
 'Movilidad urbana sostenible en Arequipa | DDP Noticias', 'El nuevo sistema de transporte busca una ciudad más limpia y conectada.', 2, 2),
('Programas de desarrollo rural: una evaluación desde los territorios',
 'programas-desarrollo-rural-evaluacion',
 'Borrador de análisis sobre la implementación de programas de desarrollo rural.',
 '<p>Contenido pendiente de edición y verificación de fuentes.</p>',
 'assets/img/reportaje-28-08-26.jpg', 'Imagen provisional de un territorio rural', '2026-09-15', 'borrador', 0,
 '', '', 3, 3),
('Archivo: informe de desarrollo territorial',
 'archivo-informe-desarrollo-territorial',
 'Documento archivado que permanece disponible para consulta editorial interna.',
 '<p>Contenido archivado.</p>',
 'assets/img/reportaje-12-08-26.jpg', 'Imagen de archivo del proyecto', '2026-06-15', 'archivado', 0,
 '', '', 1, 1),
('Conectividad rural: voces de los territorios',
 'conectividad-rural-voces-territorios',
 'Las comunidades rurales impulsan soluciones digitales para acortar las distancias.',
 '<h2>Conectarse para aprender</h2><p>Las escuelas, bibliotecas y organizaciones comunitarias usan herramientas digitales para compartir conocimientos y fortalecer sus iniciativas.</p><h2>Una apuesta territorial</h2><p>La conectividad solo es útil cuando responde a las necesidades de cada territorio.</p>',
 'assets/img/reportaje-12-08-26.jpg', 'Personas usando tecnología en una comunidad rural', '2026-07-20', 'publicado', 0,
 'Conectividad rural: voces de los territorios | DDP Noticias', 'Una mirada a las soluciones digitales de las comunidades rurales.', 2, 1);

INSERT INTO reportajes_fotos (reportaje_id, url_foto, url_foto_thumb, url_foto_webp, orden, descripcion) VALUES
(1, 'assets/img/reportaje-28-08-26.jpg', 'assets/img/reportaje-28-08-26.jpg', 'assets/img/reportaje-28-08-26.jpg', 1, 'Panorámica del territorio de Madre de Dios'),
(1, 'assets/img/reportaje-18-08-26.jpg', 'assets/img/reportaje-18-08-26.jpg', 'assets/img/reportaje-18-08-26.jpg', 2, 'Un oficio que atraviesa generaciones'),
(1, 'assets/img/reportaje-12-08-26.jpg', 'assets/img/reportaje-12-08-26.jpg', 'assets/img/reportaje-12-08-26.jpg', 3, 'Comunidad junto al río'),
(2, 'assets/img/reportaje-18-08-26.jpg', 'assets/img/reportaje-18-08-26.jpg', 'assets/img/reportaje-18-08-26.jpg', 1, 'Aula intercultural');

INSERT INTO noticias (titulo, slug, foto, link_externo, fecha_publicacion, estado, usuario_id) VALUES
('Congreso aprueba Ley de Fortalecimiento del Agro Peruano', 'congreso-ley-fortalecimiento-agro', 'assets/img/nota-facebook-20-11-25.png', 'https://www.gob.pe/congreso', '2026-09-10', 'publicado', 1),
('Banco Central reporta inflación controlada en agosto 2026', 'banco-central-inflacion-agosto-2026', 'assets/img/nota-facebook-21-11-25b.png', 'https://www.bcrp.gob.pe', '2026-09-08', 'publicado', 1),
('MINSA intensifica campaña de vacunación contra la influenza', 'minsa-campana-vacunacion-influenza', 'assets/img/nota-facebook-21-11-25.png', 'https://www.gob.pe/minsa', '2026-09-05', 'publicado', 2),
('FONCODES amplía cobertura de programas sociales en zona andina', 'foncodes-amplia-cobertura-programas-sociales', 'assets/img/nota-facebook-20-11-25.png', 'https://www.gob.pe/foncodes', '2026-09-01', 'publicado', 2),
('Exportaciones peruanas de agroindustria alcanzan récord histórico', 'exportaciones-agroindustria-record-historico', 'assets/img/nota-facebook-21-11-25b.png', 'https://www.gob.pe/mincetur', '2026-08-28', 'publicado', 1);

INSERT INTO boletines (numero_boletin, resumen, temas, foto_portada, archivo_pdf, fecha_publicacion, estado, usuario_id) VALUES
('NTEP-2026-09', 'Edición septiembre 2026 con análisis sobre minería artesanal, educación intercultural y desarrollo sostenible.', CONCAT('Promueven megaproyectos turísticos por S/ 2,400 millones.', CHAR(10), 'Invertirán S/ 9 millones en zonas rurales de Cusco.', CHAR(10), 'Producción láctea se duplica en Cajamarca.'), 'assets/img/boletin-ntep-45.png', 'boletines/ntep-2026-09.pdf', '2026-09-01', 'publicado', 1),
('NTEP-2026-08', 'Edición agosto 2026 con reportajes sobre comercio justo, mujeres emprendedoras y turismo comunitario.', CONCAT('El comercio justo llega a nuevas familias y mejora sus ingresos.', CHAR(10), 'Mujeres rurales muestran nuevas capacidades.', CHAR(10), 'El turismo comunitario conversa con el territorio.'), 'assets/img/boletin-ntep-45.png', 'boletines/ntep-2026-08.pdf', '2026-08-01', 'publicado', 1);

INSERT INTO podcasts (titulo, slug, descripcion, tipo, url_embed, portada, duracion_segundos, fecha_publicacion, estado, usuario_id) VALUES
('Minería artesanal: voces desde el río', 'mineria-artesanal-voces-rio', 'Conversación con voces de la formalización minera artesanal.', 'embed', 'https://open.spotify.com/embed/episode/example1', 'assets/img/podcast.png', 2340, '2026-09-05', 'publicado', 1),
('Educación bilingüe: ¿preservar o modernizar?', 'educacion-bilingue-preservar-modernizar', 'El diálogo entre lenguas, territorio y educación pública.', 'embed', 'https://open.spotify.com/embed/episode/example2', 'assets/img/podcast.png', 1860, '2026-08-28', 'publicado', 1),
('Agua potable: el derecho negado', 'agua-potable-derecho-negado', 'Una mirada a las brechas de agua y saneamiento en el campo.', 'embed', 'https://open.spotify.com/embed/episode/example3', 'assets/img/podcast.png', 2100, '2026-08-15', 'publicado', 2),
('El comercio justo en voces de los Andes', 'comercio-justo-voces-andes', 'Las experiencias de cooperativas andinas que deciden sus precios.', 'embed', 'https://open.spotify.com/embed/episode/example4', 'assets/img/podcast.png', 1980, '2026-09-01', 'publicado', 2);

INSERT INTO videos (titulo, slug, descripcion, tipo, url_embed, poster, duracion_segundos, fecha_publicacion, estado, usuario_id) VALUES
('Documental: Minería en Madre de Dios', 'documental-mineria-madre-de-dios', 'Un documental sobre territorio, formalización y ambiente.', 'embed', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'assets/img/podcast.png', 1800, '2026-09-05', 'publicado', 1),
('Entrevista: Educación intercultural bilingüe', 'entrevista-educacion-eib', 'Entrevista con docentes y estudiantes sobre lenguas originarias.', 'embed', 'https://www.youtube.com/embed/dQw4w9WgXcQ2', 'assets/img/podcast.png', 1200, '2026-08-28', 'publicado', 2),
('Video informativo: Agua para todos', 'video-informativo-agua-todos', 'Historias de comunidades que organizan el acceso al agua.', 'embed', 'https://www.youtube.com/embed/dQw4w9WgXcQ3', 'assets/img/podcast.png', 900, '2026-09-01', 'publicado', 1);

INSERT INTO especiales (titulo_completo, palabra_resaltada, titulo_leet, url_video, fecha_creacion, activo) VALUES
('Negocios que transforman el territorio', 'negocios', 'n3g0c10s qu3 tr4nsf0rm4n el t3rr1t0r10', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', '2026-09-20 10:00:00', 1),
('Ideas que mueven las comunidades', 'comunidades', '1d34s qu3 mu3v3n l4s c0mun1d4d3s', 'https://vimeo.com/76979871', '2026-09-15 10:00:00', 1),
('Un negocio rentable para bandas criminales', 'rentable', 'un n3g0c10 r3nt4bl3 p4r4 b4nd4s cr1m1n4l3s', 'https://youtu.be/dQw4w9WgXcQ', '2026-09-10 10:00:00', 1),
('Historias que dejan huella', 'historias', 'h1st0r14s qu3 d3j4n hu3ll4', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-09-05 10:00:00', 1);

INSERT INTO paginas (slug, titulo, contenido, imagen, meta_titulo, meta_descripcion, activo) VALUES
('sobre-nosotros', 'Sobre D&D', '<p>Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.</p><p>Trabajamos con las comunidades y los actores que impulsan desarrollo sostenible, derechos y territorios con identidad.</p>', 'assets/img/bannerimg.jpg', 'Sobre Diálogo y Desarrollo Perú', 'Conoce el propósito y la mirada editorial de DDP Noticias.', 1),
('alianzas', 'Alianzas', '<p>Creemos en el diálogo como una forma de construir soluciones. Este espacio presenta las alianzas y redes con las que colaboramos.</p><p>Si tu organización propone una iniciativa de desarrollo, puede escribirnos desde el formulario de contacto.</p>', 'assets/img/bannerimg.jpg', 'Alianzas de DDP Noticias', 'Conoce las alianzas y redes de colaboración de DDP Noticias.', 1),
('contacto', 'Contacto', '<p>¿Tienes una historia, una propuesta o una denuncia relacionada con el desarrollo del país? Escríbenos. Leemos cada mensaje y respondemos con atención.</p><p><strong>Correo:</strong> info@dialogoydesarrollo.com.pe</p>', 'assets/img/bannerimg.jpg', 'Contacto | DDP Noticias', 'Contacta al equipo editorial de DDP Noticias.', 1);

INSERT INTO configuracion (clave, valor, tipo) VALUES
('site_description', 'Portal de noticias sociales, territoriales y de desarrollo sostenible', 'text'),
('institutional_text', 'Somos un espacio de periodismo independiente que busca visibilizar las acciones de diálogo en el país desde una mirada constructiva.', 'text'),
('about_cta', 'Nosotros', 'text'),
('contact_email', 'info@dialogoydesarrollo.com.pe', 'email'),
('social_facebook', 'https://www.facebook.com/DialogoyDesarrolloPeru', 'url'),
('social_tiktok', 'https://www.tiktok.com/@dialogo.y.desarrollo', 'url'),
('social_instagram', 'https://www.instagram.com/dialogo.y.desarrollo/', 'url'),
('youtube_embed', 'https://www.youtube.com/embed/2jI6fHBtRJU', 'url'),
('youtube_title', 'Video institucional de DDP Noticias', 'text');

INSERT INTO logs_actividad (usuario_id, accion, entidad, entidad_id, detalles) VALUES
(1, 'crear', 'reportaje', 1, 'Reportaje de minería artesanal creado'),
(1, 'publicar', 'reportaje', 1, 'Reportaje de minería artesanal publicado'),
(2, 'crear', 'noticia', 1, 'Noticia del Congreso creada'),
(1, 'crear', 'boletin', 1, 'Boletín NTEP-2026-09 creado'),
(1, 'crear', 'podcast', 1, 'Podcast de minería artesanal creado'),
(1, 'crear', 'video', 1, 'Video documental creado');
