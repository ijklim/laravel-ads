<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AdTypeController extends Controller
{
    use \App\Http\Traits\ControllerTrait;

    /**
     * Retrieve the specified resource.
     *
     * Note: Laravel automatically returns JSON format for Eloquent models or collections without call to `->json()`
     *
     * Usage Tests:
     * • https://ads-server.localhost/api/ad-types
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $modelClass = $this->getModelClass();
        $primaryKeyField = (new $modelClass)->getKeyName();
        $query = $modelClass::whereNotNull($primaryKeyField);

        // === All rows meeting conditions ===
        return $query->get();
    }
}
