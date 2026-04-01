<?php

namespace TvDetector;

/**
 * Lightweight TV user-agent detector.
 *
 * Extracted from matomo/device-detector regexes/device/televisions.yml
 * and regexes/device/shell_tv.yml — brand-level regexes only, model
 * detection stripped out.
 *
 * Uses a single preg_match() instead of the full DeviceDetector pipeline
 * (no YAML parsing, no object instantiation, no multi-pass processing).
 */
class TvDetector
{
    /**
     * Combined PCRE pattern. All alternations are case-insensitive.
     * Patterns are ordered: fast universal checks first, brand patterns second.
     */
    private const PATTERN = '/(?:HbbTV|SmartTvA)\/[1-9]'           // televisions.yml universal
        . '|SmartTV'
        . '|\w+[ _]shell[ _]\w'                                     // shell_tv.yml universal
        . '|TCL\/(?:TCL|RCA|THOM)-'                                 // shell_tv.yml TCL variants
        . '|Airties'
        . '|ALDINORD[;,)]'
        . '|ALDISUED[;,)]'
        . '|Altech UEC'
        . '|ALTIMO[;,]'
        . '|Altus[;,)]'
        . '|Amazon.+AMZ'
        . '|ANDERSSON[;,)]'
        . '|ARRIS[;,)]'
        . '|ATLANTIC[;,)]'
        . '|ATVIO'
        . '|AWOX[;,)]'
        . '|AYA[;,)]'
        . '|Bangolufsen'
        . '|Beko[;,)]'
        . '|Blaupunkt_UMC[;,)]'
        . '|Botech[;,)]'
        . '|BUSH[;,)]'
        . '|CECOTEC[;,)]'
        . '|CELCUS[;,)]'
        . '|Changhong'
        . '|CLAYTON[;,)]'
        . '|CONTINENTAL_EDI[;,)]'
        . '|coocaa[;,)]'
        . '|CreNova'
        . '|CROWN[;,)]'
        . '|Cultraview690[;,]'
        . '|Daewoo[;,)]'
        . '|DIGIHOME[;,)]'
        . '|DIKOM[;,)]'
        . '|DIORA[;,)]'
        . '|DYON[;,)]'
        . '|EAS_ELECTRIC[;,)]'
        . '|EDENWOOD[;,)]'
        . '|EGL[;,)]'
        . '|ELEKTROLAND[;,)]'
        . '|ELECTRONIA[;,)]'
        . '|ELIT[;,)]'
        . '|ENDURO[;,)]'
        . '|ESSENTIELB[;,)]'
        . '|Expressluck[;,)]'
        . '|FINIX[;,)]'
        . '|FINLUX[;,)]'
        . '|FITCO, [a-z0-9_ -]+, (?:wired|wireless)'
        . '|FU[;,)]'
        . '|FUEGO[;,)]'
        . '|FUJICOM[;,)]'
        . '|FUNAI[;,)]'
        . '|GN_ELECTRONICS[;,)]'
        . '|GOGEN[;,)]'
        . '|GRAETZ[;,)]'
        . '|OWB'
        . '|(?:Amazon.+)?Grundig'
        . '|(?:HHW_)?HAIER'
        . '|HANSEATIC[;,)]'
        . '|HI-LEVEL[;,)]'
        . '|HIGH_ONE[;,)]'
        . '|Hisense'
        . '|Eurofins_Digital_Testing'
        . '|Hitachi[;,)]'
        . '|HOFER[;,)]'
        . '|HORIZON[;,)]'
        . '|HOTEL[;,)]'
        . '|Humax'
        . '|hdr1000s'
        . '|HUMELAB[;,)]'
        . '|HYUNDAI[;,)]'
        . '|Ikea'
        . '|Intek'
        . '|Inverto'
        . '|INNOHIT[;,)]'
        . '|AFTSO001'
        . '|JVC[;,)]'
        . '|KALLEY[;,)]'
        . '|KENDO[;,)]'
        . '|KUBO[;,)]'
        . '|KYDOS[;,)]'
        . '|LAURUS[;,)]'
        . '|LENCO[;,)]'
        . '|LGE ?;'
        . '|43UN68006LA'
        . '|LGE\/SQY\/RCA'
        . '|Lifemaxx[;,)]'
        . '|LINSAR[;,)]'
        . '|Loewe'
        . '|DIXONS-LOGIK[;,)]'
        . '|LUXOR[;,)]'
        . '|Manhattan'
        . '|Medion'
        . '|MEGA_VISION[;,)]'
        . '|MIIA[;,)]'
        . '|MIRAY'
        . '|MITCHELL_BROWN[;,)]'
        . '|Metz'
        . '|MStar[;,)]'
        . '|MTC[;,)]'
        . '|MYROS[;,)]'
        . '|NABO[;,)]'
        . '|NAVON[;,)]'
        . '|NEO, [a-z0-9_ -]+, (?:wired|wireless)'
        . '|NEXON[;,)]'
        . '|NEXT[;,)]'
        . '|NILAIT[;,)]'
        . '|NOKIA[;,)]'
        . '|NORDMENDE[;,)]'
        . '|NORMANDE[;,)]'
        . '|OCEANIC[;,)]'
        . '|OK[;,)]'
        . '|ONVO[;,)]'
        . '|ORAVA[;,)]'
        . '|Panasonic'
        . '|PEAQ'
        . '|(?:Philips|TPVision)'
        . '|NETTV\/'
        . '|Philips.+[0-9]{2}H[FHOU][HKLST][0-9]{4}'
        . '|POLAROID[;,)]'
        . '|PREMIER[;,)]'
        . '|PROFILO[;,)]'
        . '|PROSONIC[;,)]'
        . '|PREO[;,)]'
        . '|QILIVE[;,)]'
        . '|RCA;'
        . '|REGAL[;,)]'
        . '|Saba[;,)]'
        . '|Salora(?:_cx)?[;,)]'
        . '|Samsung(?!.*Mobile)'
        . '|Maple_2011'
        . '|SCHAUB_LORENZ[;,)]'
        . '|SCHONTECH[;,)]'
        . '|SCBC[;,)]'
        . '|_TV_[A-Z0-9_]+_TCL_SCBC'
        . '|SEG[;,)]'
        . '|SEHMAX[;,)]'
        . '|Selevision'
        . '|(?:UMC-)?Sharp'
        . '|SILVA_SCHNEIDER[;,)]'
        . '|SIMFER[;,)]'
        . '|Sky_?worth'
        . '|SKW690'
        . '|SWTV[;,)]'
        . '|SKYTECH[;,)]'
        . '|Smart[;,)]'
        . '|SMARTTECH[;,)]'
        . '|SCHNEIDER[;,)]'
        . '|SOLAS[;,]'
        . '|SN55FMN243-0246'
        . '|Sony'
        . '|SSMART[;,)]'
        . '|STAR LIGHT[;,)]'
        . '|STILEVS[;,)]'
        . '|SULPICE_TV[;,)]'
        . '|SUNGATE[;,)]'
        . '|SUNNY[;,)]'
        . '|TALBERG[;,)]'
        . '|TDSystems[;,)]'
        . '|TAUBE[;,)]'
        . '|Technicolor'
        . '|TECHNIKA[;,)]'
        . '|TechniSat'
        . '|TechnoTrend'
        . '|Techwood[;,)]'
        . '|Telefunken'
        . '|(?:Amazon.+)?TCL'
        . '|THOMSON[,]?'
        . '|TOKYO[;,)]'
        . '|Toptech690[;,]'
        . '|(?:Amazon.+)?Toshiba'
        . '|THTF_CVTE[;,)]'
        . '|TUCSON[;,)]'
        . '|TURBO-?X[;,)]'
        . '|UNITED[;,)]'
        . '|VANGUARD[;,)]'
        . '|videoweb'
        . '|tv2n'
        . '|VISITECH[;,)]'
        . '|(?:Vestel.+VESTEL|(?:BBC_CUSTOMERS|VESTEL)[;,)])'
        . '|VOX[;,)]'
        . '|VORTEX[;,)]'
        . '|WALKER[;,)]'
        . '|WALTHAM[;,)]'
        . '|WeByLoewe[;,)]'
        . '|WELLINGTON[;,)]'
        . '|WESTON[;,)]'
        . '|WONDER[;,)]'
        . '|X-VISION[;,)]'
        . '|XGEM[;,)]'
        . '|Amazon.+Xiaomi'
        . '|ALLSTAR[;,)]'
        . '|AXEN[;,)]'
        . '|Arcelik'
        . '|ZIMMER[;,)]'
        . '|DIJITSU[;,)]'
        . '|FOX[;,)]'
        . '|ONVO[;,)]'
        . '|LGE\/[^\/]+\/[^\/]+;'
        . '|OEM, [a-z0-9_ -]+, (?:wired|wireless)'
        . '|_TV_[A-Z0-9]+_[0-9]{4};'
        . '|LaTivu_[\d.]+_[0-9]{4}'
        . '/i';

    private const ANDROID_TV_PATTERN = '/Linux; Andr0id[; ](\d+[.\d]*)|Android[; ](\d+[.\d]*).+(?:(?:Android(?: UHD)?|AT&T|Google|Smart)[ _]?TV|AOSP on r33a0|BRAVIA|wv-atv)|Windows.+Andr0id TV|.+(?:K_?Android_?TV_|AndroidTV|GoogleTV_)/i';
    private const FIRE_OS_PATTERN = '/(?:Andr[o0]id (\d([\d.])*);? |Amazon;|smarttv_)AFT|AEO[ACBHKT]| KF[ADFGJKMORSTQ]|.+FIRETVSTICK2018/i';

    public static function isTV(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match(self::PATTERN, $userAgent);
    }

    public static function isAndroidTV(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match(self::ANDROID_TV_PATTERN, $userAgent);
    }

    public static function isFireOS(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match(self::FIRE_OS_PATTERN, $userAgent);
    }

}
