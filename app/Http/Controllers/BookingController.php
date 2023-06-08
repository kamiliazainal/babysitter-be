<?php

namespace App\Http\Controllers;

use App\Models\BookingDetail;
use App\Models\ChildrenDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use Log;

class BookingController extends Controller
{
    public function bookingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_datetime'    => 'required',
            'end_datetime'      => 'required',
            'parent_name'       => 'required|string',
            'parent_phone'      => 'required|string',
            'parent_email'      => 'required|email',
            'parent_address'    => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toArray()]);
        }

        try {
            $startDatetime = Carbon::parse($request->start_datetime)->tz('Asia/Kuala_Lumpur');
            $endDatetime = Carbon::parse($request->end_datetime)->tz('Asia/Kuala_Lumpur');
            $currentDatetime = Carbon::now()->tz('Asia/Kuala_Lumpur');

            if ($startDatetime->diffInHours($currentDatetime) < 6) {
                return response()->json(['message' => 'Reservation datetime should be at least 6 hours before the current time.']);
            }

            if ($startDatetime->diffInDays($currentDatetime) > 60) {
                return response()->json(['message' => 'Reservation datetime should be within 60 days from today.']);
            }

            if ($startDatetime->greaterThanOrEqualTo($endDatetime)) {
                return response()->json(['message' => 'End datetime should be greater than start datetime.']);
            }

            $bookingDetails = BookingDetail::create(
                [
                    'start_datetime'    => $startDatetime,
                    'end_datetime'      => $endDatetime,
                    'parent_name'       => data_get($request, 'parent_name', ''),
                    'parent_phone'      => data_get($request, 'parent_phone', ''),
                    'parent_email'      => data_get($request, 'parent_email', ''),
                    'parent_address'    => data_get($request, 'parent_address', ''),
                    'spouse_name'       => data_get($request, 'spouse_name'),
                    'spouse_phone'      => data_get($request, 'spouse_phone')
                ]);

            return response()->json(['data' => $bookingDetails], 200);

        } catch (Exception $e) {
            Log::error("bookingDetails failed ", [$e->getMessage()]);
        }
    }

    public function childrenDetails(Request $request, $booking_id)
    {
        $validator = Validator::make($request->all(), [
            'children' => 'required|array',
            'children.*.name' => 'required|string',
            'children.*.age' => 'required|integer|min:1|max:155',
            'children.*.gender' => 'required|string|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toArray()]);
        }

        try {
            if (count($request->children) > 4) {
                return response()->json(['message' => 'Maximum of 4 children are allowed.']);
            }

            foreach ($request->children as $childData) {
                $childrenDetails[] = ChildrenDetail::create(
                    [
                        'booking_id' => $booking_id,
                        'name' => data_get($childData, 'name', ''),
                        'age' => data_get($childData, 'age', ''),
                        'gender' => data_get($childData, 'gender', ''),
                    ]);
            }

            return response()->json([
                'message' => 'Booking created successfully',
                'data' => $childrenDetails
                ], 200);

        } catch (Exception $e) {
            Log::error("childrenDetails failed ", [$e->getMessage()]);
        }
    }

    public function bookingSummary($booking_id)
    {
        $bookingDetails = BookingDetail::with('children')->where('id', $booking_id)->first();

        $startDate = data_get($bookingDetails, 'start_datetime', '');
        $parseStartDate = $startDate ? Carbon::parse($startDate)->format('j F Y, g:i a') : '';

        $endDate = data_get($bookingDetails, 'end_datetime', '');
        $parseEndDate = $endDate ? Carbon::parse($endDate)->format('j F Y, g:i a') : '';
        return [
            'startDate'     => $parseStartDate,
            'endDate'       => $parseEndDate,
            'parentName'    => data_get($bookingDetails, 'parent_name', ''),
            'parentPhone'   => data_get($bookingDetails, 'parent_phone', ''),
            'parentEmail'   => data_get($bookingDetails, 'parent_email', ''),
            'parentAddress' => data_get($bookingDetails, 'parent_address', ''),
            'spouseName'    => data_get($bookingDetails, 'spouse_name', ''),
            'spousePhone'   => data_get($bookingDetails, 'spouse_phone'),
            'children'      => $bookingDetails->children->map(function ($children) {
                                    return $this->transformChildrens($children);
                                }),
        ];
    }

    private function transformChildrens ($children)
    {
        return [
            'childName'     => data_get($children, 'name'),
            'childAge'      => data_get($children, 'age'),
            'childGender'   => data_get($children, 'gender')
        ];
    }
}
