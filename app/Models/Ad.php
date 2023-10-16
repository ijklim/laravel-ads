<?php

namespace App\Models;

class Ad extends \Illuminate\Database\Eloquent\Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ad_code';

    /**
     * The attributes that are not mass assignable.
     * Note: $guarded is the reverse of $fillable, use only one
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Whether primary key auto increment. Affects primary key field value after `->save()`
     *
     * @var boolean
     */
    public $incrementing = false;


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
            'href' => [
                'required',
                'max:255',
                \Illuminate\Validation\Rule::unique('ads')->ignore($id, 'ad_code'),
            ],
            'image_alt_text' => 'max:255',
            'image_description' => 'max:255',
        ];
    }

    /**
     * Insert or update a resource in storage
     */
    public static function merge($request, $id = null)
    {
        // Note: Do NOT use try-catch as validation error will not show on the browser
        $validatedData = $request->validate(self::getValidationRules($id));

        $payload = $request->input();

        $result = self::updateOrCreate(
            [ (new self)->getKeyName() => $id ],
            $payload
        );

        return $result;
    }
}
