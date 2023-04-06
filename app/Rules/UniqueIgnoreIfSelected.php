<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueIgnoreIfSelected implements Rule
{
    private  $table;
    private  $column;
    private  $selectedItemID;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $table, string $column, int $selectedItemID)
    {
        //
        $this->table = $table;
        $this->column = $column;
        $this->selectedItemID = $selectedItemID;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        $recordExists = DB::table($this->table)
            ->where($this->column, $value)
            ->where('id', '!=', $this->selectedItemID)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
