<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProcessStudentID implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $frontPath;
    public $backPath;

    public function __construct(User $user, $frontPath, $backPath)
    {
        $this->user = $user;
        $this->frontPath = $frontPath;
        $this->backPath = $backPath;
    }

    public function handle()
    {
        $frontFile = storage_path('app/public/'.$this->frontPath);
        $backFile = storage_path('app/public/'.$this->backPath);

        $apiUrl = env('OCR_API_URL');

        $response = Http::attach(
            'front', file_get_contents($frontFile), 'front.jpg'
        )->attach(
            'back', file_get_contents($backFile), 'back.jpg'
        )->post($apiUrl . '/ocr');

        if ($response->failed()) {
            $this->user->update([
                'ocr_status' => 'fail',
                'ocr_confidence' => 0,
            ]);
            return;
        }

        $data = $response->json();

        $frontText = $data['front_text'] ?? '';
        $backText = $data['back_text'] ?? '';
        $fullText = trim($frontText . "\n" . $backText);

        $charCount = strlen($fullText);
        $status = $charCount > 30 ? 'medium' : 'fail';
        $confidence = $status === 'medium' ? 70 : 0;

        $this->user->update([
            'ocr_status' => $status,
            'ocr_confidence' => $confidence,
            'ocr_text' => $fullText,
        ]);

        Storage::disk('public')->delete([$this->frontPath, $this->backPath]);
    }
}
