<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $filename
 * @property int $size In bytes
 * @property string $ip_address
 * @property bool $deleted
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int|null $session_id
 */
class Upload extends Model
{
    use HasFactory;
    use HasTimestamps;
    use HasVersion7Uuids;

    public function getFilePath(): string
    {
        $directory = trim(config('upload.directory'), '/');

        if ($this->id === null) {
            $this->id = $this->newUniqueId();
        }

        return "/$directory/$this->id";
    }

    public function getEstimatedExpireFromNow(): ?CarbonInterval
    {
        if ($this->created_at->addHour()->isFuture()) {
            return $this->created_at->addHour()->diff(Carbon::now());
        }

        return null;
    }
}
