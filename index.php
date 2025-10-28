<?php

include("conexion.php"); 

$alumnos = seleccionar("
    SELECT a.id_alumno, a.nombre AS alumno, a.grado, a.promedio,
           c.id_calificacion, c.id_materia, c.calificacion, c.fecha, m.nombre AS materia
    FROM alumnos a
    LEFT JOIN calificaciones c ON a.id_alumno = c.id_alumno
    LEFT JOIN materias m ON c.id_materia = m.id_materia
    ORDER BY a.id_alumno
", []);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema_alumnos</title>

  <link rel="stylesheet" href="public/css/bootstrap.min.css"> 
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Listado de Alumnos y Calificaciones</h1>

    <a href="insertar.php" class="btn btn-success mb-3">Agregar Alumno</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Alumno</th>
                <th>Grado</th>
                <th>Materia</th>
                <th>Calificación</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php

            if ($alumnos && count($alumnos) > 0) {
                foreach ($alumnos as $fila) {
                    echo "<tr>";
                    echo "<td>".$fila['alumno']."</td>";
                    echo "<td>".$fila['grado']."</td>";
                    echo "<td>".$fila['materia']."</td>";
                    echo "<td>".$fila['calificacion']."</td>";
                    echo "<td>".$fila['fecha']."</td>";

                    echo "<td>";
                    if (!empty($fila['id_calificacion'])) {
                        //editar y elimar calificacion ya existente :)
                        echo "<a href='editar.php?id=".$fila['id_calificacion']."' class='btn btn-primary btn-sm me-1'>Editar</a>";
                        echo "<a href='eliminar.php?id=".$fila['id_calificacion']."' class='btn btn-danger btn-sm'>Eliminar</a>";
                    } else {
                        //boton para agregar calificacion de alumno existente :)
                        echo "<a href='editar.php?id_alumno=".$fila['id_alumno']."' class='btn btn-primary btn-sm'>Agregar Calificacion</a>";
                    }
                 echo "</td>";

                 echo "</tr>";

                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No hay registros</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
