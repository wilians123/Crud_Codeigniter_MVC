<?php

namespace App\Controllers;

use App\Models\CursoModel;
use CodeIgniter\Controller;

class CursoController extends Controller
{
    protected $cursoModel;

    public function __construct()
    {
        $this->cursoModel = new CursoModel();
    }

    public function index()
    {
        $data['cursos'] = $this->cursoModel->findAll();
        $data['titulo'] = "Listado de cursos";
        return view('cursos/index', $data);
    }

    public function create()
    {
        return view('formulario/create', [
            'title'    => 'Agregar Curso',
            'actionUrl' => 'cursos/store',
            'backUrl'  => 'cursos',
            'fields'   => [
                ['name' => 'nombre', 'label' => 'Nombre del Curso', 'type' => 'text'],
                ['name' => 'profesor', 'label' => 'Profesor', 'type' => 'text'],
            ]
        ]);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|min_length[3]|max_length[100]|is_unique[cursos.nombre]',
            'profesor' => 'required|min_length[3]|max_length[100]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $datos = [
            'nombre' => $this->request->getPost('nombre'),
            'profesor' => $this->request->getPost('profesor'),
            'inactivo' => 0,
        ];

        if ($this->cursoModel->save($datos)) {
            return redirect()->to('/cursos')->with('success', 'Curso creado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al crear el curso');
        }
    }

    public function edit($id)
    {
        $data['curso'] = $this->cursoModel->find($id);
        return view('cursos/edit', $data);
    }

    public function update($id)
    {
        $this->cursoModel->update($id, [
            'nombre'   => $this->request->getPost('nombre'),
            'profesor' => $this->request->getPost('profesor'),
            'inactivo' => $this->request->getPost('inactivo') ?? 0,
        ]);

        return redirect()->to('/cursos');
    }

    public function delete($id)
    {
        $this->cursoModel->delete($id);
        return redirect()->to('/cursos');
    }
}
