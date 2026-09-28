<?php

namespace App\Services\Ai;

use Anthropic\Bedrock\MantleClient;
use Anthropic\Core\Exceptions\AnthropicException;

/**
 * Claude on Amazon Bedrock, through the official Anthropic PHP SDK's Bedrock
 * (Mantle) client. Used for the manager daily digest and the "Why these?"
 * explanation on product recommendations.
 *
 * Settings live in config/ife.php (bedrock.*). isConfigured() is false when
 * no Bedrock credentials are set; callers then use their template fallback
 * rather than calling out, so the app works without AWS.
 */
class ClaudeClient
{
    public function isConfigured(): bool
    {
        $config = config('ife.bedrock', []);

        if (!($config['enabled'] ?? false)) {
            return false;
        }

        return !empty($config['api_key']) || (!empty($config['key']) && !empty($config['secret']));
    }

    public function model(): string
    {
        return (string) config('ife.bedrock.model', 'anthropic.claude-opus-5');
    }

    /**
     * One request, one text answer.
     *
     * @return array{text: string, input_tokens: int, output_tokens: int, model: string}
     * @throws \RuntimeException when Bedrock is not configured, the call fails,
     *                           Claude declines, or no text comes back
     */
    public function complete(string $system, string $prompt): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Bedrock is not configured.');
        }

        $config = config('ife.bedrock');
        $apiKey = $config['api_key'] ?: null;

        try {
            $client = new MantleClient(
                apiKey             : $apiKey,
                awsAccessKey       : $apiKey ? null : ($config['key'] ?: null),
                awsSecretAccessKey : $apiKey ? null : ($config['secret'] ?: null),
                awsSessionToken    : $apiKey ? null : ($config['session'] ?: null),
                awsRegion          : $config['region'] ?: 'us-east-1',
            );

            $message = $client->messages->create(
                maxTokens : (int) ($config['max_tokens'] ?? 16000),
                messages  : [['role' => 'user', 'content' => $prompt]],
                model     : $this->model(),
                system    : $system,
            );
        } catch (AnthropicException $e) {
            throw new \RuntimeException('Bedrock call failed: ' . $e->getMessage(), 0, $e);
        }

        if ($message->stopReason === 'refusal') {
            throw new \RuntimeException('Claude declined the request.');
        }

        $text = '';
        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $text .= $block->text;
            }
        }

        if (trim($text) === '') {
            throw new \RuntimeException('Claude returned no text (stop reason: ' . ($message->stopReason ?? 'unknown') . ').');
        }

        return [
            'text'          => trim($text),
            'input_tokens'  => (int) $message->usage->inputTokens,
            'output_tokens' => (int) $message->usage->outputTokens,
            'model'         => $this->model(),
        ];
    }
}
