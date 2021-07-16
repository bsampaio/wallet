<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class DocumentService extends Resource {

    public function __construct($resourceToken, array $args = [])
    {
        parent::__construct($args, $resourceToken);
    }

    public function endpoint(): string
    {
        return 'documents';
    }

    public function inspect($id)
    {
        return $this->getById($id);
    }

    public function list()
    {
        return $this->get();
    }
}
