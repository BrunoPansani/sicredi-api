<?php

namespace SicrediAPI\Mappers;

use DateTime;
use SicrediAPI\Domain\Boleto\Boleto as BoletoDomain;
use SicrediAPI\Domain\Boleto\PaymentInformation;
use SicrediAPI\Domain\Boleto\DiscountConfiguration as DiscountConfigurationDomain;
use SicrediAPI\Domain\Boleto\InterestConfiguration as InterestConfigurationDomain;
use SicrediAPI\Domain\Boleto\Beneficiary as BeneficiaryDomain;
use SicrediAPI\Domain\Boleto\Payee as PayeeDomain;
use SicrediAPI\Domain\Boleto\Messages as MessagesDomain;
use SicrediAPI\Domain\Boleto\Information as InformationDomain;
use SicrediAPI\Domain\Boleto\Liquidation as LiquidationDomain;

class Boleto
{
    public static function mapCreateBoleto(BoletoDomain $boleto)
    {
        $base = [
            'beneficiarioFinal' => (new Beneficiary($boleto->getBeneficiary()))->toArray(),
            'pagador' => (new Payee($boleto->getPayee()))->toArray(),
            'nossoNumero' => $boleto->getOurNumber(),
            'seuNumero' => $boleto->getYourNumber(),
            'dataVencimento' => $boleto->getDueDate()->format('Y-m-d'),
            'valor' => $boleto->getAmount(),
            'codigoBeneficiario' => $boleto->getBeneficiaryCode(),
            'especieDocumento' => $boleto->getDocumentType(),
            'informativo' => (new Messages($boleto->getInformation()))->toArray(),
            'mensagem' => (new Messages($boleto->getMessages()))->toArray(),
        ];

        if (!empty($boleto->getDiscounts())) {
            array_merge($base, (new DiscountConfiguration($boleto->getDiscounts()))->toArray());
        }

        // Merge getInterest
        if (!empty($boleto->getInterests())) {
            array_merge($base, (new InterestConfiguration($boleto->getInterests()))->toArray());
        }

        // Merge messages and information
        if (!empty($boleto->getMessages())) {
            array_merge($base, (new Messages($boleto->getMessages()))->toArray());
        }

        if (!empty($boleto->getInformation())) {
            array_merge($base, (new Information($boleto->getInformation()))->toArray());
        }

        return array_filter($base, function ($value) {
            return !empty($value);
        });
    }

