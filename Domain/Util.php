<?php

namespace Domain;

use Domain\Entities\StepMania\ISimfile;
use Domain\Entities\StepMania\IPack;

class Util
{
    private static $_countryCodes =
        array (
            'AF' => 'Afghanistan',
            'AX' => 'Åland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Zaire',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Côte D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and Mcdonald Islands',
            'VA' => 'Vatican City State',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'KENYA',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic People\'s Republic of',
            'KR' => 'Korea, Republic of',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia, the Former Yugoslav Republic of',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States of',
            'MD' => 'Moldova, Republic of',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan, Province of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Minor Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );
    
    public static function countryNameToCode($name)
    {
        return array_search($name, self::$_countryCodes);
    }
    
    public static function countryNameFromLatLong($lat, $long)
    {
        $url = 'http://maps.google.com/maps/api/geocode/json?address=' . $lat .',' . $long .'&sensor=false'; 
        $get     = file_get_contents($url);
        $geoData = json_decode($get);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid geocoding results');
        }

        if(isset($geoData->results[0])) {
            foreach($geoData->results[0]->address_components as $addressComponent) {
                if(in_array('country', $addressComponent->types)) {
                    return $addressComponent->long_name; 
                }
            }
        }
        return null; 
    }
    
    public static function bytesToHumanReadable($bytes, $dec = 2)
    {
        if(!is_int($bytes)) throw new \Exception('Bytes must be an int.' . var_dump($bytes));
     
        //Shamelessly stolen from stackoverflow: http://stackoverflow.com/questions/15188033/human-readable-file-size
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1000, $factor)) . @$size[$factor];
    }

    public static function packToArray(IPack $pack)
    {
        return array(
            'title'=> $pack->getTitle(),
            'contributors' => $pack->getContributors(),
            'simfiles' => self::getPackSimfilesArray($pack),
            'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
            'mirrors' => self::getPackMirrorsArray($pack),
            'size' => $pack->getFile() ? self::bytesToHumanReadable($pack->getFile()->getSize()) : null,
            'uploaded' => $pack->getFile() ? date('F jS, Y', $pack->getFile()->getUploadDate()) : null
        );
    }
    
    public static function getPackMirrorsArray(IPack $pack)
    {
        $packMirrors = array();

        if($pack->getFile())
        {
            $packMirrors[] = array('source' => 'DivinElegy', 'uri' => 'files/pack/' . $pack->getFile()->getHash());
        }

        if($pack->getFile()->getMirrors())
        {
            foreach($pack->getFile()->getMirrors() as $mirror)
            {
                $packMirrors[] = array('source' => $mirror->getSource(), 'uri' => $mirror->getUri());
            }
        }
        
        return $packMirrors;
    }
    
    public static function getPackSimfilesArray(IPack $pack)
    {
        $packSimfiles = array();
        foreach($pack->getSimfiles() as $simfile)
        {
            $packSimfiles[] = self::simfileToArray($simfile);
        }
        
        return $packSimfiles;
    }
    
    public static function simfileToArray(Entities\StepMania\ISimfile $simfile)
    {
        $singleSteps = array();
        $doubleSteps = array();

        foreach($simfile->getSteps() as $steps)
        {   
            $stepDetails = array(
                'artist' => $steps->getArtist()->getTag(),
                'difficulty' => $steps->getDifficulty()->getITGName(),
                'rating' => $steps->getRating()
            );

            if($steps->getMode()->getPrettyName() == 'Single')
            {
                $singleSteps[] = $stepDetails;
            } else {
                $doubleSteps[] = $stepDetails;
            }
        }

        return array(            
            'title' => $simfile->getTitle(),
            'artist' => $simfile->getArtist()->getName(),
            'steps' => array(
                'single' => $singleSteps,
                'double' => $doubleSteps
            ),
            'bgChanges' => $simfile->hasBgChanges() ? 'Yes' : 'No',
            'fgChanges' => $simfile->hasFgChanges() ? 'Yes' : 'No',
            'bpmChanges' => $simfile->hasBPMChanges() ? 'Yes' : 'No',
            'banner' => $simfile->getBanner() ? 'files/banner/' . $simfile->getBanner()->getHash() : 'files/banner/default',
            'download' => $simfile->getSimfile() ?  'files/simfile/' . $simfile->getSimfile()->getHash() : null,
            'size' => $simfile->getSimfile() ? Util::bytesToHumanReadable($simfile->getSimfile()->getSize()) : null,
            'uploaded' => $simfile->getSimfile() ? date('F jS, Y', $simfile->getSimfile()->getUploadDate()) : null
        );
    }
}