<?php

declare(strict_types=1);

namespace Engelsystem\Models;

use Engelsystem\Models\User\User;
use Engelsystem\Models\User\Settings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                         $id
 * @property string                      $name
 * @property string                      $description
 * @property string                      $email
 * @property string                      $phone
 * @property string                      $contact_person
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 *
 * @property-read Collection|User[]      $user
 * @property-read Collection|Settings[]  $settings
 *
 * @method static Builder|Organization whereId($value)
 * @method static Builder|Organization whereName($value)
 * @method static Builder|Organization whereDescription($value)
 * @method static Builder|Organization whereEmail($value)
 * @method static Builder|Organization wherePhone($value)
 */
class Organization extends BaseModel
{
    #use HasFactory;

    /** @var bool Enable timestamps */
    public $timestamps = true; // phpcs:ignore

    /** @var string[] */
    protected $fillable = [ // phpcs:ignore
        'name',
        'description',
        'email',
        'phone',
        'contact_person',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Settings::class);
    }
}
