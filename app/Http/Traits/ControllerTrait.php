<?php

namespace App\Http\Traits;

trait ControllerTrait
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = false;

        $result = ($this->getModelClass())::find($id);

        if ($result) {
            $status = $result->delete();
            if ($status) {
                $message = "Deleted";
            } else {
                $message = "Deletion failed, please try again.";
            }
        } else {
            // Frontend should have prevented this
            $message = "Invalid id specified for deletion";
        }

        return [
            'id' => $id,
            'message' => $message,
            'status' => $status ? 'success' : 'danger',
        ];
    }

    /**
     * Extract an array of search words from a sentence
     */
    private function extractSearchStrings($searchSentence)
    {
        // Remove unimportant characters, such as: . , - _
        $searchSentence = preg_replace('/[.,_\-]/i', ' ', $searchSentence);

        return explode(' ', $searchSentence);
    }

    /**
     * Extract model class name from controller class name
     */
    private function getModelClass()
    {
        $controllerClassName = class_basename(__CLASS__);

        // Check for valid controller script class names
        if (str_ends_with($controllerClassName, $suffix = 'Controller')) {
        } else {
            // Class name not of recognizable format
            return false;
        }

        return '\\App\\Models\\' . substr($controllerClassName, 0, strlen($controllerClassName) - strlen($suffix));
    }

    /**
     * Get the primary key field name of the model
     */
    public function getModelPrimaryKeyName()
    {
        return (new ($this->getModelClass()))->getKeyName();
    }

    /**
     * Retrieve all field validation rules
     */
    public function getValidationRules()
    {
        return ($this->getModelClass())::getValidationRules();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $result = ($this->getModelClass())::merge($request);

        if ($result) {
            $relationshipsToLoad = ($this->getModelClass())::getRelationshipArray();
            if (count($relationshipsToLoad)) {
                $result->load($relationshipsToLoad);
            }
            $message = "Saved";
        } else {
            $message = "An error has been encountered during save, please try again.";
            $result = null;
        }

        return [
            'message' => $message,
            'result' => $result,
            'status' => $result ? 'success' : 'danger',
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $result = ($this->getModelClass())::merge($request, $id);

        if ($result) {
            // Tip: Use `refresh()` to reload relationship, e.g. useful when role_id has changed
            $result->refresh();

            $relationshipsToLoad = ($this->getModelClass())::getRelationshipArray();
            if (count($relationshipsToLoad)) {
                $result->load($relationshipsToLoad);
            }
            $message = "Updated";
        } else {
            $message = "An error has been encountered during update, please try again.";
            $result = null;
        }

        return [
            'id' => $id,
            'message' => $message,
            'result' => $result,
            'status' => $result ? 'success' : 'danger',
        ];
    }
}
