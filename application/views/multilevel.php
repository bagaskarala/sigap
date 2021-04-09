<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <title> SIGAP - Sistem Informasi Gama Press</title>
    <meta
        property="og:title"
        content="Log In SIGAP"
    >
    <meta
        name="author"
        content="Bagaskara LA"
    >
    <meta
        property="og:locale"
        content="id_ID"
    >
    <meta
        name="description"
        content="SIGAP - Sistem Informasi GAMA PRESS"
    >
    <meta
        property="og:description"
        content="Sistem Informasi GAMA PRESS"
    >
    <link
        rel="canonical"
        href=""
    >
    <meta
        property="og:url"
        content="https://digitalpress.ugm.ac.id/sigap"
    >
    <meta
        property="og:site_name"
        content="SIGAP UGMPRESS"
    >

    <!-- Favicons -->
    <link
        rel="apple-touch-icon-precomposed"
        sizes="144x144"
        href="<?= base_url('assets/apple-touch-icon.png'); ?>"
    >
    <link
        rel="shortcut icon"
        href="<?= base_url('assets/favicon.ico'); ?>"
    >
    <meta
        name="theme-color"
        content="#3063A0"
    >

    <!-- BEGIN THEME STYLES -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/stylesheets/theme.min.css'); ?>"
        data-skin="default"
    >
    <link
        rel="stylesheet"
        href="<?= base_url('assets/stylesheets/theme-dark.min.css'); ?>"
        data-skin="dark"
    >
    <link
        rel="stylesheet"
        href="<?= base_url('assets/stylesheets/custom.css'); ?>"
    >
    <!-- Disable unused skin immediately -->

    <script>
    var skin = localStorage.getItem('skin') || 'default';
    var unusedLink = document.querySelector('link[data-skin]:not([data-skin="' + skin + '"])');

    unusedLink.setAttribute('rel', '');
    unusedLink.setAttribute('disabled', true);
    </script>
    <!-- END THEME STYLES -->
</head>

<body>
    <main class="empty-state empty-state-fullpage bg-primary">
        <div class="empty-state-container">
            <section class="card">
                <div class="card-body p-4">
                    <h3> Pilih Role </h3>
                    <p class="font-weight-bold">Halo, <?= $this->session->userdata('username'); ?></p>
                    <p>Akun anda mempunyai role author dan reviewer.</p>
                    <div class="d-flex flex-column">
                        <button
                            class="btn btn-success my-2"
                            onclick="location.href='<?= base_url('auth/multilevel/author'); ?>';"
                        >Masuk sebagai Author</button>
                        <button
                            class="btn btn-warning my-2"
                            onclick="location.href='<?= base_url('auth/multilevel/reviewer'); ?>';"
                        >Masuk sebagai Reviewer</button>
                    </div>
                    <div class="state-action m-0">
                        <a
                            href="<?= base_url('auth/logout'); ?>"
                            class="btn btn-lg btn-light"
                        >
                            <i class="fa fa-logout"></i> Logout</a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- BEGIN BASE JS -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <!-- END BASE JS -->
</body>

</html>
