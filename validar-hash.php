<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Verificar Hash - Sistema UFC Quixadá</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.ico" rel="icon">
    <link href="assets/img/favicon.ico" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
<!-- ======= Header ======= -->
<header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center justify-content-lg-between">
        <a href="index.html" class="logo me-auto me-lg-0"><img src="assets/img/favicon.ico" alt="" class="img-fluid"></a>
        <a href="painel/" class="get-started-btn scrollto">Fazer Login</a>
    </div>
</header><!-- End Header -->

<!-- ======= Main Section ======= -->
<section id="particles-js" class="d-flex align-items-center justify-content-center">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="150">
            <div class="col-xl-6 col-lg-8 text-center">
                <h1>Verificar Validade do Hash</h1>
                <h2>Insira o código para verificar a autenticidade do pedido</h2>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-lg-6">
                <?php
                // Capturar valores da URL
                $codigoPedido = isset($_GET['codigoPedido']) ? htmlspecialchars($_GET['codigoPedido']) : '';
                $hash = isset($_GET['hash']) ? htmlspecialchars($_GET['hash']) : '';
                ?>
                <form action="painel/util/validar-hash.php" method="POST" class="php-email-form">
                    <div class="form-group">
                        <label for="codigoPedido" style="color: white;">Código do Pedido</label>
                        <input type="text" name="codigoPedido" class="form-control" id="codigoPedido" value="<?= $codigoPedido ?>" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="hash" style="color: white;">Hash</label>
                        <input type="text" name="hash" class="form-control" id="hash" value="<?= $hash ?>" required>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-8 text-center">
                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'valid'): ?>
                        <div class="alert alert-success">O hash é válido!</div>
                    <?php elseif ($_GET['status'] === 'invalid'): ?>
                        <div class="alert alert-danger">O hash é inválido!</div>
                    <?php elseif ($_GET['status'] === 'not_found'): ?>
                        <div class="alert alert-danger">O pedido não foi encontrado!</div>
                    <?php elseif ($_GET['status'] === 'error'): ?>
                        <div class="alert alert-danger">Ocorreu um erro ao verificar o hash!</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section><!-- End Main Section -->

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="container">
        <div class="copyright">
            &copy; Copyright <strong><span>Universidade Federal do Ceará - Campus Quixadá</span></strong>. Todos os direitos reservados
        </div>
        <div class="credits">
            Desenvolvido por <a href="https://github.com/gabriel-bri" target="_blank">Gabriel Brito</a> e <a href="https://github.com/soumbra" target="_blank">Franciel Silveira</a>
        </div>
    </div>
</footer><!-- End Footer -->

<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>
    particlesJS.load('particles-js', 'particles.json', function(){
        console.log('particles.json loaded...');
    });
</script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
</body>

</html>
