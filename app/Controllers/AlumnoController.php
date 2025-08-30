<?php

namespace App\Controllers;

use App\Models\AlumnoModel;
use CodeIgniter\Controller;
use App\Models\DetalleAlumnoCursoModel;
use App\Models\CursoModel;
use Exception;

class AlumnoController extends Controller
{
    protected $alumnoModel;

    public function __construct()
    {
        $this->alumnoModel = new AlumnoModel();
    }

    public function index()
    {
        $alumnoModel = new AlumnoModel();
        $cursoModel = new CursoModel();
        $detalleModel = new DetalleAlumnoCursoModel();

        $alumnos = $alumnoModel->findAll();

        foreach ($alumnos as &$alumno) {
            $cursosAsignados = $detalleModel->where('alumno', $alumno['alumno'])->countAllResults();
            $alumno['tiene_cursos'] = $cursosAsignados > 0;
            $alumno['total_cursos'] = $cursosAsignados;
        }

        $data['alumnos'] = $alumnos;
        $data['cursos'] = $cursoModel->findAll();
        $data['titulo'] = 'Listado de alumnos';

        return view('alumnos/index', $data);
    }

    public function create()
    {
        return view('formulario/create', [
            'title'    => 'Agregar Alumno',
            'actionUrl' => 'alumnos/store',
            'backUrl'  => 'alumnos',
            'fields'   => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text'],
                ['name' => 'apellido', 'label' => 'Apellido', 'type' => 'text'],
                ['name' => 'direccion', 'label' => 'Dirección', 'type' => 'text'],
                ['name' => 'movil', 'label' => 'Móvil', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ]
        ]);
    }


    public function store()
    {

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[alumnos.email]',
            'movil' => 'required|numeric|min_length[8]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $datos = [
            'nombre' => $this->request->getPost('nombre'),
            'apellido' => $this->request->getPost('apellido'),
            'direccion' => $this->request->getPost('direccion'),
            'movil' => $this->request->getPost('movil'),
            'email' => $this->request->getPost('email'),
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'user' => 'admin',
            'inactivo' => 0,
        ];

        if ($this->alumnoModel->save($datos)) {
            return redirect()->to('/alumnos')->with('success', 'Alumno creado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al crear el alumno');
        }
    }

    public function edit($id)
    {
        $data['alumno'] = $this->alumnoModel->find($id);
        return view('alumnos/edit', $data);
    }

    public function update($id)
    {
        $this->alumnoModel->update($id, [
            'nombre'    => $this->request->getPost('nombre'),
            'apellido'  => $this->request->getPost('apellido'),
            'direccion' => $this->request->getPost('direccion'),
            'movil'     => $this->request->getPost('movil'),
            'email'     => $this->request->getPost('email'),
            'user'      => 'admin',
            'inactivo'  => $this->request->getPost('inactivo') ?? 0,
        ]);

        return redirect()->to('/alumnos');
    }

    public function delete($id)
    {
        $this->alumnoModel->delete($id);
        return redirect()->to('/alumnos');
    }

    public function asignarCursos()
    {
        $alumno_pk = $this->request->getPost('alumno_id');
        $cursos = $this->request->getPost('cursos');

        $detalleModel = new DetalleAlumnoCursoModel();
        $detalleModel->where('alumno', $alumno_pk)->delete();

        if (!empty($cursos)) {
            foreach ($cursos as $curso_pk) {
                $detalleModel->insertDetalle([
                    'alumno' => $alumno_pk,
                    'curso' => $curso_pk
                ]);
            }
        }

        return redirect()->to('/alumnos')->with('message', 'Cursos asignados correctamente');
    }


    public function debugCursos($alumno_pk)
    {
        $detalleModel = new DetalleAlumnoCursoModel();

        echo "<h3>Debug para alumno ID: $alumno_pk</h3>";


        $detalles = $detalleModel->where('alumno', $alumno_pk)->findAll();
        echo "<h4>Registros en detalle_alumno_curso:</h4>";
        echo "<pre>" . print_r($detalles, true) . "</pre>";


        $builder = $detalleModel->builder();
        $query = $builder
            ->select('cursos.nombre, cursos.profesor, detalle_alumno_curso.alumno, detalle_alumno_curso.curso')
            ->join('cursos', 'cursos.curso = detalle_alumno_curso.curso', 'inner')
            ->where('detalle_alumno_curso.alumno', $alumno_pk)
            ->get();

        echo "<h4>Consulta con JOIN:</h4>";
        echo "<pre>" . print_r($query->getResultArray(), true) . "</pre>";


        echo "<h4>SQL Query:</h4>";
        echo "<pre>" . $detalleModel->getLastQuery() . "</pre>";


        $cursoModel = new CursoModel();
        $cursos = $cursoModel->findAll();
        echo "<h4>Todos los cursos disponibles:</h4>";
        echo "<pre>" . print_r($cursos, true) . "</pre>";
    }


    public function verCursosAsignados($alumno_pk)
    {
        try {
            $detalleModel = new DetalleAlumnoCursoModel();


            $existe = $detalleModel->where('alumno', $alumno_pk)->first();

            if (!$existe) {
                return $this->response->setJSON([]);
            }


            $cursos = $detalleModel
                ->select('cursos.nombre, cursos.profesor')
                ->join('cursos', 'cursos.curso = detalle_alumno_curso.curso', 'inner')
                ->where('detalle_alumno_curso.alumno', $alumno_pk)
                ->where('detalle_alumno_curso.inactivo', 0)
                ->findAll();


            log_message('info', 'Cursos encontrados para alumno ' . $alumno_pk . ': ' . json_encode($cursos));

            return $this->response->setJSON($cursos);
        } catch (Exception $e) {
            log_message('error', 'Error en verCursosAsignados: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error interno del servidor']);
        }
    }
}
