<?php

namespace App\Models;

use Database\Factories\WorldChatMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorldChatMessage extends Model
{
    /** @use HasFactory<WorldChatMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'noi_dung',
    ];

    /**
     * Người gửi tin nhắn.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Định dạng tin nhắn gửi về frontend.
     */
    public function formatForUser(?int $currentUserId = null): array
    {
        $user = $this->user;
        $isMe = ($currentUserId !== null && $this->user_id === $currentUserId);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'author' => $user ? ($user->ho_ten ?? 'Gymer') : 'Thành viên',
            'avatar' => $user ? $user->initials : 'G',
            'avatar_url' => $user ? $user->avatar_url : null,
            'text' => $this->noi_dung,
            'time' => $this->created_at ? $this->created_at->format('H:i') : now()->format('H:i'),
            'date_str' => $this->created_at ? $this->created_at->format('d/m/Y') : '',
            'is_me' => $isMe,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
