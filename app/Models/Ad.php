<?php

namespace App\Models;

class Ad extends \Illuminate\Database\Eloquent\Model
{

    /**
     * The accessors to append to the model's array form. (Tip)
     *
     * @var array
     */
    protected $appends = [
        'url_affiliate',
        'url_product',
        'url_segment_image',
    ];

    /**
     * Defining default attribute values
     */
    protected $attributes = [
        'is_enabled' => true,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ad_code' => 'string',
        'html_updated_at' => 'datetime',
        'is_enabled' => 'boolean',
        'price_updated_at' => 'datetime',
    ];

    /**
     * The attributes that are not mass assignable.
     * Note: $guarded is the reverse of $fillable, use only one
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays. (Tip)
     *
     * @var array
     */
    protected $hidden = [
        'html',
    ];

    /**
     * Whether primary key auto increment. Affects primary key field value after `->save()`
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ad_code';


    // === Relationships ===
    public function adType()
    {
        return $this->belongsTo(
            AdType::class,
            (new AdType)->getKeyName()
        );
    }


    // === Accessors ===
    /**
     * Create accessor field `url_affiliate`
     * • Amazon Product affiliate link if AmazonBanner
     * • href
     */
    public function getUrlAffiliateAttribute()
    {
        if ($this->ad_type === 'AmazonBanner' && $this->ad_code) {
            return "https://www.amazon.com/gp/product/$this->ad_code?th=1" .
                '&linkCode=ll1' .
                '&tag=aimprove-20' .
                '&language=en_US' .
                '&ref_=as_li_ss_tl';
        }

        return $this->href;
    }

    /**
     * Create accessor field `url_product`
     * • Amazon Product link if AmazonBanner
     * • href
     */
    public function getUrlProductAttribute()
    {
        if ($this->ad_type === 'AmazonBanner') {
            return "https://www.amazon.com/gp/product/$this->ad_code";
        }

        return $this->href;
    }

    /**
     * Where the Amazon product image is located on this server
     */
    public function getUrlSegmentImageAttribute()
    {
        // Product pictures should all be of type "webp"
        switch ($this->ad_type) {
            case 'AmazonBanner':
                return "/img/Amazon/$this->ad_code.webp";
            case 'MochahostBanner':
                return "/img/Mochahost/$this->ad_code.webp";
            case 'ImageAd':
                return "/img/ImageAd/$this->ad_code.webp";
            default:
                return '';
        }
    }


    // === Public functions ===
    /**
     * The list of relationships needed for store and update
     */
    public static function getRelationshipArray()
    {
        return [
        ];
    }

    /**
     * Field requirements for the current model
     */
    public static function getValidationRules($id = null)
    {
        return [
            'ad_code' => 'required|max:30',
            'ad_type' => 'required|max:30',
            'display_ratio' => 'required|integer',
            'image_description' => 'nullable|max:255',
            'title' => 'nullable|max:255',
        ];
    }

    /**
     * Insert or update a resource in storage
     */
    public static function merge($request, $id = null)
    {
        $skipValidation = $request->skip_validation ?? false;

        if (!$skipValidation) {
            // Note: Do NOT use try-catch as validation error will not show on the browser
            $validatedData = $request->validate(self::getValidationRules($id));
        }

        $self = (new self);

        // Remove accessor fields that will cause update error
        $payload = $request->except('skip_validation', ...$self->appends);

        $result = self::updateOrCreate(
            [ $self->getKeyName() => $id ],
            $payload
        );

        return $result;
    }
}
