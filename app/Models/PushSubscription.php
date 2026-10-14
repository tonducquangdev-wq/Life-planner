<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'push_subscriptions';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'endpoint',
        'endpoint_hash',
        'public_key',
        'auth_token',
        'content_encoding',
        'device_name',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tự động tạo endpoint_hash nếu chưa có trước khi lưu.
     */
    protected static function booted(): void
    {
        static::saving(function (self $subscription): void {
            if ($subscription->endpoint && empty($subscription->endpoint_hash)) {
                $subscription->endpoint_hash = self::hashEndpoint($subscription->endpoint);
            }
        });
    }

    /**
     * Tính mã băm SHA256 cho endpoint để index và tìm kiếm nhanh.
     */
    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }
}
