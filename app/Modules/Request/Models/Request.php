<?php

namespace App\Modules\Request\Models;

use App\Modules\User\Models\User;
use Database\Factories\RequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Request extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return RequestFactory::new();
    }

    public const STATUS_NEW = 'new';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELED = 'canceled';

    protected $table = 'requests';

    protected $fillable = [
        'client_name',
        'phone',
        'address',
        'problem_text',
        'status',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function canBeTaken(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function canBeCanceled(): bool
    {
        return !in_array($this->status, [self::STATUS_DONE, self::STATUS_CANCELED], true);
    }
}
