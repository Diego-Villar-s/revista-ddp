<?php
$siteConfig = $siteConfig ?? [];
$institutional = $siteConfig['institutional_text'] ?? SITE_DESCRIPTION;
$email = $siteConfig['contact_email'] ?? 'info@dialogoydesarrollo.com.pe';
$facebook = $siteConfig['social_facebook'] ?? '#';
$tiktok = $siteConfig['social_tiktok'] ?? '#';
$instagram = $siteConfig['social_instagram'] ?? '#';
?>
<section class="w3l-footer-29-main py-5" id="footer">
    <div class="footer-29 py-md-3">
        <div class="container">
            <div class="row footer-top-29">
                <div class="col-lg-6 col-md-6 footer-list-29 footer-1">
                    <h6 class="footer-title-29">Quiénes Somos</h6>
                    <p><?= htmlspecialchars($institutional, ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="main-social-footer-29">
                        <a target="_blank" rel="noopener" href="<?= htmlspecialchars($facebook, ENT_QUOTES, 'UTF-8') ?>" class="facebook" aria-label="Facebook"><span class="fa fa-facebook-square"></span></a>
                        <a target="_blank" rel="noopener" href="<?= htmlspecialchars($tiktok, ENT_QUOTES, 'UTF-8') ?>" class="twitter" aria-label="TikTok"><img src="<?= PUBLIC_URL ?>/assets/img/tiktokp.png" alt="TikTok"></a>
                        <a target="_blank" rel="noopener" href="<?= htmlspecialchars($instagram, ENT_QUOTES, 'UTF-8') ?>" class="instagram" aria-label="Instagram"><span class="fa fa-instagram"></span></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 footer-list-29 footer-2 mt-md-0 mt-5">
                    <ul>
                        <h6 class="footer-title-29">Contenido</h6>
                        <li><a href="<?= BASE_URL ?>/noticias">Noticias</a></li>
                        <li><a href="<?= BASE_URL ?>/videos">Videos</a></li>
                        <li><a href="<?= BASE_URL ?>/podcast">Podcast</a></li>
                        <li><a href="<?= BASE_URL ?>/reportajes">Reportajes</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mt-lg-0 mt-5 footer-list-29 footer-3">
                    <div class="properties">
                        <h6 class="footer-title-29">Contacto</h6>
                        <ul>
                            <li><a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a></li>
                            <li><a href="<?= BASE_URL ?>/contacto">Formulario de contacto</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="bottom-copies text-center">
                <p class="copy-footer-29">© <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME_FULL, ENT_QUOTES, 'UTF-8') ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
    <button onclick="topFunction()" id="movetop" title="Volver arriba"><span class="fa fa-angle-up"></span></button>
    <script>
        window.onscroll = function () {
            document.getElementById('movetop').style.display = (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) ? 'block' : 'none';
        };
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</section>
