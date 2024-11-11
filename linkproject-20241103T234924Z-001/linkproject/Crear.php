<?php
require_once "conecta.php";
$conexion = conecta();
$sql = "SELECT * FROM proyectos";

//Cambios que se hicieron :Se cambio el nombre de la columna del id del proyecto por id_proyecto tambien se definio por defecto cupos y miembros como 0 para que no existiera problemas por estar definidas como not null.
//Se debe tener una carpeta llamada "logos" para los logos de los proyectos y una carpeta llamada "foto" para los perfiles de usuario

session_start(); // Iniciar la sesión para obtener el id_alumno
// Suponiendo que el id_alumno está almacenado en la sesión
$id_alumno = $_SESSION['id_alumno'];

$sql_verificar = "SELECT * FROM proyectos WHERE lider_id = $id_alumno";
$resultado_verificar = pg_query($conexion, $sql_verificar);

if (pg_num_rows($resultado_verificar) > 0) {
    // Si el alumno ya está registrado en un proyecto, mostrar un mensaje y redirigir
    echo '<script>alert("Ya estás registrado en un proyecto. No puedes crear ni unirte a otro proyecto."); window.location.href = "panelUsuario.php";</script>';
    exit(); // Detener la ejecución
}

// Si el alumno no está registrado en un proyecto, continuar con el formulario
$sql = "SELECT * FROM proyectos";
$resultado = pg_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Proyecto</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> <!-- Enlace a tu archivo CSS -->
</head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .campo {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .campo::placeholder {
            color: #999;
        }

        input[type="file"] {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="button"] {
            background-color: #1E8449;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
            margin-top: 20px;
        }

        input[type="button"]:hover {
            background-color: #0056b3;
        }

        #mensaje {
            width: 100%;
            height: 30px;
            background: #EFEFEF;
            border-radius: 5px;
            color: #F00;
            font-size: 16px;
            line-height: 25px;
            text-align: center;
            margin-top: 20px;
            padding: 5px;
            display: none;
        }
    </style>
<body>

<div class="navbar">
    <div>
        <a href="Home_alumno.php">Inicio</a>
        <a href="Equipo.php">Equipo</a>
        <a href="Gestor.php">Gestor de Proyectos</a>
        <a href="Visualizador.php">Proyectos</a>
        <a href="Directorio.php">Directorio</a>
    </div>
    <div>
        <i class="fas fa-bell notification-icon" onclick="toggleNotificationDropdown()"></i>
        <i class="fas fa-user-circle profile-icon" onclick="toggleDropdown()"></i>
        <div class="dropdown" id="dropdownMenu">
            <a href="completar_perfil.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>
    <div class="container">
        <h1>Tu Proyecto</h1>
        <form name="forma01" action="guardar_proyecto.php" method="POST" enctype="multipart/form-data" align="center">
            <input type="text" name="nombre" id="nombre" class="campo" placeholder="Nombre" required>
            <input type="text" name="descripcion" id="descripcion" class="campo" placeholder="Descripcion" required>
            <input type="text" name="area" id="area" class="campo" placeholder="Área">
            <input type="text" name="asesor" id="asesor" class="campo" placeholder="Asesor">
            <input type="text" name="conocimientos" id="conocimientos" class="campo" placeholder="Conocimientos">
            <input type="text" name="nivel_innovacion" id="nivel_innovacion" class="campo" placeholder="Innovación">
            
            <!-- Sección para el logo -->
            <label for="logo" class="campo">Logo</label>
            <input type="file" name="logo" id="logo" accept="image/*" required>

            <!-- Campo oculto para pasar el id_alumno -->
            <input type="hidden" name="lider_id" value="<?php echo $id_alumno; ?>">
            
            <!-- Botón para guardar -->
            <input type="submit" value="Guardar">
        </form>
    </div>

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    function toggleNotificationDropdown() {
        // Aquí puedes implementar la lógica para el menú de notificaciones si lo necesitas
        alert('Aquí se mostrarían las notificaciones.'); // Ejemplo de alerta
    }

    // Cerrar el menú desplegable si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon') && !event.target.matches('.notification-icon')) {
            var dropdown = document.getElementById("dropdownMenu");
            dropdown.style.display = "none"; // Oculta el menú desplegable
        }
    }
</script>

</body>
</html>
