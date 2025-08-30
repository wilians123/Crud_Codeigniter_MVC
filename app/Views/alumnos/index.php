<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?? 'Alumnos' ?></title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" />
    <style>
        .curso-asignado {
            color: #4caf50 !important;
        }

        .sin-curso {
            color: #ff9800 !important;
        }

        .badge-cursos {
            background-color: #2196f3;
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: 5px;
        }
    </style>
</head>

<body>

    <?= view('layouts/navbar') ?>

    <div class="container">
        <h4><?= $titulo ?? 'Listado de alumnos' ?></h4>


        <a href="<?= site_url('alumnos/create') ?>" class="btn waves-effect waves-light teal">
            <i class="material-icons left">add</i>Nuevo Alumno
        </a>

        <table class="striped highlight responsive-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Móvil</th>
                    <th>Email</th>
                    <th>Cursos</th>
                    <th>Inactivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                    <tr>
                        <td><?= $alumno['alumno'] ?></td>
                        <td><?= $alumno['nombre'] ?></td>
                        <td><?= $alumno['apellido'] ?></td>
                        <td><?= $alumno['direccion'] ?></td>
                        <td><?= $alumno['movil'] ?></td>
                        <td><?= $alumno['email'] ?></td>
                        <td>
                            <i class="material-icons <?= $alumno['tiene_cursos'] ? 'curso-asignado' : 'sin-curso' ?>">
                                <?= $alumno['tiene_cursos'] ? 'assignment_turned_in' : 'assignment_late' ?>
                            </i>
                            <?php if ($alumno['tiene_cursos']): ?>
                                <span class="badge-cursos"><?= $alumno['total_cursos'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $alumno['inactivo'] ? 'Sí' : 'No' ?></td>
                        <td>

                            <a href="<?= site_url('alumnos/edit/' . $alumno['alumno']) ?>" class="btn-small blue tooltipped" data-tooltip="Editar alumno">
                                <i class="material-icons">edit</i>
                            </a>


                            <a href="<?= site_url('alumnos/delete/' . $alumno['alumno']) ?>"
                                class="btn-small red tooltipped"
                                data-tooltip="Eliminar alumno"
                                onclick="return confirm('¿Seguro que deseas eliminar este alumno?')">
                                <i class="material-icons">delete</i>
                            </a>


                            <a href="#modalAsignarCursos"
                                class="btn-small <?= $alumno['tiene_cursos'] ? 'orange' : 'teal' ?> modal-trigger asignar-curso-btn tooltipped"
                                data-tooltip="<?= $alumno['tiene_cursos'] ? 'Modificar cursos asignados' : 'Asignar cursos' ?>"
                                data-id="<?= $alumno['alumno'] ?>">
                                <i class="material-icons">playlist_add</i>
                            </a>


                            <?php if ($alumno['tiene_cursos']): ?>
                                <a href="#modalVerCursos"
                                    class="btn-small green modal-trigger ver-cursos-btn tooltipped"
                                    data-tooltip="Ver cursos asignados"
                                    data-id="<?= $alumno['alumno'] ?>">
                                    <i class="material-icons">visibility</i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <div id="modalAsignarCursos" class="modal">
        <div class="modal-content">
            <h5>Asignar cursos al alumno</h5>
            <form id="formAsignarCursos" method="post" action="<?= site_url('alumnos/asignarCursos') ?>">
                <input type="hidden" name="alumno_id" id="alumno_id">
                <div class="input-field">
                    <select multiple name="cursos[]" id="cursos_select">
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= $curso['curso'] ?>"><?= $curso['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Selecciona uno o varios cursos</label>
                </div>
                <button type="submit" class="btn teal">Asignar</button>
                <a href="#!" class="modal-close btn-flat">Cancelar</a>
            </form>
        </div>
    </div>


    <div id="modalVerCursos" class="modal">
        <div class="modal-content">
            <h5>Cursos asignados</h5>
            <ul id="listaCursosAsignados" class="collection"></ul>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close btn-flat">Cerrar</a>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const baseUrl = '<?= base_url() ?>';


            var elems = document.querySelectorAll('.modal');
            M.Modal.init(elems);


            var selectElems = document.querySelectorAll('select');
            M.FormSelect.init(selectElems);


            var tooltipElems = document.querySelectorAll('.tooltipped');
            M.Tooltip.init(tooltipElems);


            document.querySelectorAll('.asignar-curso-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let alumnoId = this.dataset.id;
                    document.getElementById('alumno_id').value = alumnoId;


                    const select = document.getElementById('cursos_select');
                    Array.from(select.options).forEach(option => option.selected = false);
                    M.FormSelect.init(document.querySelectorAll('select'));
                });
            });


            document.querySelectorAll('.ver-cursos-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let alumnoId = this.dataset.id;


                    let lista = document.getElementById('listaCursosAsignados');
                    lista.innerHTML = '<li class="collection-item">Cargando...</li>';


                    const url = `${baseUrl}alumnos/verCursosAsignados/${alumnoId}`;
                    console.log('Fetching URL:', url);

                    fetch(url)
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response URL:', response.url);

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }


                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('La respuesta no es JSON válido');
                            }

                            return response.json();
                        })
                        .then(data => {
                            console.log('Data received:', data);
                            lista.innerHTML = '';

                            if (data.error) {
                                lista.innerHTML = `<li class="collection-item red-text">${data.error}</li>`;
                            } else if (Array.isArray(data) && data.length > 0) {
                                data.forEach(curso => {
                                    let li = document.createElement('li');
                                    li.className = 'collection-item';
                                    li.innerHTML = `
                                    <div>
                                        <i class="material-icons left">book</i>
                                        <strong>${curso.nombre}</strong>
                                        ${curso.profesor ? '<br><span class="grey-text">Profesor: ' + curso.profesor + '</span>' : ''}
                                    </div>
                                `;
                                    lista.appendChild(li);
                                });
                            } else {
                                lista.innerHTML = '<li class="collection-item grey-text">No tiene cursos asignados</li>';
                            }
                        })
                        .catch(error => {
                            console.error('Error completo:', error);
                            lista.innerHTML = `<li class="collection-item red-text">Error: ${error.message}</li>`;
                        });
                });
            });
        });
    </script>
</body>

</html>