<?php

namespace App\Repositories;
use Illuminate\Support\Facades\Cache;
use App\Models\Campaign;
use App\Models\Campaign_kpi;

class CampaingRepository
{
    public function createCampaing(array $data)
    {
        return Campaign::create([
            'title' => $data['title'],
            'latitude' => $data['latitude'],
            'location' => $data['location'],
            'radius' => $data['radius'],
            'required_volunteers' => $data['required_volunteers'],
            'target_amount' => $data['target_amount'],
            'has_evaluation' => $data['has_evaluation'] ?? 0,
            'image' => $data['image'] ?? null,
            'video' => $data['video'] ?? null,
            'description' => $data['description'],
            'type' => $data['type'],
            'priority' => $data['priority'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'longitude' => $data['longitude'],
        ]);
    }    public function createCampaing_Kpi( array $data)
    {
        return Campaign_kpi::create($data);
    }
    public function findById($id)
    {
        return Campaign::where('id',$id)->exists();
    }

    // public function index()
    // {
    //     return Campaign::all();
    // }
//الدالة بعد ما طبقنا الكاش
public function index()
{
    return Cache::remember('all_campaigns', now()->addMinutes(10), function () {
        return Campaign::all();
    });
}

    public function indexWithRelation($id)
    {
        return Campaign::with('Campaign_kpis')->find($id);
    }

    public function indexDetail( $id)
    {
        return Campaign::with('Campaign_kpis')->find($id);
    }

    public function Search($request)
    {
        $query=Campaign::query();
        if($request->filled('title')){
            $query->where('title','like','%'.$request->title)  ;
        }
        if($request->filled('status')){
            $query->where('status','like','%'.$request->status)  ;
        }
        if($request->filled('type')){
            $query->where('type','like','%'.$request->type)  ;
        }
        return $query->get() ;
    }
    public function getById($id)
    {
        return Campaign::find($id);
    }
    public function update(array $data, $campaign)
    {
        $campaign->update($data);

        return $campaign->fresh();
    }
    public function indexForEvaulation()
    {
        return Campaign::where('has_evaluation',1)->get();

    }
}
