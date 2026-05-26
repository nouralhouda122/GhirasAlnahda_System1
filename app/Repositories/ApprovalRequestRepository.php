<?php


namespace App\Repositories;


use App\Models\ApprovalRequest;

class ApprovalRequestRepository
{
    public function create(array $data)
    {
        return ApprovalRequest::create($data);
    }

    public function index()
    {
       return   ApprovalRequest::all();
    }

    public function find($id)
    {
        return   ApprovalRequest::find($id);

    }

    public function update(array $data, $model)
    {
        return $model->update($data);
    }

}
