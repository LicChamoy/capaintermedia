<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrar Categoría</title>
        <link rel="stylesheet" href="styles/registro.css">
    </head>
    <body>
        <?php include_once 'navbar/navbar.html'; ?>
        <style>
            <?php include 'navbar/styles.css'; ?>
        </style>

        <div class="container">
            <h2>Registrar Categoría</h2>
            <form action="../guardar_categoria.php" method="POST">
                <div class="register-container">
                    <section class="Menu">
                        <label for="nombre">Nombre de la Categoría:</label>
                        <div class="txt">
                            <input type="text" placeholder="Nombre" id="nombre" name="nombre" required>
                        </div>
                    </section>
                </div>
                <input type="submit" value="Registrar">
            </form>
        </div>
    </body>
</html>
