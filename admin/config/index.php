<?php
include '../../conexion.php';

// Eliminar proxy
if (isset($_GET['eliminar_id'])) {
    $id_eliminar = $_GET['eliminar_id'];

    // Obtener la información del proxy antes de eliminarlo
    $sentencia_info = $conexion->prepare("SELECT * FROM config WHERE id = ?");
    $sentencia_info->execute([$id_eliminar]);
    $config_info = $sentencia_info->get_result()->fetch_assoc();

    // Eliminar el proxy
    $sentencia_eliminar = $conexion->prepare("DELETE FROM config WHERE id = ?");
    $resultado_eliminar = $sentencia_eliminar->execute([$id_eliminar]);

    if ($resultado_eliminar) {
        // Eliminar la imagen asociada si existe
        if (!empty($config_info['image']) && file_exists($config_info['image'])) {
            unlink($config_info['image']);
        }

        header("Location: index.php");
        exit();
    } else {
        echo "Error al eliminar el proxy: " . mysqli_error($conexion);
    }
}

$sentencia = $conexion->prepare("SELECT * FROM config");
$sentencia->execute();
$config = $sentencia->get_result()->fetch_all(MYSQLI_ASSOC);

include '../../templates/header.php';
?>
<link rel="stylesheet" href="../../css/index.css">

<div class="card animate__animated animate__fadeIn">
    <div class="card-header d-flex">
        <h3 style="margin-left: auto">Configs</h3>
        <a type="button" class="btn btn-outline-light ml-auto" href="crear.php">+</a>
    </div>

    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Imagen</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($config as $registro) { ?>
                        <tr>
                            <td><?php echo $registro['id'] ?></td>
                            <td><?php echo $registro['name'] ?></td>
                            <td>
                                <?php
                                // Muestra la imagen si la ruta está disponible
                                if (!empty($registro['image']) && file_exists($registro['image'])) {
                                    echo '<img src="' . $registro['image'] . '" alt="Imagen" style="max-width: 100px; max-height: 100px;">';
                                } else {
                                    echo 'Imagen no disponible';
                                }
                                ?>
                            </td>
                            <td><?php echo $registro['description'] ?></td>
                            <td><?php echo $registro['price'] ?></td>
                            <td>
                                <a type="button" class="btn btn-outline-warning" href="editar.php?txtid=<?php echo $registro['id']; ?>">Editar</a>
                                <a type="button" class="btn btn-outline-danger" href="index.php?eliminar_id=<?php echo $registro['id']; ?>" onclick="return ConfirmDelete();">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>

<script type="text/javascript">
    function ConfirmDelete() {
        var respuesta = confirm("¿Estás seguro que deseas eliminar el item?");
        return respuesta;
    }
</script>
