<?php

namespace App\Repositories;

interface DiagramRepositoryInterface
{
    public function all($request);
    public function find($id);
    public function create($request);
    public function update($id, array $data);
    public function delete($id);
}
