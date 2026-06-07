<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Services\ComplaintService;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComplaintController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    /**
     * 1. GET /api/complaints/meta-data
     */
    public function metaData(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => Complaint::getSensitivityMetaData()
        ], 200);
    }

    /**
     * 2. GET /api/complaints (عرض الشكاوى المفلترة للمدراء والمستخدمين)
     */
    public function index(): JsonResponse
    {
        $complaints = $this->complaintService->getAllComplaints();
        return response()->json([
            'status' => true,
            'data'   => $complaints
        ], 200);
    }

    /**
     * 3. POST /api/complaints (إضافة شكوى من قبل المتطوع/المستخدم)
     */
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        $complaint = $this->complaintService->storeComplaint(
            $request->validated(),
            $request->hasFile('attachment'),
            $request->file('attachment')
        );

        return response()->json([
            'status'  => true,
            'message' => 'Your complaint has been submitted successfully.',
            'data'    => $complaint
        ], 201);
    }

    /**
     * 4. PUT/PATCH /api/complaints/{id}/review (الـ API الناقص: رد المسؤول وتغيير الحالة)
     */
    public function review(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status'      => 'required|in:in_progress,resolved,rejected',
            'admin_reply' => 'string|min:5'
        ]);

        try {
            $updatedComplaint = $this->complaintService->processReview($id, $request->all());
            return response()->json([
                'status'  => true,
                'message' => 'Complaint updated and processed successfully.',
                'data'    => $updatedComplaint
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }


    public function show(int $id): JsonResponse
{
    $complaint = $this->complaintService->getComplaintById($id);

    return response()->json([
        'status' => true,
        'data' => $complaint
    ], 200);
}

public function filter(Request $request): JsonResponse
{
    $complaints = $this->complaintService->filterComplaints(
        $request->status,
        $request->sensitivity ?? $request->sensitivity_level
    );

    return response()->json([
        'status' => true,
        'data' => $complaints
    ], 200);
}
}
