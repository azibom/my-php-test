<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function isThereFreeEmployee()
    {
        $freeEmployees = $this->where("state", "waiting")->get();
        if (count($freeEmployees) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getFreeEmployee()
    {
        $freeEmployee = $this->where("state", "waiting")->orderBy('priority', 'desc')->first();
        return $freeEmployee;
    }
}
