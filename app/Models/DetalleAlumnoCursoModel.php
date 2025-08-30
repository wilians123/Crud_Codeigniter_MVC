<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleAlumnoCursoModel extends Model
{
    protected $table      = 'detalle_alumno_curso';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'alumno',
        'curso',
        'fecha_asignacion',
        'user',
        'inactivo'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'fecha_asignacion';

    public function insertDetalle($data)
    {
        $data['fecha_asignacion'] = date('Y-m-d H:i:s');
        $data['user'] = 'admin';
        $data['inactivo'] = 0;

        return $this->insert($data);
    }
}
