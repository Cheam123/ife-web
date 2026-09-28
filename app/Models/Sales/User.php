<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Config;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Notifiable;

    protected $table = 'users';

    /*********
     * user_type value
     * =========
     * 1: Reserved 
     * 2: Normal 
     * 3: Salesperson 
     * 4: Pre-Sales 
     * 7: Customer-Support
     * 8: System-Support
     * 9: Partnership-Support
     * 
     * Data column value 
     * =================
     * Pre-Sales        : list down all user-type = pre-sales & salesperson as selection
     * Level1-Support   : list down all user-type = level1-support as selection
     * Level2-Support   : list down all user-type = level2-support as selection
     * Project-Support  : list down all user-type = project-support as selection
     * Closing-Support  : list down all user-type = salesperson as selection
     * System-Support   : list down all user-type = system-support as selection
     * 
     * WHEN USER-TYPE = Partnership
     * When partnership_margin > 0
     *      calculate the commission (pro-rate) give to all user_type = 9 (partnership))
     * 
     * WHEN USER-TYPE = System-Support
     * When system_support_margin > 0
     *     If system_support = not null 
     *      calculate the commission then give to the system_support_id
     *     if system_support = null
     *      calculate the commission (pro-rate) give to all user_type = 8 (system-support)
     */

    protected $fillable = [
        'tin',
        'show_private',
        'has_tools',
        'user_type', // 1:Reserve 2:Normal 3:Salesperson 4:Pre-Sales 5:Level1-Support (admin) 6:Level2-Support (IT)
        'account_debtor', // this is debtor in accounting software
        'account_sales_person', // this is upline name in accounting software
        'pre_sales',
        'level1_support',
        'level2_support',
        'level3_support',
        'level4_support',
        'system_support',
        'project_support',
        'closing_support',
        'system_comm_ratio', // this field only applicable for user_type = system support. if the order system support is null, commission will share to all user_type = system support base on this ratio.
        'status',
        'name',
        'internal_name',
        'company_name',
        'company_email',
        'company_tel',
        'email',
        'private',
        'token_expires',
        'social_id',
        'ic',
        'gender',
        'dob',
        'dial_code',
        'contact_number',
        'bank_name',
        'bank_account_holder',
        'bank_account',
        'stockist_point',
        'stockist_credit',
        'wallet_balance',
        's3_image_path',
        'previous_stockist_id',
        'stockist_id',
        'is_purchase_rank',
        'purchase_rank_start_date',
        'purchase_rank_end_date',
        'stockist_reference',
        'member_id',
        'previous_member_id',
        'is_purchase_member',
        'purchase_member_start_date',
        'purchase_member_end_date',

        'term_due',
        'term_unallocate_amount',
        'term_balance_limit',
        'term_limit',
        'term_deposit',
        'term_balance_risk',
        'term_accum_profit',
        'allow_see_dl_orders'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'wallet_pin',
        'remember_token',
        'fb_token',
        'gmail_token',
        'apple_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'     => 'datetime',
        'last_visit_datetime'   => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => UserCreated::class,
    ];

    public function presales()
    {
        return $this->hasOne(User::class, 'id', 'pre_sales');
    }

    public function level1support()
    {
        return $this->hasOne(User::class, 'id', 'level1_support');
    }

    public function level2support()
    {
        return $this->hasOne(User::class, 'id', 'level2_support');
    }

    public function level3support()
    {
        return $this->hasOne(User::class, 'id', 'level3_support');
    }

    public function level4support()
    {
        return $this->hasOne(User::class, 'id', 'level4_support');
    }

    public function projectsupport()
    {
        return $this->hasOne(User::class, 'id', 'project_support');
    }

    public function closingsupport()
    {
        return $this->hasOne(User::class, 'id', 'closing_support');
    }

    public function systemsupport()
    {
        return $this->hasOne(User::class, 'id', 'system_support');
    }

    public function upline()
    {
        return $this->hasOne(User::class, 'id', 'stockist_reference');
    }
}
