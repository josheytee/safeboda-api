<?php

namespace App\Http\Controllers;

use Polyline;
use App\PromoCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\PromoCodeResource;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{

    /**
     * Retrieve promo codes
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->has('type') && request()->type == "active") {
            $promoCodes = PromoCode::where('expires_at', '>=', Carbon::now()->toDateString())->get();
            $promoCodes = PromoCodeResource::collection($promoCodes)->hide(['deleted_at']);
        } else {
            $promoCodes = PromoCodeResource::collection(PromoCode::withTrashed()->get());        } 
        return response()->json(['promoCodes' => ($promoCodes)], 200);
    }

    /**
     * Create new promo code
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateCreation($request);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $promoCodeModel = new PromoCode;
        $promoCode = $promoCodeModel::create([
            'code' => $promoCodeModel->generateCode(),
            'expires_at' => $request->input('expires_at'),
            'radius' => $request->input('radius'),
            'amount' => $request->amount,
        ]);

        return response()->json(['promoCode' => (new PromoCodeResource($promoCode))->hide(['deleted_at'])], 201);
    }

    /**
     * Validate promo code creation
     *
     * @param Request $request
     * @return Illuminate\Support\Facades\Validator
     */
    private function validateCreation(Request $request)
    {
        $customAttribute = ['expires_at' => 'expiry date'];
        $validator = Validator::make($request->all(), [
            'radius' => 'required|numeric',
            'amount' => 'required|numeric',
            'expires_at' => 'required|date'
        ], [], $customAttribute);

        return $validator;
    }

    /**
     * Check promo code validity
     *
     * @param Request $request
     * @param int $code
     * @return Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        /** @var \App\PromoCode $promoCode */
        $code = request()->input('code');
        $promoCode = PromoCode::where('code' , $code)->first();
        // $promoCode = PromoCode::find($code);


        //Return 404 if promo code is non-existent or not active
        if (!$promoCode || !$this->isActive($promoCode)) {
            return response()->json(['error' => 'Promo code not found'], 404);
        }

        $validator = $this->validateShowPromoCode($request);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $data = $request->only('origin', 'destination');
        if (
            $promoCode->isValid($data["origin"], $data["destination"])
        ) {
            $promoCode = (new PromoCodeResource($promoCode))->hide(['deleted_at']);
            $polyline = Polyline::encode([
                [$data['origin']['lat'], $data['origin']['lon']],
                [$data['destination']['lat'], $data['destination']['lon']],
            ]);
            return response()->json(['promoCode' => $promoCode, 'polyline' => $polyline], 200);
        }

        return response()->json(['error' => "Promo code is not valid"], 400);
    }

    /**
     * Validate promo code validity check
     *
     * @param Request $request
     * @return Illuminate\Support\Facades\Validator
     */
    private function validateShowPromoCode(Request $request)
    {
        $customAttributes = ['origin.lon' => 'Origin Longitude', 'origin.lat' => 'Origin Latitude', 'destination.lon' => 'Destination Longitude', 'destination.lat' => 'Destination Latitude'];

        $validator = Validator::make($request->all(), [
            // 'code' => 'required|exists:promo_codes,code',
            'origin.lon' => 'required|numeric',
            'origin.lat' => 'required|numeric',
            'destination.lon' => 'required|numeric',
            'destination.lat' => 'required|numeric',
        ], $customAttributes);
        return $validator;
    }

    /**
     * Configure promo code radius
     *
     * @param Request $request
     * @param int $code
     * @return void
     */
    public function update(Request $request, $code)
    {
        // $promoCode = PromoCode::find($code);
        $promoCode = PromoCode::where('code' , $code)->first();

        if ($promoCode) {
            $validator = Validator::make($request->all(), [
                'radius' => 'required|numeric',
            ]);
            if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

            $promoCode->radius = $request->input('radius');
            $promoCode->save();
            return response()->json(['promoCode' => $promoCode], 200);
        }
        return response()->json(['error' => "Promo code not found"], 404);
    }

    /**
     * Deactivate Promo Code
     *
     * @param int $code
     * @return Illuminate\Http\Response
     */
    public function deactivate($code)
    {
        // $promoCode = PromoCode::find($code);
        $promoCode = PromoCode::where('code' , $code)->first();

        if ($promoCode) {
            $promoCode->delete(); //Soft delete promo code
            return response()->json(['message' => "Promo code deactivated"]);
        }
        return response()->json(['error' => "Promo code not found"], 404);
    }

    /**
     * Check if promo code is active and has not passed expiry date
     *
     * @param \App\PromoCode $promoCode
     * @return boolean
     */
    private function isActive($promoCode)
    {
        return ($promoCode->expires_at >= Carbon::now()->toDateString() && is_null($promoCode->deleted_at));
    }
}
