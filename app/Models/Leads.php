<?php

namespace App\Models;

use App\Models\User;
use App\Models\Cities;
use App\Models\States;
use App\Models\Tasks;

use App\Models\Sales\User as Db2User;
use App\Services\FormSchemaService;

use App\Traits\Paginatable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Leads extends Model
{
    use HasFactory, Paginatable, Sortable;
    use SoftDeletes;

    /** Outlet size, ordered small to large (the recommender treats it as a rank). */
    public const SIZE_BANDS = [
        'small'  => 'Small',
        'medium' => 'Medium',
        'large'  => 'Large',
    ];

    /** Price segment the outlet sells into. */
    public const SEGMENTS = [
        'budget'    => 'Budget',
        'mid_range' => 'Mid-range',
        'premium'   => 'Premium',
    ];

    protected $casts = [
        'latitude'             => 'float',
        'longitude'            => 'float',
        'location_accuracy'    => 'float',
        'location_captured_at' => 'datetime',
        'seats'                => 'integer',
    ];

    protected $fillable = [
        'customer_id',
        'business_name',
        'receiving_date',
        'belong_to',
        'assign_to',
        'hq_checker',
        'name',
        'business_category',
        'source',
        'email',
        'mobile',
        'address',
        'city_id',
        'state_id',
        'postcode',
        'ife_area_id',
        'latitude',
        'longitude',
        'location_accuracy',
        'location_captured_at',
        'size_band',
        'seats',
        'segment',
        'remark',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'upline_name',
        'presales_name',
        'closing_sales_name',
    ];

    public function getUplineNameAttribute()
    {
        $customer = new Db2User();
        $customer->setConnection('mysql2');
        $customer = $customer->where('id',$this->customer_id)->first();

        if (!$customer) {
            return '';
        } else {
            $upline = new db2User();
            $upline->setConnection('mysql2');
            $upline = $upline->where('id',$customer->stockist_reference)->first();

            if (!$upline) {
                return '';
            } else {
                return ucwords(strtolower($upline->name));
            }
        }
    }

    public function getPresalesNameAttribute()
    {
        $customer = new db2User();
        $customer->setConnection('mysql2');
        $customer = $customer->where('id',$this->customer_id)->first();

        if (!$customer) {
            return '';
        } else {
            $pre_sales = new db2User();
            $pre_sales->setConnection('mysql2');
            $pre_sales = $pre_sales->where('id',$customer->pre_sales)->first();

            if (!$pre_sales) {
                return '';
            } else {
                return ucwords(strtolower($pre_sales->name));
            }
        }
    }

    public function getClosingSalesNameAttribute()
    {
        $customer = new db2User();
        $customer->setConnection('mysql2');
        $customer = $customer->where('id',$this->customer_id)->first();

        if (!$customer) {
            return '';
        } else {
            $closing_support = new db2User();
            $closing_support->setConnection('mysql2');
            $closing_support = $closing_support->where('id',$customer->closing_support)->first();

            if (!$closing_support) {
                return '';
            } else {
                return ucwords(strtolower($closing_support->name));
            }
        }
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'id');
    }
    
    public function ifearea()
    {
        return $this->belongsTo(IfeArea::class, 'ife_area_id', 'id');
    }

    /**
     * Admins and Managers see every lead; a normal User sees the leads they
     * created or that are assigned to them.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->seesAllRecords()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('belong_to', $user->id)
              ->orWhere('assign_to', $user->id);
        });
    }

    // creater
    public function createdBy()
    {
        return $this->belongsTo(User::class,'belong_to','id')->withTrashed();
    }

    // the last subscriber
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assign_to', 'id')->withTrashed();
    }

    public function hqChecker()
    {
        return $this->belongsTo(User::class, 'hq_checker', 'id')->withTrashed();
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id')->orderBy('status','asc')->orderBy('created_at','desc');
    }

    public function runningTasks()
    {
        return $this->hasMany(Tasks::class, 'lead_id', 'id')->whereIn('status',[1,2,3]);
    }

    public function documentUploads()
    {
        return $this->hasMany(DocumentUpload::class,'lead_id','id');
    }

    /** Visits (IFE reports) made at this outlet, newest first. */
    public function visits()
    {
        return $this->hasMany(IFEReport::class, 'lead_id', 'id')->orderByDesc('created_at');
    }

    /** Orders this outlet placed, newest first. */
    public function orders()
    {
        return $this->hasMany(Order::class, 'lead_id', 'id')->orderByDesc('order_date')->orderByDesc('id');
    }

    public function recommendation()
    {
        return $this->hasOne(OutletRecommendation::class, 'lead_id', 'id');
    }

    public static function sizeBandLabel(?string $value): ?string
    {
        return $value === null ? null : (self::SIZE_BANDS[$value] ?? $value);
    }

    public static function segmentLabel(?string $value): ?string
    {
        return $value === null ? null : (self::SEGMENTS[$value] ?? $value);
    }

    /**
     * Where the outlet is, in the GPS Stamp answer shape
     * {lat, lng, accuracy, captured_at}; null when never captured.
     */
    public function locationStamp(): ?array
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return [
            'lat'         => (float) $this->latitude,
            'lng'         => (float) $this->longitude,
            'accuracy'    => $this->location_accuracy === null ? null : (float) $this->location_accuracy,
            'captured_at' => optional($this->location_captured_at)->toIso8601String(),
        ];
    }

    /**
     * Writes a location captured on the device. Same shape and validation as
     * a GPS Stamp form answer; an empty value clears the location.
     *
     * @return string|null the validation error, or null once applied
     */
    public function applyLocationStamp($value): ?string
    {
        $gps = app(FormSchemaService::class)->normalizeGps(['label' => 'the outlet'], $value);
        if ($gps['error'] !== null) {
            return $gps['error'];
        }

        $stamp = $gps['value'];

        $this->latitude             = $stamp['lat'] ?? null;
        $this->longitude            = $stamp['lng'] ?? null;
        $this->location_accuracy    = $stamp['accuracy'] ?? null;
        $this->location_captured_at = isset($stamp['captured_at']) ? \Illuminate\Support\Carbon::parse($stamp['captured_at']) : null;

        return null;
    }
}
