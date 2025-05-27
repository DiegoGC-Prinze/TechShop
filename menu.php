<header> 
    <div class="navbar navbar-expand-lg cyberpunk-navbar shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="navbar-brand text-neon">
                <strong>TechShop</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarHeader" aria-controls="navbarHeader"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="#" class="nav-link nav-link-neon active">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link nav-link-neon">Contacto</a>
                    </li>
                </ul>

                <a href="checkout.php" class="btn btn-neon position-relative btn-sm me-2">
                    <i class="bi bi-cart"></i> Carrito
                    <span id="num_cart" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        0
                    </span>
                </a>

                <?php if(isset($_SESSION['user_id'])){ ?>
                    <div class="dropdown">
                        <button class="btn btn-neon btn-sm dropdown-toggle" type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?php echo $_SESSION['user_name']; ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="btn_session">
                            <li><a class="dropdown-item" href="Compras.php">Mis Compras</a></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm ms-2">
                        <i class="bi bi-person-circle"></i> Ingresar
                    </a>
                    <a href="loginAdmin.php" class="btn btn-outline-warning btn-sm ms-2">
                        <i class="bi bi-shield-lock-fill"></i> Admin
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</header>
