<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" type="text/css" href="styles/perfil.css">
</head>
<body>

    <div class="contenedor">
        <?php include_once 'navbar/navbar.html';?>
        <style>
        <?php include 'navbar/styles.css'; ?>
        </style>
        
        <h1>Editar datos del Usuario</h1>
        <div class="container">
            <div class="profile">
                <div class="profile-details">
                    <form id="profile-form" method="post" action="updatePerfil.php" enctype="multipart/form-data">
                        <div class="profile-image-container">
                            <img id="profile-image" src="" alt="Imagen de perfil">
                            <input type="file" name="foto_perfil" id="foto_perfil">
                        </div>

                        <div class="input-container">
                            <div>
                                <span class="label">Nombre(s):</span>
                                <input type="text" class="value" name="nombre" id="nombre" required>
                            </div>
                            <div>
                                <span class="label">Apellido(s):</span>
                                <input type="text" class="value" name="apellido" id="apellido" required>
                            </div>
                        </div>

                        <div>
                            <span class="label">Dirección de correo electrónico:</span>
                            <input type="text" class="value" name="correo" id="correo" required>
                        </div>

                        <div>
                            <span class="label">Fecha de nacimiento:</span>
                            <input class="value" type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        </div>
                        
                        <div>
                            <label class="password-label">Nueva contraseña:</label>
                            <input class="password-input" name="password" id="password" placeholder="Ingrese su nueva contraseña">
                        </div>
                        <div>
                            <label class="password-label">Confirmar nueva contraseña:</label>
                            <input class="password-input" name="Cpassword" id="Cpassword" placeholder="Confirme su nueva contraseña">
                        </div>
                        <div>
                            <button type="button" class="save-button" onclick="submitForm()">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function submitForm() {
            document.getElementById("profile-form").submit();
        }

        function cargarDatosUsuario() {
            fetch('api/api.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('nombre').value = data.nombres;
                    document.getElementById('apellido').value = data.apellidos;
                    document.getElementById('correo').value = data.correo;
                    document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento;

                    // Cargar la imagen de perfil
                    if (data.foto_perfil) {
                        document.getElementById('profile-image').src = 'data:image/jpeg;base64,' + data.foto_perfil;
                    }
                })
                .catch(error => console.error('Error al cargar los datos del usuario:', error));
        }

        window.onload = cargarDatosUsuario;
    </script>

</body>
</html>
