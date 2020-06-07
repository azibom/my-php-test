<?php

namespace App\Repositories\Call;

use App\Call;
use App\Employee;
use App\Repositories\Call\CallTypes\CallTypesRepositoryInterface;
use App\Repositories\Call\CallTypes\NormalCallRepository;
use App\Repositories\Call\CallTypes\UrgentCallRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class CallRepository implements CallRepositoryInterface
{
    const URGENT_CALL = "urgent";
    const NORMAL_CALL = "normal";

    const EMPLOYEE_WAITING_STATE = "waiting";
    const EMPLOYEE_BUSY_STATE = "busy";

    const CALL_WAITING_STATE = "waiting";
    const CALL_DOING_STATE = "doing";
    const CALL_DONE_STATE = "done";

    const SERVER_ERROR_CODE = 503;
    const SUCCESS_CODE = 200;

    public $normalCallRepository;
    public $urgentCallRepository;
    public $call;
    public function __construct(NormalCallRepository $normalCallRepository, UrgentCallRepository $urgentCallRepository, Call $call)
    {
        $this->urgentCallRepository = $urgentCallRepository;
        $this->normalCallRepository = $normalCallRepository;
        $this->call = $call;
    }
    
    public function newCall($request)
    {
        $type = $request->type;
        $hash = $request->hash;
        if ($type == self::URGENT_CALL) {
            return $this->callOpratoin($this->urgentCallRepository, $hash);
        } elseif ($type == self::NORMAL_CALL) {
            return $this->callOpratoin($this->normalCallRepository, $hash);
        }
    }

    public function callOpratoin(CallTypesRepositoryInterface $call, $hash)
    {
        $call->setdata($hash);
        $response = $call->initOpration();
        return $this->response(['message' => $response['message']], $response['statusCode']);
    }

    public function endOfTheCall($request)
    {
        $hash = $request->hash;
        $call = Call::where('hash', $hash)->first();
        if (!isset($call)) {
            return $this->response(['message' => 'incorrect hash'], self::SUCCESS_CODE);
        }

        if ($call->state != self::CALL_DOING_STATE) {
            return $this->response(['message' => 'incorrect hash'], self::SUCCESS_CODE);
        }

        DB::beginTransaction();
        try {
            $employee = Employee::where('id', $call->employee_id)->first();
                    
            $call->state = self::CALL_DONE_STATE;
            $call->save();
    
            if ($this->call->isThereUrgentCall()) {
                $urgentCall = $call->getUrgentCall();
                $this->assignCallToEmployee($urgentCall, $employee);
            } else {
                $employee->state = self::EMPLOYEE_WAITING_STATE;
                $employee->save();
            }
            DB::commit();
            return $this->response(['message' => 'Successfuly done'], self::SUCCESS_CODE);
        } catch (Exception $ex) {
            DB::rollback();
            return $this->response(['message' => 'Server error'], self::SERVER_ERROR_CODE);
        }
    }
    
    public function newEmployee($request)
    {
        $employee = new Employee;
        $employee->priority = $request->priority;
        $employee->state = self::EMPLOYEE_WAITING_STATE;
        $employee->save();

        if ($this->call->isThereUrgentCall()) {
            $urgentCall = $this->call->getUrgentCall();
            DB::beginTransaction();
            try {
                $this->assignCallToEmployee($urgentCall, $employee);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                return $this->response(['message' => 'Server error'], self::SERVER_ERROR_CODE);
            }
        }

        return $this->response(['message' => 'Successfully added'], self::SUCCESS_CODE);
    }

    public function newCallWant($request)
    {
        $employee_id = $request->id;
        $employee = Employee::find($employee_id);
        if (!isset($employee)) {
            return $this->response(['message' => 'User not found'], self::SUCCESS_CODE);
        }

        if ($employee->state != self::EMPLOYEE_WAITING_STATE) {
            return $this->response(['message' => 'User is busy'], self::SUCCESS_CODE);
        }

        if ($this->call->isThereNormalCall()) {
            $normalCall = $this->call->getNormalCall();
            DB::beginTransaction();
            try {
                $this->assignCallToEmployee($normalCall, $employee);
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                return $this->response(['message' => 'Server error'], self::SERVER_ERROR_CODE);
            }
        }
        return $this->response(['message' => 'Successfully assigned'], self::SUCCESS_CODE);
    }

    public function assignCallToEmployee($call, $employee)
    {
        $call->state = self::CALL_DOING_STATE;
        $call->employee_id = $employee->id;
        $call->save();

        $employee->state = self::EMPLOYEE_BUSY_STATE;
        $employee->save();
    }

    public function response($data, int $statusCode)
    {
        $response = ["data"=>$data, "statusCode"=>$statusCode];
        return $response;
    }
}
