<?php

namespace App\Services;

use App\Http\Resources\ApprovalRequestDetailResource;
use App\Http\Resources\ApprovalRequestResource;
use App\Repositories\ApprovalRequestRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ApprovalRequestService
{
    protected $approvalRequestRepository;

    public function __construct(ApprovalRequestRepository $approvalRequestRepository)
    {
        $this->approvalRequestRepository = $approvalRequestRepository;
    }

    public function show()
    {
        $requests = $this->approvalRequestRepository->index();

        return [
            'user' => ApprovalRequestResource::collection($requests),
            'message' => 'Approval requests retrieved successfully',
            'code' => 200
        ];
    }

    public function indexDetail($id)
    {
        $request = $this->approvalRequestRepository->find($id);

        if (!$request) {

        return [
            'user' => null,
            'message' => 'Approval request not found',
            'code' => 404
        ];}
            return [
                'user' => new ApprovalRequestDetailResource($request),
                'message' => 'Approval request retrieved successfully',
                'code' => 200
            ];
        }



    public function updateStatus($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {

            $data = $this->approvalRequestRepository->find($id);

            if (!$data) {
                return [
                    'user' => null,
                    'message' => 'Approval request not found',
                    'code' => 404
                ];
            }

            // 🔥 تحقق مباشر من الحالة الحالية
            if ($data->status === 'approved' || $data->status === 'rejected') {
                return [
                    'user' => '',
                    'message' => 'This request has already been processed',
                    'code' => 400
                ];
            }

            if (!in_array($request->status, ['approved', 'rejected'])) {
                return [
                    'user' => null,
                    'message' => 'Invalid status',
                    'code' => 422
                ];
            }

            $this->approvalRequestRepository->update([
                'status' => $request->status,
                'notes' => $request->notes,
            ], $data);

            $data = $data->fresh();

            if ($data->approvable) {
                $data->approvable->update([
                    'status' => $request->status
                ]);
            }








            return [
                'user' => new ApprovalRequestResource($data),
                'message' => 'Status updated successfully',
                'code' => 200
            ];
        });
    }    }
