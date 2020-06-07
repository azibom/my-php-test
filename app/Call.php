<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public function isThereUrgentCall()
    {
        $urgentCall = $this->where("state", "waiting")->where("priority", "high")->get();
        if (count($urgentCall) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUrgentCall()
    {
        $urgentCall = $this->where("state", "waiting")->where("priority", "high")->first();
        return $urgentCall;
    }

    public function isThereNormalCall()
    {
        $urgentCall = $this->where("state", "waiting")->where("priority", "low")->get();
        if (count($urgentCall) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getNormalCall()
    {
        $urgentCall = $this->where("state", "waiting")->where("priority", "low")->first();
        return $urgentCall;
    }
}
