<?php

namespace App\Jobs;

use App\Services\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AnalyzeKPIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $text) {}

    public function handle(OllamaService $ollama)
    {
        $result = $ollama->analyze($this->text);

        DB::table('kpi_results')->insert([
            'result' => json_encode($result),
            'created_at' => now()
        ]);
    }
}
