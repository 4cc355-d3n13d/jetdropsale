<?php

namespace App\Models;

use App\Enums\InvoiceStatusType;
use App\Exceptions\BalanceOperationException;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductTag;
use App\Models\Shopify\Shop;
use App\Models\User\Setting;
use App\Models\User\Traits\Role;
use App\Models\User\UserSource;
use App\SuperClass\AuthenticatableModel;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Contracts\User as SocialUser;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use ScoutElastic\Searchable;

/**
 * App\Models\User
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property float $balance
 * @property string|null $remember_token
 * @property float $credit_limit
 * @property string|null $billing_reference Customer reference for payment gateways
 * @property string $notes
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Card[] $cards
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProduct[] $myProducts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User\UserRole[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User\Setting[]                $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User\UserSource[]             $sources
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shopify\Shop[]                $shops
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductTag[]        $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product\MyProductCollection[] $collections
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserBalanceHistory[]          $balanceHistory
 * @mixin \Eloquent
 */
class User extends AuthenticatableModel implements AuditableInterface, NeedsAuditWhenCreated
{
    use AuditableTrait,
        Role,
        Searchable;

    protected $indexConfigurator = UserIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'id'                => ['type' => 'keyword'],
            'name'              => ['type' => 'keyword'],
            'email'             => ['type' => 'keyword'],
            'password'          => ['type' => 'keyword'],
            'remember_token'    => ['type' => 'keyword'],
            'created_at'        => ['type' => 'keyword'],
            'updated_at'        => ['type' => 'keyword'],
            'deleted_at'        => ['type' => 'keyword'],
            'credit_limit'      => ['type' => 'keyword'],
            'billing_reference' => ['type' => 'keyword'],
            'notes'             => ['type' => 'text', 'analyzer' => 'my_analyzer'],
        ]
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credit_limit',
        'billing_reference',
        'notes',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [
        'balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->credit_limit = CreditLimit::where('limit', '>', 0)->orderBy('limit')->get()->first()->limit;
        });
        self::created(function (self $model) {
            if ($cookieHash = request()->cookies->get('dropwow_uuid')) {
                UserSource::where(['cookie_hash'=> $cookieHash])->update(['user_id' => $model->id]);
            }
        });
    }

    public static function createBySocialProvider(
        SocialUser $socialUser,
        /** @noinspection PhpUnusedParameterInspection */
        string $providerName
    ): ?User {
        if (! $email = $socialUser->getEmail()) {
            return null;
        }

        return (new self)->create([
            'email'    => $email,
            'username' => $socialUser->getNickname(),
            'name'     => $socialUser->getName(),
            'password' => '-',
        ]);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(UserSource::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function myProducts(): HasMany
    {
        return $this->hasMany(MyProduct::class);
    }

    public function getOpenInvoice(): Invoice
    {
        $invoice = Invoice::firstOrCreate([
            'user_id' => $this->id,
            'status' => InvoiceStatusType::OPEN,
        ]); // We need an identificator to make a relation with an order later

        return $invoice;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getRejectedInvoices()
    {
        return Invoice::where([
            'user_id' => $this->id,
            'status' => InvoiceStatusType::REJECTED,
        ])->get();
    }

    /** @throws ModelNotFoundException */
    public function getPrimaryCard(): Card
    {
        /** @var Card $card */
        $card = $this->cards()->where('primary', true)->firstOrFail();

        return $card;
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /** @return mixed */
    public function setting(string $key)
    {
        if (! $setting = $this->settings()->where('key', $key)->first()) {
            if ($value = Setting::defaultSettings()->get($key)) {
                return $value['value'];
            }
            abort(Response::HTTP_NOT_FOUND);
        }

        return $setting->value;
    }

    public function setImpersonating($id): void
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating(): void
    {
        Session::forget('impersonate');
    }

    public function isImpersonating(): bool
    {
        return Session::has('impersonate');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(MyProductTag::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(MyProductCollection::class);
    }

    public function balanceHistory(): HasMany
    {
        return $this->hasMany(UserBalanceHistory::class);
    }

    /** @throws BalanceOperationException */
    public function changeBalance(float $sum, Model $initiator, ?string $description = null): void
    {
        if (! $sum) {
            throw new BalanceOperationException('Incorrect sum.');
        }
        if (! in_array(get_class($initiator), [self::class, Invoice::class])) {
            throw new BalanceOperationException('Passed initiator is not allowed.');
        }
        if ($initiator instanceof self) {
            if (! $initiator->can('updateUserBalance')) {
                throw new BalanceOperationException('Passed initiator does not have enough rights.');
            }
        }

        $newBalance = $this->attributes['balance'] + $sum;

        UserBalanceHistory::create([
            'user_id' => $this->id,
            'balance_before' => $this->balance,
            'balance_diff' => $sum,
            'balance_after' => $newBalance,
            'initiator_id' => $initiator->id,
            'initiator_type' => get_class($initiator),
            'description' => $description
        ]);

        $this->attributes['balance'] = $newBalance;
        $this->save();

        event('user.balance.changed', $this);
    }

    public function setBalanceAttribute(float $value): void
    {
        throw new \RuntimeException('Direct modification of the balance property is disabled. Use changeBalance() method instead.');
    }
}
