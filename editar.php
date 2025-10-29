<?php

include("conexion.php");

//obtener todas las materias del select :)
$materias = seleccionar("SELECT id_materia, nombre FROM materias", []);

//obtener id_calificacion o id_alumno :)
$id_calificacion = $_GET['id'] ?? null;
$id_alumno = $_GET['id_alumno'] ?? null;

$nueva_calificacion = false;

//si viene id_calificacion que sea editar :)
if ($id_calificacion) {
    $datos_result = seleccionar("
        SELECT a.id_alumno, a.nombre AS alumno, a.grado, a.promedio,
               c.id_calificacion, c.id_materia, c.calificacion, c.fecha
        FROM alumnos a
        LEFT JOIN calificaciones c ON a.id_alumno = c.id_alumno
        WHERE c.id_calificacion = $1
    ", [$id_calificacion]);

    $datos = $datos_result[0] ?? null;
    if (!$datos) {
        header("Location: index.php");
        exit();
    }
}

//si viene id_alumno => agregar calificación :)
elseif ($id_alumno) {
    $datos_result = seleccionar("
        SELECT id_alumno, nombre AS alumno, grado, promedio
        FROM alumnos
        WHERE id_alumno = $1
    ", [$id_alumno]);

    $datos = $datos_result[0] ?? null;
    if (!$datos) {
        header("Location: index.php");
        exit();
    }

    $nueva_calificacion = true;
} else {
    header("Location: index.php");
    exit();
}

//procesa el formulario al darle enviar :)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $grado = $_POST['grado'];
    $materia = $_POST['materia'];
    $calificacion = $_POST['calificacion'];
    $fecha = $_POST['fecha'];

    //actualiza los datos del alumno :)
    $queryAlumno = "UPDATE alumnos SET nombre=$1, grado=$2, promedio=$3 WHERE id_alumno=$4";
    if (!modificar($queryAlumno, [$nombre, $grado, $calificacion, $datos['id_alumno']])) {
        echo "Error al actualizar alumno: " . pg_last_error($con);
        exit();
    }

    if ($nueva_calificacion) {
        //insertar nueva calificación :)
        $queryCalif = "INSERT INTO calificaciones(id_alumno, id_materia, calificacion, fecha) VALUES($1, $2, $3, $4)";
        if (!insertar($queryCalif, [$datos['id_alumno'], $materia, $calificacion, $fecha])) {
            echo "Error al agregar calificación: " . pg_last_error($con);
            exit();
        }
    } else {
        //actualizar calificación existente :)
        $queryCalif = "UPDATE calificaciones SET id_materia=$1, calificacion=$2, fecha=$3 WHERE id_calificacion=$4";
        if (!modificar($queryCalif, [$materia, $calificacion, $fecha, $id_calificacion])) {
            echo "Error al actualizar calificación: " . pg_last_error($con);
            exit();
        }
    }

    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $nueva_calificacion ? "Agregar Calificación" : "Editar Alumno"; ?></title>
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4"><?php echo $nueva_calificacion ? "Agregar Calificación" : "Editar Alumno"; ?></h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required value="<?php echo $datos['alumno'] ?? ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Grado</label>
            <input type="text" name="grado" class="form-control" required value="<?php echo $datos['grado'] ?? ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Materia</label>
            <select name="materia" class="form-select">
                <option value="">--Seleccionar--</option>
                <?php
                foreach ($materias as $m) {
                    $selected = ($m['id_materia'] == ($datos['id_materia'] ?? '')) ? "selected" : "";
                    echo "<option value='".$m['id_materia']."' $selected>".$m['nombre']."</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Calificación</label>
            <input type="number" step="0.01" name="calificacion" class="form-control" value="<?php echo $datos['calificacion'] ?? ''; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo $datos['fecha'] ?? date('Y-m-d'); ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $nueva_calificacion ? "Agregar" : "Actualizar"; ?></button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
