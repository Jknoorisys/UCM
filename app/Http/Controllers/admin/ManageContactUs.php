<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageContactUs extends Controller
{
    public function getContactUs(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_number'   => 'required||numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.validation'),
                    'errors'    =>  $validator->errors(),
                ], 400
            );
        } 

        try {
            $per_page = 10;
            $page_number = $request->input(key:'page_number', default:1);

            $search = $request->search ? $request->search : '';

            $query = ContactUs::query();
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('phone', 'LIKE', "%$search%");
            }

            $contactUs = $query->offset(($page_number - 1) * $per_page)
                                    ->limit($per_page)
                                    ->get();

            $total =  $query->count();       
            if ($contactUs) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $contactUs
                ], 200);
            }else{
                return response()->json([
                    'status'    => 'success',
                    'message'   =>  trans('msg.list.failed'),
                    'data'      => $contactUs
                ], 200);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
