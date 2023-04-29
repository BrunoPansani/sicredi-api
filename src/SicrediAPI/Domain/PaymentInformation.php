<?php

namespace SicrediAPI\Domain;

class PaymentInformation
{
    // {
    //     "txid":"445488181811848",
    //     "qrCode":"00020126930014br.gov.bcb.pix2571pix-qrcode-h.sicredi.com.br/qr/v2/cobv/528520acdd5f4740b63b9b643ca2bcf99999999999999999999BR5903PIX6006Cidade62070503***630441AC\n\n",
    //     "linhaDigitavel":"74891121150039736789903123451001187340000000050",
    //     "codigoBarras":"74897937700000099891122224595067890312345109",
    //     "cooperativa":"6789",
    //     "posto":"03",
    //     "nossoNumero":"211001292"}

    /**
     * @var int ID of the transaction
     */
    private int $transactionId;

    /**
     * @var string String that represents a QR Code
     */
    private string $qrCode;

    /**
     * @var string Numeric representation of the boleto - "linha digitável"
     */
    private string $numericRepresentation;

    /**
     * @var string Barcode
     */
    private string $barcode;

    /**
     * @var string Identifier of the boleto on Sicredi - "nosso número"
     */
    private string $ourNumber;

    public function __construct($transactionId, $qrCode, $numericRepresentation, $barcode, $ourNumber)
    {
        $this->transactionId = $transactionId;
        $this->qrCode = $qrCode;
        $this->numericRepresentation = $numericRepresentation;
        $this->barcode = $barcode;
        $this->ourNumber = $ourNumber;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getQrCode()
    {
        return $this->qrCode;
    }

    public function getNumericRepresentation()
    {
        return $this->numericRepresentation;
    }

    public function getBarcode()
    {
        return $this->barcode;
    }

    public function getOurNumber()
    {
        return $this->ourNumber;
    }

    public static function fromArray($data)
    {
        return new PaymentInformation(
            $data['txid'] ?? $data['txId'],
            $data['qrCode'] ?? $data['codigoQrCode'],
            $data['linhaDigitavel'],
            $data['codigoBarras'],
            $data['nossoNumero']
        );
    }
}
