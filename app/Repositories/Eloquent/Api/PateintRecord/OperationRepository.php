<?php

namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\Operation;
use App\Repositories\Api\PateintRecord\OperationRepositoryInterface;

class OperationRepository implements OperationRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
     public function create(array $data) {
        return Operation::create($data);
    }
}
