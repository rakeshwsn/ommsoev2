<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * User Agents configuration file.
 *
 * This file contains arrays of user agent data for platforms, browsers, mobiles,
 * and robots. It is used by the User Agent Class to help identify browser,
 * platform, robot, and mobile device data.
 */
class UserAgents extends BaseConfig
{
    /**
     * Platforms array.
     *
     * @var array<string, string>
     */
    public array $platforms = [
        'aix' => 'AIX',
        'android' => 'Android',
        'apachebench' => 'ApacheBench',
        'blackberry' => 'BlackBerry',
        'bsdi' => 'BSDi',
        'cocoon' => 'O2 Cocoon',
        'debian' => 'Debian',
        'digital paths' => 'Digital Paths',
        'edge' => 'Edge',
        'edg' => 'Edge',
        'ericsson' => 'Ericsson',
        'freebsd' => 'FreeBSD',
        'gnu' => 'GNU/Linux',
        'hiptop' => 'Danger Hiptop',
        'hp-ux' => 'HP-UX',
        'ibrowse' => 'IBrowse',
        'ie' => 'Internet Explorer',
        'ipad' => 'iPad',
        'iphone' => 'Apple iPhone',
        'ipod' => 'Apple iPod Touch',
        'irix' => 'Irix',
        'j2me' => 'Generic Mobile',
        'kde' => 'KDE',
        'konqueror' => 'Konqueror',
        'lg' => 'LG',
        'linux' => 'Linux',
        'lynx' => 'Lynx',
        'mac' => 'Mac OS',
        'maxthon' => 'Maxthon',
        'midp' => 'Generic Mobile',
        'mmp' => 'Generic Mobile',
        'mobi' => 'Generic Mobile',
        'mot-' => 'Motorola',
        'motorola' => 'Motorola',
        'netbsd' => 'NetBSD',
        'netfront' => 'Netfront Browser',
        'nec-' => 'NEC',
        'nintendo 3ds' => 'Nintendo 3DS',
        'nintendo dsi' => 'Nintendo DSi',
        'nintendo ds' => 'Nintendo DS',
        'nintendo wii' => 'Nintendo Wii',
        'obigo' => 'Obigo',
        'opera' => 'Opera',
        'opera mini' => 'Opera Mini',
        'opera mobi' => 'Opera Mobile',
        'open web' => 'OpenWeb',
        'openweb' => 'OpenWeb',
        'openwave' => 'Openwave Browser',
        'os x' => 'Mac OS X',
        'palm' => 'Palm',
        'palmscape' => 'Palmscape',
        'playstation 3' => 'PlayStation 3',
        'playstation portable' => 'PlayStation Portable',
        'playstation vita' => 'PlayStation Vita',
        'ppc' => 'Macintosh',
        'ppc mac' => 'Power PC Mac',
        'psp' => 'PlayStation Portable',
        'sagem' => 'Sagem',
        'sanyo' => 'Sanyo',
        'series60' => 'Symbian S60',
        'sharp' => 'Sharp',
        'sie-' => 'Siemens',
        'sony' => 'Sony Ericsson',
        'symbian' => 'SymbianOS',
        'symbianos' => 'SymbianOS',
        'up.browser' => 'Generic Mobile',
        'up.link' => 'Generic Mobile',
        'vario' => 'Vario',
        'voda' => 'Vodafone',
        'windows' => 'Unknown Windows OS',
        'windows ce' => 'Windows CE',
        'windows nt 4.0' => 'Windows NT 4.0',
        'windows nt 5.0' => 'Windows 2000',
        'windows nt 5.1' => 'Windows XP',
        'windows nt 5.2' => 'Windows 2003',
        'windows nt 6.0' => 'Windows Vista',
        'windows nt 6.1' => 'Windows 7',
        'windows nt 6.2' => 'Windows 8',
        'windows nt 6.3' => 'Windows 8.1',
        'windows nt 10.0' => 'Windows 10',
        'wml' => 'Generic Mobile',
        'xda' => 'XDA',
        'xhtml' => 'Generic Mobile',
        'yandex' => 'YandexBot',
    ];

    /**
     * Browsers array.
     *
     * @var array<string, string>
     */
    public array $browsers = [
        'amaya' => 'Amaya',
        'android' => 'Android',
        'blackberry' => 'BlackBerry',
        'camino' => 'Camino',
        'chrome' => 'Chrome',
        'chromium' => 'Chromium',
        'edge' => 'Edge',
        'edg' => 'Edge',
        'elaine' => 'Palm',
        'epiphany' => 'Epiphany',
        'firebird' => 'Firebird',
        'fire
