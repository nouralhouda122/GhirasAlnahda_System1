<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
namespace App\Http\Controllers;
use App\Models\Campaign;
use App\Models\Donation;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'campaign_id' => 'required|exists:campaigns,id',
        ]);

        // تسجيل التبرع كعملية معلقة Pending
        $donation = Donation::create([
            'user_id' => auth()->id(),
            'campaign_id' => $request->campaign_id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);
        $campaign = Campaign::findOrFail($donation->campaign_id);

        $campaign->current_amount += $donation->amount;

        $campaign->save();


        Stripe::setApiKey(config('services.stripe.secret'));

        // إنشاء جلسة دفع في Stripe
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'تبرع للحملة رقم: ' . $request->campaign_id,
                    ],
                    'unit_amount' => $request->amount * 100, // تحويل المبلغ للسنتات
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'donation_id' => $donation->id,
            ],
            // روابط العودة للواجهة الأمامية
            'success_url' => url('/api/donations/success?session_id={CHECKOUT_SESSION_ID}&donation_id=' . $donation->id),
            'cancel_url' => url('/api/donations/cancel?donation_id=' . $donation->id),
        ]);

        return response()->json([
            'status' => 'success',
            'data'=>$campanig,
            'checkout_url' => $checkoutSession->url // الواجهة الأمامية تفتح هذا الرابط للمستخدم
        ]);
    }

    // 2. معالجة نجاح الدفع عند عودة المستخدم
    public function paymentSuccess(Request $request)
    {
        $donation = Donation::findOrFail($request->donation_id);
        $donation->update([
            'status' => 'completed',
            'payment_id' => $request->session_id,
        ]);

        return response()->json([
            'message' => 'تم التبرع بنجاح، شكراً لمساهمتك!'
        ]);
    }
public function campaignDonations($campaignId)
{
    $campaign = Campaign::findOrFail($campaignId);

    $donations = $campaign->donations()
        ->where('status', 'completed')
        ->with('user:id,name')
        ->latest()
        ->get();

    return response()->json([
        'status' => 'success',
        'campaign' => [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'target_amount' => $campaign->target_amount,
            'current_amount' => $campaign->current_amount,
            'remaining_amount' => max(
                0,
                $campaign->target_amount - $campaign->current_amount
            ),
        ],
        'donations_count' => $donations->count(),
        'donations' => $donations->map(function ($donation) {
            return [
                'id' => $donation->id,
                'donor' => [
                    'id' => $donation->user?->id,
                    'name' => $donation->user?->name,
                ],
                'amount' => $donation->amount,
                'currency' => $donation->currency,
                'created_at' => $donation->created_at,
            ];
        }),
    ]);
}
    // 3. معالجة إلغاء الدفع
    public function paymentCancel(Request $request)
    {
        $donation = Donation::findOrFail($request->donation_id);
        $donation->update(['status' => 'failed']);

        return response()->json([
            'message' => 'تم إلغاء عملية التبرع.'
        ], 400);
    }
}