    /**
     *     {
    * // PaymentInformation
    * "linhaDigitavel": "74891121150023100718882848251011287560000000177",
    * "codigoBarras": "74892875600000001771121100231007188284825101",
    * "nossoNumero": "211001290",
    * "txId": "445488181811848",
    * "codigoQrCode": "00020126930014br.gov.bcb.pix2571pix-qrcode-h.sicredi.com.br/qr/v2/cobv/528520acdd5f4740b63b9b643ca2bcf99999999999999999999BR5903PIX6006Cidade62070503***630441AC\n\n",


    * "carteira": "SIMPLES", // wallet
    * "seuNumero": "MOCKDDA", // yourNumber
    *
    * // Payee
    * "pagador": {
    *     "codigo": "02LNG",
    *     "documento": "01234567890",
    *     "nome": "PAGADOR DDA"
    * },

    * // Beneficiary
    * "beneficiarioFinal": {
    *     "codigo": "R8N",
    *     "documento": "01234567890",
    *     "nome": "PAGADOR DDA"
    * },


    * "dataEmissao": "2021-08-24", // issueDate
    * "dataVencimento": "2021-09-27", // dueDate
    * "valorNominal": 90,  // amount
    * "situacao": "LIQUIDADO", // status
    *
    * // InterestConfiguration
    * "multa": 0.05,
    * "abatimento": 0,
    * "tipoJuros": "A - VALOR",
    * "juros": 0.01,
    * "diasProtesto": 3,
    * "validadeAposVencimento": 1,
    * "diasNegativacao": 0,
    *
    * // Liquidation
    * "dadosLiquidacao": {
    *     "data": "2021-09-27T03:00:00.000+0000",
    *     "valor": 80,
    *     "multa": 2.67,
    *     "abatimento": 15.99,
    *     "juros": 5.13,
    *     "desconto": 10
    * },
    *
    * // DiscountConfiguration
    * "descontoAntecipacao": 0,
    * "tipoDesconto": "A - VALOR",
    * "descontos": [
    *     {
    * * "numeroOrdem": 1,
    * * "valorDesconto": 10,
    * * "dataLimite": "2021-10-05"
    *     },
    *     {
    * * "numeroOrdem": 2,
    * * "valorDesconto": 5,
    * * "dataLimite": "2021-10-06"
    *     },
    *     {
    * * "numeroOrdem": 3,
    * * "valorDesconto": 3,
    * * "dataLimite": "2021-10-07"
    *     }
    * ]
    * }
     * @param array $boleto
     * @return BoletoDomain
     */
    public static function mapFromQuery($data): BoletoDomain
    {
        $beneficiary = new BeneficiaryDomain(
            $data['beneficiarioFinal']['nome'],
            $data['beneficiarioFinal']['documento'],
            null,
            $data['beneficiarioFinal']['codigo']
        );

        $payee = new PayeeDomain(
            $data['pagador']['nome'],
            $data['pagador']['documento'],
            null,
            $data['pagador']['codigo']
        );

        $issueDate = DateTime::createFromFormat('Y-m-d', $data['dataEmissao']);
        $dueDate = DateTime::createFromFormat('Y-m-d', $data['dataVencimento']);
        $amount = $data['valorNominal'];
        $yourNumber = $data['seuNumero'];
        $ourNumber = $data['nossoNumero'];

        $boleto = new BoletoDomain(
            $beneficiary,
            $payee,
            $amount,
            null,
            null,
            null,
            $yourNumber,
            $dueDate,
            $issueDate,
            $ourNumber
        );

        $boleto->setWallet($data['carteira']);
        $boleto->setStatus($data['situacao']);

        if (!empty($data['descontos'])) {
            $discounts = [];

            // sort array by numeroOrdem
            usort($data['descontos'], function ($a, $b) {
                return $a['numeroOrdem'] <=> $b['numeroOrdem'];
            });

            foreach ($data['descontos'] as $discount) {
                $discounts[] = new \SicrediAPI\Domain\Discount(
                    $discount['valorDesconto'],
                    \DateTime::createFromFormat('Y-m-d', $discount['dataLimite'])
                );
            }

            $discountConfiguration = new \SicrediAPI\Domain\DiscountConfiguration(
                $discounts,
                $data['tipoDesconto'] == 'VALOR' ? DiscountConfigurationDomain::TYPE_VALUE : DiscountConfigurationDomain::TYPE_PERCENTAGE,
                $data['descontoAntecipacao'],
            );

            $boleto->setDiscounts($discountConfiguration);
        }

        if (!empty($data['juros'])) {
            $interests = new InterestConfigurationDomain(
                $data['tipoJuros'] == 'VALOR' ? InterestConfigurationDomain::TYPE_VALUE : InterestConfigurationDomain::TYPE_PERCENTAGE,
                $data['juros'],
                $data['multa'],
                $data['diasProtesto'],
                $data['diasNegativacao'],
                $data['validadeAposVencimento']
            );

            $boleto->setInterests($interests);
        }

        $paymentInformation = PaymentInformation::fromArray($data);
        $boleto->setPaymentInformation($paymentInformation);

        if (!empty($data['dadosLiquidacao'])) {
            $boleto->setLiquidation(LiquidationDomain::fromArray($data['dadosLiquidacao']));
        }

        return $boleto;
    }

    /**
     * @param array $data
     * @return BoletoDomain
     */
    public static function mapFromQueryDailyLiquidations(array $data)
    {
        if (empty($data['items'])) {
            return [];
        }

        $liquidations = [];

        foreach ($data['items'] as $item) {
            // dataPagamento = 2021-09-01 07:23:28.7
            $date = DateTime::createFromFormat('Y-m-d H:i:s.u', $item['dataPagamento']);
            $liquidation = new LiquidationDomain(
                $date,
                (float) $item['valorLiquidado'],
                (float) $item['multaLiquida'],
                (float) $item['abatimentoLiquido'],
                (float) $item['jurosLiquido'],
                (float) $item['descontoLiquido'],
                (string) $item['nossoNumero'],
                (string) $item['seuNumero'],
                (float) $item['valor'],
                (string) $item['tipoLiquidacao']
            );

            $liquidations[] = $liquidation;
        }

        return $liquidations;

    }
}
