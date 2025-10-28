<?php

include("conexion.php");

//esto es para obtener todas las materias del select :)
$materias = seleccionar("SELECT id_materia, nombre FROM materias", []);

//cuando se envie el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $grado = $_POST['grado'];
    $materia = $_POST['materia'];
    $calificacion = $_POST['calificacion'];
    $fecha = $_POST['fecha'];

    //valida la calificacion para promedio
    $promedio = is_numeric($calificacion) ? $calificacion : 0;

//insertar alumno:)
    $queryAlumno = "INSERT INTO alumnos(nombre, grado, promedio) VALUES($1, $2, $3) RETURNING id_alumno";
    $resultadoAlumno = insertar($queryAlumno, [$nombre, $grado, $promedio]);

    $id_alumno = null;
    if ($resultadoAlumno) {
        $fila = pg_fetch_assoc($resultadoAlumno); // obtener la primera fila
        $id_alumno = $fila['id_alumno'] ?? null;
    }

//insertas la calificacion cuando seleccionas materia :)
if ($id_alumno && !empty($materia) && !empty($calificacion)) {
        $queryCalif = "INSERT INTO calificaciones(id_alumno, id_materia, calificacion, fecha) VALUES($1, $2, $3, $4)";
        insertar($queryCalif, [$id_alumno, $materia, $calificacion, $fecha]);
    }

    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Alumno</title>

    <link rel="stylesheet" href="public/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Agregar Alumno</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Grado</label>
            <input type="text" name="grado" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Materia</label>
            <select name="materia" class="form-select">
                <option value="">--Seleccionar--</option>
                <?php
                if ($materias && count($materias) > 0) {
                    foreach ($materias as $m) {
                        echo "<option value='".$m['id_materia']."'>".$m['nombre']."</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Calificación</label>
            <input type="number" step="0.01" name="calificacion" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
