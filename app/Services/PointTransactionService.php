<?php
namespace App\Services;
use App\Http\Requests\AddPointTransactionRequest;
use App\Http\Resources\PointTransactionResources;
use App\Repositories\PointTransactionRepository;
use App\Repositories\userRepository;
class PointTransactionService
{
    protected $userRepository;
    public function __construct(userRepository $userRepository,PointTransactionRepository $pointTransactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->pointTransactionRepository=$pointTransactionRepository;
    }
    public function index($user)
    {
        $PointTransaction = $user->receivedPoints()
            ->with(['volunteer','campaign','awardedBy'])
            ->get();

        if ($PointTransaction->isEmpty()) {
            return [
                'user' => [],
                'message' => 'No point transactions found',
                'code' => 200
            ];
        }

        return [
            'user' => PointTransactionResources::collection($PointTransaction),
            'message' => 'success',
            'code' => 200
        ];
    }
    public function show($user_id)
    {
        $user = $this->userRepository->getById($user_id);

        if (!$user) {
            return [
                'user' => '',
                'message' => 'User not found',
                'code' => 404
            ];
        }
        $PointTransaction = $user->receivedPoints()
            ->with(['volunteer','campaign','awardedBy'])
            ->get();

        if ($PointTransaction->isEmpty()) {
            return [
                'user' => [],
                'message' => 'No point transactions for this user',
                'code' => 200
            ];
        }
        return [
            'user' => PointTransactionResources::collection($PointTransaction),
            'message' => 'success',
            'code' => 200
        ];
    }

    public function addPonus(AddPointTransactionRequest $request)
    {
        $pointTranscation=$this->pointTransactionRepository->create($request->toArray());

        return [
            'user' => $pointTranscation,
            'message' => 'success',
            'code' => 200
        ];

    }
}
