<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndOfTheCallRequest;
use App\Http\Requests\NewCallRequest;
use App\Http\Requests\NewCallWantRequest;
use App\Http\Requests\NewEmployeeRequest;
use App\Repositories\Call\CallRepositoryInterface;

class CallController extends Controller
{
    public function __construct(CallRepositoryInterface $callRepository)
    {
        $this->callRepository = $callRepository;
    }

    public function newCall(NewCallRequest $request)
    {
        $response = $this->callRepository->newCall($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function endOfTheCall(EndOfTheCallRequest $request)
    {
        $response = $this->callRepository->endOfTheCall($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function newCallWant(NewCallWantRequest $request)
    {
        $response = $this->callRepository->newCallWant($request);
        return response()->json($response["data"], $response["statusCode"]);
    }

    public function newEmployee(NewEmployeeRequest $request)
    {
        $response = $this->callRepository->newEmployee($request);
        return response()->json($response["data"], $response["statusCode"]);
    }
}
