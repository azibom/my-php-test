<?php

namespace App\Repositories\Call;

interface CallRepositoryInterface {
    public function newCall($request);
    public function endOfTheCall($request);
    public function newEmployee($request);
    public function newCallWant($request);
}