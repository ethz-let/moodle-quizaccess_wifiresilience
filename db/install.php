<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Post install script for the quizaccess_wifiresilience plugin.
 *
 * @package   quizaccess_wifiresilience
 * @copyright 2017 ETH Zurich (amr.hourani@let.ethz.ch)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post install hook implementation for the quizaccess_wifiresilience plugin.
 */
function xmldb_quizaccess_wifiresilience_install() {
    global $OUTPUT;
    // If OpenSSL is available, generate a public/private key pair.
    if (function_exists('openssl_pkey_new')) {
        $privatekey = openssl_pkey_new(array('digest_alg' => 'sha512', 'private_key_bits' => 1024,
                'private_key_type' => OPENSSL_KEYTYPE_RSA));
        if ($privatekey) {
            openssl_pkey_export($privatekey, $privatekeystring);
            $publickeydata = openssl_pkey_get_details($privatekey);
            openssl_pkey_free($privatekey);
            set_config('privatekey', $privatekeystring, 'quizaccess_wifiresilience');
            set_config('publickey', $publickeydata['key'], 'quizaccess_wifiresilience');
        } else {
            echo $OUTPUT->notification('Failed to generate an public/private key pair.');
            while ($message = openssl_error_string()) {
                echo $OUTPUT->notification($message);
            }
        }
    }
}
