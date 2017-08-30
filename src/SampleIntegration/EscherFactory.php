<?php

namespace SampleIntegration;


use Escher\Escher;

class EscherFactory
{

    public static function createForSuite()
    {
        return self::createWithCredentialScope('eu/suite/ems_request');
    }


    public static function createForSessionValidator()
    {
        return self::createWithCredentialScope('eu/session-validator/ems_request');
    }


    private static function createWithCredentialScope($credentialScope)
    {
        $escher = Escher::create($credentialScope);
        $escher->setVendorKey('EMS');
        $escher->setAlgoPrefix('EMS');
        $escher->setAuthHeaderKey('X-EMS-Auth');
        $escher->setDateHeaderKey('X-EMS-Date');

        return $escher;
    }

}
