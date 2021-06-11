<?php


namespace App\Services\QRCode;


use chillerlan\QRCode\Data\QRCodeDataException;
use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QRCodeException;
use chillerlan\QRCode\QROptions;

class QRCodeService
{
    /**
     * @param string $url
     * @return string
     * @throws QRCodeDataException
     * @throws QRCodeOutputException
     * @throws QRCodeException
     */
    public function render(string $url): string
    {
        $options = new QROptions();
        $options->version = QRCode::VERSION_AUTO;
        $options->eccLevel = QRCode::ECC_H;
        $options->imageBase64 = false;
        $options->logoSpaceWidth = 13;
        $options->logoSpaceHeight = 13;
        $options->scale = 5;
        $options->imageTransparent = false;

        $outputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($url));

        return $outputInterface->dump(null, storage_path('/app/private/images/logos/logo-blue-64x64.png'));
    }
}
