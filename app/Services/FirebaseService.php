<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use PHPUnit\Util\Test;

class FirebaseService
{
  protected string $projectId;
  protected string $credentialsPath;

  public function __construct()
  {
    $this->projectId = config('services.fcm.project_id') ?? 'N/A';
    
    $this->credentialsPath = base_path(config('services.fcm.credentials'));

    if (!file_exists($this->credentialsPath)) {
      throw new \RuntimeException("Firebase credentials file not found at {$this->credentialsPath}");
    }
  }

  /**
   * Get an OAuth2 access token using the service account JSON
   */
  protected function getAccessToken(): string
  {
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging']; // required scope :contentReference[oaicite:2]{index=2}

    $creds = new ServiceAccountCredentials($scopes, $this->credentialsPath);

    $token = $creds->fetchAuthToken();

    if (!isset($token['access_token'])) {
      throw new \RuntimeException('Unable to fetch access token for FCM.');
    }

    return $token['access_token'];
  }

  /**
   * Send a notification to a single device token
   */
  public function sendToDevice(string $token, string $title, string $body, array $data = []): array
  {
    // Check if user has notifications enabled
    $user = User::where('fcm_token', $token)->first();
    if ($user && !$user->enable_notification) {
        return [];
    }

    $accessToken = $this->getAccessToken();

    $url = sprintf(
      'https://fcm.googleapis.com/v1/projects/%s/messages:send',
      $this->projectId
    );

    $payload = [
      'message' => [
        'token' => $token,
 
        'android' => [
          'priority' => 'high',
          // Android draws this one itself from the system channel, so the
          // alert still appears when the app is killed and no JS handler
          // gets to run. 'default' is the Notifee channel the app creates
          // (HIGH importance). If it is missing FCM falls back to a
          // low-importance channel, so the alert still shows but arrives
          // silently.
          'notification' => [
            'channel_id' => 'default',
          ],
        ],
        // Shown in system tray (notification message)
        'notification' => [
          'title' => $title,
          'body' => $body,
        ],

        // Custom key/value pairs (data message)
        'data' => (object)$data,
      ],
    ];

    $response = Http::withToken($accessToken)   // Authorization: Bearer <token>
      ->acceptJson()
      ->post($url, $payload);

    if ($response->failed()) {
      \Log::error('FCM v1 sendToDevice failed', [
        'status' => $response->status(),
        'body' => $response->body(),
      ]);
    }

    // Successful response looks like: { "name": "projects/xxx/messages/0:123456..." } :contentReference[oaicite:3]{index=3}
    return $response->json() ?? [];
  }

}