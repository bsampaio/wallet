<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\QRCode\QRCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller
{
    const CANT_CREATE_QRCODE = 'Can\'t create a QRCode with the given URL.';
    /**
     * @var QRCodeService
     */
    private $qrcodeService;

    public function __construct()
    {
        $this->qrcodeService = new QRCodeService();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function qrcode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }


        $image = $this->qrcodeService->render($request->url, true);
        return response()->json(['image' => $image]);
    }
}
