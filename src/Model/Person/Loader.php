<?php
declare(strict_types=1);

namespace App\Model\Person;

use App\Model\Person;

class Loader
{
    /**
     * @var Person
     */
    private $person;

    public function __construct()
    {
        $this->person = new Person();
    }

    public function load(string $data): Person
    {
        $data = json_decode($data, true);
        $person = $this->person;
        if (array_key_exists('fiscalcode', $data)) {
            $fiscalCode = strtoupper(trim($data['fiscalcode']));
            $person->setFiscalCode($this->escapeFiscalCodeFromOmocodie($fiscalCode));
            $person->setNotEscapedFiscalCode($fiscalCode);
        }
        if (array_key_exists('firstname', $data)) {
            $person->setFirstName(strtoupper(trim($data['firstname'])));
        }
        if (array_key_exists('lastname', $data)) {
            $person->setLastName(strtoupper(trim($data['lastname'])));
        }
        if (array_key_exists('birthdate', $data)) {
            $person->setBirthDate(strtoupper(trim($data['birthdate'])));
        }
        if (array_key_exists('ismale', $data)) {
            $person->setIsMale((bool)$data['ismale']);
        }
        if (array_key_exists('municipality', $data)) {
            $person->setMunicipality(strtoupper(trim($data['municipality'])));
        }
        return $person;
    }

    /**
     * @param string $fiscalCode
     * @return string
     */
    private function escapeFiscalCodeFromOmocodie(string $fiscalCode): string
    {
        //see this link for reference
        //https://quifinanza.it/tasse/codice-fiscale-come-si-calcola-e-come-si-corregge-in-caso-di-omocodia/1708/
        $map = [
            'L' => 0,
            'M' => 1,
            'N' => 2,
            'P' => 3,
            'Q' => 4,
            'R' => 5,
            'S' => 6,
            'T' => 7,
            'U' => 8,
            'V' => 9,
        ];
        return preg_replace_callback(
            '/[LMNP-V]/',
            function ($char) use ($map) {
                return $map[$char[0]];
            },
            $fiscalCode
        );
    }
}
