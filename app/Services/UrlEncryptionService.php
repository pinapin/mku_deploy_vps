<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class UrlEncryptionService
{
    /**
     * Encrypt ID for safe URL usage
     */
    public static function encryptId($id)
    {
        try {
            // Add timestamp and random salt for uniqueness
            $payload = [
                'id' => $id,
                'timestamp' => now()->timestamp,
                'salt' => Str::random(8)
            ];

            $encrypted = Crypt::encrypt($payload);

            // Make URL safe
            return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
        } catch (\Exception $e) {
            // Fallback to simple encoding if encryption fails
            return base64_encode('id:' . $id . ':' . now()->timestamp);
        }
    }

    /**
     * Decrypt ID from URL parameter
     */
    public static function decryptId($encryptedId)
    {
        try {
            // Make URL safe string back to normal
            $base64 = strtr($encryptedId, '-_', '+/');
            $padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);

            $decrypted = Crypt::decrypt(base64_decode($padded));

            // Validate structure and return ID
            if (is_array($decrypted) && isset($decrypted['id'])) {
                // Check if not too old (optional: 24 hours)
                if (isset($decrypted['timestamp']) && (now()->timestamp - $decrypted['timestamp']) < 86400) {
                    return $decrypted['id'];
                }
            }

            return null;
        } catch (\Exception $e) {
            // Fallback for simple encoding
            try {
                $decoded = base64_decode($encryptedId);
                if (strpos($decoded, 'id:') === 0) {
                    $parts = explode(':', $decoded);
                    if (count($parts) >= 2) {
                        return (int) $parts[1];
                    }
                }
            } catch (\Exception $fallbackEx) {
                // All decryption failed
            }

            return null;
        }
    }

    /**
     * Generate short encrypted token for URLs
     */
    public static function generateToken($id)
    {
        $data = $id . '|' . now()->timestamp . '|' . Str::random(16);
        $encrypted = Crypt::encrypt($data);

        // Create shorter, URL-safe token
        return substr(rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '='), 0, 32);
    }

    /**
     * Decrypt token back to ID
     */
    public static function decryptToken($token)
    {
        try {
            // Pad the base64 string if needed
            $base64 = strtr($token, '-_', '+/');
            $padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);

            $decrypted = Crypt::decrypt(base64_decode($padded));
            $parts = explode('|', $decrypted);

            if (count($parts) >= 3) {
                // Check timestamp (not too old - 24 hours)
                $timestamp = (int) $parts[1];
                if ((now()->timestamp - $timestamp) < 86400) {
                    return (int) $parts[0];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create secure hash for verification
     */
    public static function createHash($id, $salt = null)
    {
        if (!$salt) {
            $salt = config('app.key');
        }

        return hash_hmac('sha256', $id . $salt . now()->format('Y-m-d'), $salt);
    }

    /**
     * Verify hash
     */
    public static function verifyHash($id, $hash, $salt = null)
    {
        if (!$salt) {
            $salt = config('app.key');
        }

        $expected = hash_hmac('sha256', $id . $salt . now()->format('Y-m-d'), $salt);
        return hash_equals($expected, $hash);
    }
}