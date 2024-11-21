<?php
session_start();
$nombre = $_SESSION['nombre'];
$id_alumno = $_SESSION['id_alumno']; // ID del alumno actual

// Conectar a la base de datos
require_once "conecta.php";
$conexion = conecta();

// Consulta para obtener los proyectos como líder
$sql_lider = "SELECT * FROM proyectos WHERE lider_id = $id_alumno";
$resultado_lider = pg_query($conexion, $sql_lider);

// Consulta para obtener los proyectos como miembro
$sql_miembro = "SELECT p.* 
                FROM proyectos p
                INNER JOIN equipos e ON e.proyecto_id = p.id_proyecto
                WHERE e.miembro_id = $id_alumno AND e.rol = 'miembro'";
$resultado_miembro = pg_query($conexion, $sql_miembro);


$es_miembro = false; // Inicializar como falso por defecto

if (pg_num_rows($resultado_miembro) > 0) {
    $es_miembro = true; // Cambiar a verdadero si el usuario es miembro
}


// Combina los resultados en un solo array para evitar duplicados
$proyectos = [];

// Agrega proyectos como líder
while ($fila = pg_fetch_assoc($resultado_lider)) {
    $proyectos[$fila['id_proyecto']] = $fila;
}

// Agrega proyectos como miembro (evitando duplicados)
while ($fila = pg_fetch_assoc($resultado_miembro)) {
    if (!isset($proyectos[$fila['id_proyecto']])) {
        $proyectos[$fila['id_proyecto']] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Linkproject</title>
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"> 
</head>
<style>
    /* Contenedor general de la sección de proyectos */
.project-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 2rem;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
}

/* Botones de acciones (crear, eliminar) */
.project-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 1.5rem;
}

.project-btn {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: #4caf50;
    border-radius: 8px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.project-btn:hover {
    background-color: #45a049;
}

/* Vista de proyectos */
.project-view {
    text-align: center;
    width: 100%;
    min-height: 400px; /* Establece un alto mínimo para dar más presencia */
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 1rem; /* Añade un poco de margen superior */
    box-sizing: border-box;
}

.project-view h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: flex;
}

.project-view p {
    font-size: 1rem;
    color: #666;
}


#notificationDropdown {
    display: none;
    position: absolute;
    top: 50px;
    right: 10px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    min-width: 250px;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
}

#notificationDropdown ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

#notificationDropdown ul li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

#notificationDropdown ul li:last-child {
    border-bottom: none;
}

#notificationDropdown ul li a {
    color: #333;
    text-decoration: none;
}

#notificationDropdown ul li:hover {
    background-color: #f1f1f1;
}


.error-message {
    margin-top: 10px;
    font-size: 14px;
    color: red;
    font-weight: bold;
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
          <div id="notificationDropdown" class="dropdown-content" style="display: none;">
    <!-- Aquí aparecerán las notificaciones -->
         <ul id="notificationList"></ul>
        </div>
        <div class="dropdown" id="dropdownMenu">
            <a href="Intermedio.php">Perfil</a>
            <a href="CerrarSesion.php">Cerrar Sesion</a>
        </div>
    </div>
</div>

<div class="welcome">
    <h1>Bienvenido <?php echo htmlspecialchars($nombre); ?></h1>
</div>

<div class="project-section">
    <div class="project-actions">
        <?php if (!$es_miembro): ?>
            <a href="Crear.php" class="project-btn">Crear Proyecto</a>
        <?php else: ?>
            <button class="project-btn" disabled style="background-color: #ccc; cursor: not-allowed;">
                Crear Proyecto
            </button>
            <p style="color: red;">No puedes crear un proyecto porque ya eres miembro de un equipo.</p>
        <?php endif; ?>
    </div>
</div>

<div class="project-view">
    <h2>Tus Proyectos</h2>
    <?php
    if (count($proyectos) > 0) {
        foreach ($proyectos as $proyecto) {
            echo "<div style='font-size: 18px; margin-top: 10px;'>";
            echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
            echo "<ul style='list-style-type: none; padding: 0;'>";
            echo "<li> <img src='" . htmlspecialchars($proyecto["logo"]) . "' alt='Logo' style='width: 200px; height: 200px;'> </li>";
            echo "<li><strong>Nombre del Proyecto:</strong> " . $proyecto['nombre'] . "</li>";
            echo "<li><strong>Descripción:</strong> " . $proyecto['descripcion'] . "</li>";
            echo "<li><strong>Asesor:</strong> " . $proyecto['asesor'] . "</li>";
            echo "<li><strong>Conocimiento:</strong> " . $proyecto['conocimientos'] . "</li>";
            echo "<li><strong>Innovacion:</strong> " . $proyecto['nivel_innovacion'] . "</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";

            // Mostrar botón para eliminar proyecto solo si es líder
            if ($proyecto['lider_id'] == $id_alumno) {
                echo "<form action='eliminar_proyecto.php' method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='id_proyecto' value='" . htmlspecialchars($proyecto['id_proyecto']) . "'>";
                echo "<input type='submit' value='Eliminar Proyecto' style='background-color: #ff4c4c; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;' onclick=\"return confirm('¿Estás seguro de eliminar este proyecto?');\">";
                echo "</form>";
            }
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No tienes proyectos aún.</p>";
    }
    ?>
</div>

<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

   

function toggleNotificationDropdown() {
    var dropdown = document.getElementById("notificationDropdown");

    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";
        obtenerInvitaciones(); // Llamar la función para obtener invitaciones
    } else {
        dropdown.style.display = "none";
    }
}

function obtenerInvitaciones() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "obtener_invitaciones.php", true); // Llamar al script PHP para obtener invitaciones
    xhr.onload = function() {
        if (xhr.status === 200) {
            var invitacionesHTML = xhr.responseText;  // Obtenemos directamente el HTML generado
            var notificationList = document.getElementById("notificationList");
            notificationList.innerHTML = ""; // Limpiar lista antes de agregar las nuevas invitaciones

            if (invitacionesHTML.trim() !== "") {
                // Si la respuesta no está vacía, añadirla al contenedor
                notificationList.innerHTML = invitacionesHTML;
            } else {
                notificationList.innerHTML = "<li>No tienes invitaciones pendientes.</li>";
            }
        } else {
            alert("Error al obtener invitaciones.");
        }
    };
    xhr.send();
}


function actualizarEstadoInvitacion(id_invitacion, estado) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "actualizar_invitacion.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("Invitación " + estado);
            obtenerInvitaciones(); // Refrescar la lista de invitaciones
            location.reload();
        } else {
            alert("Error al actualizar la invitación.");
        }
    };
    xhr.send("id_invitacion=" + id_invitacion + "&estado=" + estado);
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

