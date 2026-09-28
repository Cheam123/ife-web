<?php

namespace App\Models;

use App\Models\User;
use App\Models\Cities;
use App\Models\States;
use App\Models\Tasks;

use App\Models\Sales\User as Db2User;

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
}
