<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

class ContactTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Devuelve todos los contactos ordenados por fecha descendente.
     */
    public function fetchAll(): ResultSet
    {
        return $this->tableGateway->select(function ($select) {
            $select->order('fecha DESC');
        });
    }

    /**
     * Busca un contacto por id.
     */
    public function getContact(int $id): ?Contact
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();

        return $row ?: null;
    }

    /**
     * Guarda un contacto nuevo y devuelve el id generado.
     */
    public function saveContact(Contact $contact): int
    {
        $data = [
            'nombre'  => $contact->nombre,
            'email'   => $contact->email,
            'mensaje' => $contact->mensaje,
        ];

        // No insertamos 'id' ni 'fecha': los genera MySQL.
        $this->tableGateway->insert($data);

        return (int) $this->tableGateway->getLastInsertValue();
    }

    /**
     * Elimina un contacto por id.
     */
    public function deleteContact(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}