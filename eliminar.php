<?php

include("conexion.php");

//obtener el id de la calificación :)
$id_calificacion = $_GET['id'] ?? null;

if ($id_calificacion) {
    //esto elimina la calificacion directamente :)
    $query = "DELETE FROM calificaciones WHERE id_calificacion = $1";
    $resultado = eliminar($query, [$id_calificacion]);

    if (!$resultado) {
        echo "error al eliminar: " . pg_last_error($con);
        exit();
    }
}

//redirige al index :)
header("Location: index.php");
exit();

?>