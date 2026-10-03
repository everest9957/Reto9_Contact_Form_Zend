<?php

declare(strict_types=1);

namespace Application\Model;

class Contact
{
    public ?int $id = null;
    public string $nombre = '';
    public string $email = '';
    public string $mensaje = '';
    public ?string $fecha = null;

    /**
     * Carga datos desde un array (por ejemplo, del formulario o la BD)
     * en las propiedades del objeto.
     */
    public function exchangeArray(array $data): void
    {
        $this->id      = ! empty($data['id'])      ? (int) $data['id'] : null;
        $this->nombre  = $data['nombre']  ?? '';
        $this->email   = $data['email']   ?? '';
        $this->mensaje = $data['mensaje'] ?? '';
        $this->fecha   = $data['fecha']   ?? null;
    }

    /**
     * Devuelve los datos como array (útil para TableGateway).
     */
    public function getArrayCopy(): array
    {
        return [
            'id'      => $this->id,
            'nombre'  => $this->nombre,
            'email'   => $this->email,
            'mensaje' => $this->mensaje,
            'fecha'   => $this->fecha,
        ];
    }
}